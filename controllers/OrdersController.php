<?php

namespace app\controllers;


use app\commons\exceptions\LogicException;
use app\commons\helpers\AutoOrder;
use app\commons\helpers\FreeTimeCalculate;
use app\commons\helpers\TimeHelper;
use app\commons\notification\NotifyHelper;
use app\models\ar\car\CarBrands;
use app\models\ar\car\CarModels;
use app\models\ar\carwash\CarwashSales;
use app\models\ar\carwash\CarwashSettings;
use app\models\ar\complex\Complexes;
use app\models\ar\order\Orders;
use app\models\ar\order\OrderService;
use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalLog;
use app\models\ar\personal\PersonalNotification;
use app\models\ar\service\Services;
use Cassandra\Time;
use yii\base\BaseObject;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

/**
 * Class OrdersController
 * @package app\controllers
 */
class OrdersController extends Controller
{
    public $layout = 'app/main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['auto-create', 'create', 'edit', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['auto-create', 'create', 'edit', 'delete'],
                        'roles' => ['perm_create_edit_orders'],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ]
                ],
            ],
        ];
    }

    /**
     * Страница создания заказа
     */
    public function actionCreate($date = false, $startTime = false, $post = false, $endTime = false)
    {
        $cwId = \Yii::$app->user->identity->getCWid();
        $carwashSettings = CarwashSettings::findOne(['carwash_id' => $cwId]);
        if (!$carwashSettings) {
            throw new LogicException('Не найдены базовые настройки автомойки');
        }
        $personalList = Personal::find()->where(['carwash_id' => $cwId])->all();

        $orderModel = new Orders();
        $orderModel->carwash_id = $cwId;
        $orderModel->status = Orders::STATUS_NEW_FROM_WASH;
        if ($date) {
            $date = date('Y-m-d', strtotime($date));
            if (TimeHelper::isValidDate($date)) {
                $orderModel->date = $date;
            }
        }
        if ($startTime) {
            $orderModel->start_time = $startTime;
        }
        if ($endTime) {
            $orderModel->end_time = $endTime;
        }
        if ($post && $post <= $carwashSettings->post_count) {
            $orderModel->post = $post;
        }

        if ($orderModel->load(\Yii::$app->request->post())) {
            $orderPostData = \Yii::$app->request->post('Orders');
            $orderModel->start_time = TimeHelper::convertTimeToMin($orderPostData['start_time']);
            $orderModel->end_time = TimeHelper::convertTimeToMin($orderPostData['end_time']);
            if ($orderModel->start_time == $orderModel->end_time) {
                throw new LogicException('Время начала и окончания записи не может быть идентичным');
            }
            $timeChecker = new FreeTimeCalculate($cwId);
            $isIntersection = $timeChecker->checkIntersectionsOrdersByPost($orderModel->date, $orderModel->start_time, $orderModel->end_time, $orderModel->post);
            if ($isIntersection) {
                \Yii::$app->session->setFlash('warning', 'Выбранное время на посте №' . $orderModel->post . ' уже занято. Измените настройки времени');
            }

            if (!$isIntersection && $orderModel->save()) {
                $this->saveSelectedServices($orderModel->id, $orderModel->car_type);
                \Yii::$app->session->setFlash('success', 'Запись успешно добавлена');
                $notify = new NotifyHelper();
                $notify->trigger(PersonalNotification::EVENT_NEW_ORDER, ['orderId' => $orderModel->id]);
                return $this->redirect('/');
            }
        }
        if (!empty($orderModel->errors)) {
            var_dump($orderModel->errors);
        }
        $washComplexes = Complexes::find()->where(['carwash_id' => $cwId, 'is_detailing' => false])->orderBy('name ASC')->all();
        $detailingComplexes = Complexes::find()->where(['carwash_id' => $cwId, 'is_detailing' => true])->orderBy('name ASC')->all();
        $washServices = Services::find()->where(['carwash_id' => $cwId, 'is_detailing' => false])->orderBy('name ASC')->all();
        $detailingServices = Services::find()->where(['carwash_id' => $cwId, 'is_detailing' => true])->orderBy('name ASC')->all();

        $carBrands = CarBrands::find()->where(['carwash_id' => null])->orWhere(['carwash_id' => $cwId])->all();
        $carModels = CarModels::find()->where(['car_brand_id' => $carBrands[0]->id])->all();

        return $this->render('create', [
            'carwashId' => $cwId,
            'postCounts' => $carwashSettings->post_count,
            'model' => $orderModel,
            'carBrands' => $carBrands,
            'carModels' => $carModels,
            'personalList' => $personalList,
            'washComplexes' => $washComplexes,
            'detailingComplexes' => $detailingComplexes,
            'washServices' => $washServices,
            'detailingServices' => $detailingServices,

            'selectedWashComplexes' => [],
            'selectedDetailingComplexes' => [],
            'selectedWashServices' => [],
            'selectedDetailingServices' => [],
        ]);
    }

    public function actionAutoCreate()
    {
        $cwId = \Yii::$app->user->identity->getCWid();
        $carwashSettings = CarwashSettings::findOne(['carwash_id' => $cwId]);
        $personalList = Personal::find()->where(['carwash_id' => $cwId])->all();

        $orderModel = new Orders();
        $orderModel->carwash_id = $cwId;
        $orderModel->status = Orders::STATUS_NEW_FROM_WASH;
        $orderModel->date = date('Y-m-d');

        if ($orderModel->load(\Yii::$app->request->post())) {
            $orderPostData = \Yii::$app->request->post('Orders');
            $workTime = 5;
            if ($orderPostData['work_time']) {
                $workTime = $orderPostData['work_time'];
            }
            $orderModel->start_time = TimeHelper::convertTimeToMin($orderPostData['start_time']);
            $autoOrder = new AutoOrder($cwId, $orderModel->date, $orderPostData['start_time'], $workTime);
            $orderModel->post = $autoOrder->findOptimalPost();

            if ($orderModel->post === false) {
                \Yii::$app->session->setFlash('danger', 'Нет возможности записать на данный промежуток времени. Все посты в это время заняты');
                $orderModel->post = null;
            }
            // Время окончания рассчитывается автоматически, исходя из времени потраченного на работу
            $orderModel->end_time = TimeHelper::convertTimeToMin($autoOrder->getEndTime());
            if (isset($orderModel->personal_id)) {
                $personal = Personal::findOne($orderModel->personal_id);
                if ($personal) {
                    $orderModel->personal_fullname = $personal->getShortUsername();
                }
            }
            if (isset($orderModel->post) && $orderModel->save()) {
                \Yii::$app->session->setFlash('success', 'Запись успешно добавлена');
                $notify = new NotifyHelper();
                $notify->trigger(PersonalNotification::EVENT_NEW_ORDER, ['orderId' => $orderModel->id]);
                $this->saveSelectedServices($orderModel->id, $orderModel->car_type);
                return $this->redirect('/');
            }
        }
        if (!empty($orderModel->errors)) {
            var_dump($orderModel->errors);
            die();
        }
        $washComplexes = Complexes::find()->where(['carwash_id' => $cwId, 'is_detailing' => false])->orderBy('name ASC')->all();
        $detailingComplexes = Complexes::find()->where(['carwash_id' => $cwId, 'is_detailing' => true])->orderBy('name ASC')->all();
        $washServices = Services::find()->where(['carwash_id' => $cwId, 'is_detailing' => false])->orderBy('name ASC')->all();
        $detailingServices = Services::find()->where(['carwash_id' => $cwId, 'is_detailing' => true])->orderBy('name ASC')->all();

        return $this->render('auto-create', [
            'carwashId' => $cwId,
            'postCounts' => $carwashSettings->post_count,
            'model' => $orderModel,

            'personalList' => $personalList,
            'washComplexes' => $washComplexes,
            'detailingComplexes' => $detailingComplexes,
            'washServices' => $washServices,
            'detailingServices' => $detailingServices,

            'selectedWashComplexes' => [],
            'selectedDetailingComplexes' => [],
            'selectedWashServices' => [],
            'selectedDetailingServices' => [],
        ]);
    }

    public function actionInfo()
    {
        $cwId = \Yii::$app->user->identity->getCWid();
        $carwashSettings = CarwashSettings::findOne(['carwash_id' => $cwId]);
        $personalList = Personal::find()->where(['carwash_id' => $cwId])->all();

        $orderModel = new Orders();
        $orderModel->carwash_id = $cwId;

        $washComplexes = Complexes::find()->where(['carwash_id' => $cwId, 'is_detailing' => false])->orderBy('name ASC')->all();
        $detailingComplexes = Complexes::find()->where(['carwash_id' => $cwId, 'is_detailing' => true])->orderBy('name ASC')->all();
        $washServices = Services::find()->where(['carwash_id' => $cwId, 'is_detailing' => false])->orderBy('name ASC')->all();
        $detailingServices = Services::find()->where(['carwash_id' => $cwId, 'is_detailing' => true])->orderBy('name ASC')->all();
        $activeSales = CarwashSales::find()->where(['carwash_id' => $cwId])->andWhere(['<=', 'start_at', date('Y-m-d')])->andWhere(['>=', 'end_at', date('Y-m-d')])->orderBy('position ASC')->all();


        return $this->render('info', [
            'carwashId' => $cwId,
            'postCounts' => $carwashSettings->post_count,
            'model' => $orderModel,

            'personalList' => $personalList,
            'activeSales' => $activeSales,
            'washComplexes' => $washComplexes,
            'detailingComplexes' => $detailingComplexes,
            'washServices' => $washServices,
            'detailingServices' => $detailingServices,

            'selectedWashComplexes' => [],
            'selectedDetailingComplexes' => [],
            'selectedWashServices' => [],
            'selectedDetailingServices' => [],
        ]);
    }

    public function actionDelete($orderId)
    {
        $pId = \Yii::$app->user->identity->getPId();
        $personal = Personal::findOne($pId);
        $order = Orders::findOne($orderId);
        if (!$personal || !$order) {
            throw new LogicException('Не найдены базовые данные заказа');
        }
        if ($order->carwash_id != $personal->carwash_id) {
            throw new ForbiddenHttpException('Нет  доступа к данному заказу');
        }
        $services = OrderService::deleteAll(['order_id' => $orderId]);
        $notify = new NotifyHelper();
        $notify->trigger(PersonalNotification::EVENT_CANCEL_ORDER, ['orderId' => $orderId, 'initiatorPersonalId' => $pId, 'isPersonalDelete' => true]);

        $order->delete();
        try {
            $log = new PersonalLog();
            $log->createLog($personal->id, '', PersonalLog::DELETE_ORDER, 'Удален заказ #' . $orderId);


        } catch (\Exception $exception) {
        }
    }

    public function actionEdit($orderId)
    {
        $cwId = \Yii::$app->user->identity->getCWid();
        $carwashSettings = CarwashSettings::findOne(['carwash_id' => $cwId]);
        $personalList = Personal::find()->where(['carwash_id' => $cwId])->all();

        $orderModel = Orders::findOne($orderId);
        if (!$orderModel) {
            throw new NotFoundHttpException('Заказ не найден');
        }
        if ($orderModel->carwash_id != $cwId) {
            throw new ForbiddenHttpException('Нет доступа к данному заказу');
        }
        if (in_array($orderModel->status, [Orders::STATUS_REMOVED, Orders::STATUS_ARCHIVE])) {
            return $this->redirect(['/orders/show', 'id' => $orderId]);
        }

        if ($orderModel->load(\Yii::$app->request->post())) {
            $orderPostData = \Yii::$app->request->post('Orders');
            $orderModel->start_time = TimeHelper::convertTimeToMin($orderPostData['start_time']);
            $orderModel->end_time = TimeHelper::convertTimeToMin($orderPostData['end_time']);
            if ($orderModel->start_time == $orderModel->end_time) {
                throw new LogicException('Время начала и окончания записи не может быть идентичным');
            }
            $timeChecker = new FreeTimeCalculate($cwId);
            $isIntersection = $timeChecker->checkIntersectionsOrdersByPost($orderModel->date, $orderModel->start_time, $orderModel->end_time, $orderModel->post, $orderId);
            if ($isIntersection) {
                \Yii::$app->session->setFlash('warning', 'Выбранное время на посте №' . $orderModel->post . ' уже занято. Измените настройки времени');
            }
            if (!$isIntersection && $orderModel->save()) {
                // Удаляем старые, так как перезаписываем
                OrderService::deleteAll(['order_id' => $orderId]);
                $this->saveSelectedServices($orderModel->id, $orderModel->car_type);
                \Yii::$app->session->setFlash('success', 'Заказ обновлен');
            } else {
                \Yii::$app->session->setFlash('danger', 'Ошибка при обновлении заказа');
            }
        }
        if (!empty($orderModel->errors)) {
            var_dump($orderModel->errors);
        }
        $washComplexes = Complexes::find()->where(['carwash_id' => $cwId, 'is_detailing' => false])->orderBy('name ASC')->all();
        $detailingComplexes = Complexes::find()->where(['carwash_id' => $cwId, 'is_detailing' => true])->orderBy('name ASC')->all();
        $washServices = Services::find()->where(['carwash_id' => $cwId, 'is_detailing' => false])->orderBy('name ASC')->all();
        $detailingServices = Services::find()->where(['carwash_id' => $cwId, 'is_detailing' => true])->orderBy('name ASC')->all();

        $carBrands = CarBrands::find()->where(['carwash_id' => null])->orWhere(['carwash_id' => $cwId])->all();
        $carModels = CarModels::find()->where(['car_brand_id' => $carBrands[0]->id])->all();

        $selectedWashComplexes = OrderService::find()->select('entity_id')
            ->where(['order_id' => $orderId, 'is_detailing' => false, 'type' => OrderService::TYPE_COMPLEX])->column();
        $selectedDetailingComplexes = OrderService::find()->select('entity_id')
            ->where(['order_id' => $orderId, 'is_detailing' => true, 'type' => OrderService::TYPE_COMPLEX])->column();
        $selectedWashServices = OrderService::find()->select('entity_id')
            ->where(['order_id' => $orderId, 'is_detailing' => false, 'type' => OrderService::TYPE_SERVICE])->column();
        $selectedDetailingServices = OrderService::find()->select('entity_id')
            ->where(['order_id' => $orderId, 'is_detailing' => true, 'type' => OrderService::TYPE_SERVICE])->column();

        return $this->render('edit', [
            'carwashId' => $cwId,
            'postCounts' => $carwashSettings->post_count ?? 1,
            'model' => $orderModel,
            'carBrands' => $carBrands,
            'carModels' => $carModels,
            'personalList' => $personalList,
            'washComplexes' => $washComplexes,
            'detailingComplexes' => $detailingComplexes,
            'washServices' => $washServices,
            'detailingServices' => $detailingServices,

            'selectedWashComplexes' => $selectedWashComplexes ?? [],
            'selectedDetailingComplexes' => $selectedDetailingComplexes ?? [],
            'selectedWashServices' => $selectedWashServices ?? [],
            'selectedDetailingServices' => $selectedDetailingServices ?? [],
        ]);
    }

    public function actionShow($id)
    {
        $cwId = \Yii::$app->user->identity->getCWid();
        $carwashSettings = CarwashSettings::findOne(['carwash_id' => $cwId]);
        $personalList = Personal::find()->where(['carwash_id' => $cwId])->all();

        $orderModel = Orders::findOne($id);
        if (!$orderModel) {
            throw new NotFoundHttpException('Заказ не найден');
        }
        if ($orderModel->carwash_id != $cwId) {
            throw new ForbiddenHttpException('Нет доступа к данному заказу');
        }
        $selectedComplexes = $orderModel->getOrderServices()->where(['type' => OrderService::TYPE_COMPLEX])->all();
        $selectedServices = $orderModel->getOrderServices()->where(['type' => OrderService::TYPE_SERVICE])->all();

        $carBrand = $orderModel->getCarBrand()->one();
        $carModel = $orderModel->getCarModel()->one();


        return $this->render('show', [
            'carwashId' => $cwId,
            'postCounts' => $carwashSettings->post_count,
            'model' => $orderModel,
            'carBrand' => $carBrand,
            'carModel' => $carModel,
            'personalList' => $personalList,

            'selectedComplexes' => $selectedComplexes,
            'selectedServices' => $selectedServices,
        ]);
    }

    private function saveSelectedServices($orderId, $carType)
    {
        // Комплекс
        $washComplexIds = \Yii::$app->request->post('ComplexesList');
        if (is_string($washComplexIds)) {
            $washComplexIds = explode(',', $washComplexIds);
        }
        if (!empty($washComplexIds)) {
            $washComplexArray = Complexes::find()->where(['in', 'id', $washComplexIds])->all();
            foreach ($washComplexArray as $complex) {
                $priceType = "type_{$carType}_price";
                $item = new OrderService();
                $item->order_id = $orderId;
                $item->entity_id = $complex->id;
                $item->type = OrderService::TYPE_COMPLEX;
                $item->is_detailing = (int)$complex->is_detailing;
                $item->order_id = $orderId;
                $item->name = $complex->name;
                $item->price = $complex->$priceType;
                $item->save();
            }
        }

        // Услуги
        $washServiceIds = \Yii::$app->request->post('ServicesList');
        if (is_string($washServiceIds)) {
            $washServiceIds = explode(',', $washServiceIds);
        }
        if (!empty($washServiceIds)) {
            $washServiceArray = Services::find()->where(['in', 'id', $washServiceIds])->all();
            foreach ($washServiceArray as $service) {
                $priceType = "type_{$carType}_price";
                $item = new OrderService();
                $item->order_id = $orderId;
                $item->entity_id = $service->id;
                $item->type = OrderService::TYPE_SERVICE;
                $item->is_detailing = (int)$service->is_detailing;
                $item->order_id = $orderId;
                $item->name = $service->name;
                $item->price = $service->$priceType;
                $item->save();
            }
        }
    }

}