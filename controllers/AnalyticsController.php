<?php


namespace app\controllers;


use app\commons\helpers\ClientHelper;
use app\commons\helpers\TimeHelper;
use app\commons\models\AnalyticsSpreadsheet;
use app\models\ar\complex\Complexes;
use app\models\ar\order\Orders;
use app\models\ar\order\OrderService;
use app\models\ar\personal\Personal;
use app\models\ar\service\Services;
use yii\data\Pagination;
use yii\db\Expression;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use function GuzzleHttp\Psr7\uri_for;

/**
 * Class AnalyticsController
 * @package app\controllers
 */
class AnalyticsController extends Controller
{
    public $layout = 'app/main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['perm_view_analytics'],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    public function actionFinance()
    {
        $cwId = \Yii::$app->user->identity->getCWid() ?? 0;
        $dataType = 'personal';


        [$ordersFindFromDate, $ordersFindToDate] = TimeHelper::resolveDateRange('2018-01-01', date('Y-m-d'));

        $orders = Orders::find()
            ->where(['orders.carwash_id' => $cwId])
            ->andWhere(['between', 'orders.date', $ordersFindFromDate, $ordersFindToDate])
            ->all();

        $serviceType = \Yii::$app->request->get('serviceType');
        $category = \Yii::$app->request->get('category');
        if ($category) {
            if (in_array($category, ['wash', 'detail'])) {
                $dataType = 'services';
                if ($serviceType === 'complex') {
                    $dataType = 'complexes';
                } elseif ($serviceType === 'all') {
                    $dataType = 'all';
                }
            } else {
                $dataType = 'personal';
            }
        }

        $data = [];
        switch ($dataType) {
            case 'personal':
                $data = $this->prepareDataForPersonalFinance($cwId, $orders);
                break;
            case 'services':
                if ($category === 'wash') {
                    $data = $this->prepareDataForServicesFinance($cwId, false, $orders);
                } else {
                    $data = $this->prepareDataForServicesFinance($cwId, true, $orders);
                }
                break;
            case 'complexes':
                if ($category === 'wash') {
                    $data = $this->prepareDataForComplexesFinance($cwId, false, $orders);
                } else {
                    $data = $this->prepareDataForComplexesFinance($cwId, true, $orders);
                }
                break;
            case 'all':
                if ($category === 'wash') {
                    $data = $this->prepareDataForAllServiceFinance($cwId, false, $orders);
                } else {
                    $data = $this->prepareDataForAllServiceFinance($cwId, true, $orders);
                }
                // Костыль, т.к. у нас не должно быть лишнего типа all.
                $dataType = 'services';
                break;
        }

        if ($sort = \Yii::$app->request->get('sort')) {
            if ($sort === 'sum') {
                if ($dataType === 'personal') {
                    ArrayHelper::multisort($data, 'personalSum', SORT_DESC);
                } elseif (in_array($dataType, ['services', 'complexes'])) {
                    ArrayHelper::multisort($data, 'serviceSum', SORT_DESC);
                }
            } elseif ($sort === 'count') {
                if ($dataType === 'personal') {
                    ArrayHelper::multisort($data, 'totalOrders', SORT_DESC);
                } elseif (in_array($dataType, ['services', 'complexes'])) {
                    ArrayHelper::multisort($data, 'totalServices', SORT_DESC);
                }
            }
        }

        if (\Yii::$app->request->get('print')) {
            $spreadsheetCreator = new AnalyticsSpreadsheet();
            if ($category === 'personal') {
                $file = $spreadsheetCreator->printFinancePersonalTable($data);
            } elseif (in_array($category, ['wash', 'detail'])) {
                if ($serviceType === 'complex') {
                    $file = $spreadsheetCreator->printFinanceComplexTable($data);
                } elseif (in_array($serviceType, ['service', 'all'])) {
                    $file = $spreadsheetCreator->printFinanceServiceTable($data);
                }
            }
            if (isset($file)) {
                return \Yii::$app->response->sendFile($file);
            }
        }

        return $this->render('finance', [
            'dataType' => $dataType,
            'data' => $data,
        ]);
    }

    public function actionOrders()
    {
        $cwId = \Yii::$app->user->identity->getCWid() ?? 0;

        [$ordersFindFromDate, $ordersFindToDate] = TimeHelper::resolveDateRange(date('Y-m-d'), date('Y-m-d'));


        $query = Orders::find()->where(['orders.carwash_id' => $cwId])->andWhere(['between', 'orders.date', $ordersFindFromDate, $ordersFindToDate]);

        if ($sort = \Yii::$app->request->get('sort')) {
            if ($sort === 'date') {
                $query->orderBy('date DESC');
            } elseif ($sort === 'price') {
                $query->orderBy('total_price DESC');
            }
        }

        $filterParam = \Yii::$app->request->get('filter', '');
        if ($filterParam !== '') {
            $filter = explode(',', $filterParam);
            if (in_array('with-reviews', $filter, true)) {
                $query->joinWith(['chats'])->andWhere('chat.id IS NOT NULL');
            }
            if (in_array('removed', $filter, true)) {
                $query->andWhere(['orders.status' => Orders::STATUS_REMOVED]);
            }
        }

        if (\Yii::$app->request->get('print')) {
            $spreadsheetCreator = new AnalyticsSpreadsheet();
            $file = $spreadsheetCreator->printOrdersTable($query->all());
            return \Yii::$app->response->sendFile($file);
        }

        $pages = new Pagination(['totalCount' => $query->count()]);
        $pages->setPageSize(15);
        $orders = $query->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('orders', [
            'orders' => $orders,
            'ordersPages' => $pages,
        ]);
    }

    public function actionClients()
    {
        $cwId = \Yii::$app->user->identity->getCWid() ?? 0;

        [$ordersFindFromDate, $ordersFindToDate] = TimeHelper::resolveDateRange('2018-01-01', date('Y-m-d'));
        $text = \Yii::$app->request->get('number');
        $sort = \Yii::$app->request->get('sort');

        $query = Orders::find()
            ->select([
                'orders.car_number',
                'orders.car_region',
                'id' => 'MAX(orders.id)',
            ])
            ->where(['orders.carwash_id' => $cwId])
            ->andWhere(['between', 'orders.date', $ordersFindFromDate, $ordersFindToDate])
            ->groupBy(['orders.car_number', 'orders.car_region']);

        if ($sort === 'total_price') {
            $query->addSelect(['total_price_sum' => 'SUM(orders.total_price)']);
            $query->orderBy(['total_price_sum' => SORT_DESC]);
        } elseif ($sort === 'visits') {
            $query->addSelect(['visits' => 'COUNT(orders.id)']);
            $query->orderBy(['visits' => SORT_DESC]);
        } else {
            $query->orderBy(['id' => SORT_DESC]);
        }

        if (is_string($text) && $text !== '' && $text !== 'false') {
            $query->andWhere(['like', new Expression("CONCAT(orders.car_number, ' ', orders.car_region)"), $text]);
        }

//        if ($filter = explode(',', \Yii::$app->request->get('filter'))) {
//            if (in_array('with-reviews', $filter)) {
//                $query->joinWith(['chats'])->andWhere('chat.id IS NOT NULL');
//            }
//        }

        if (\Yii::$app->request->get('print')) {
            $spreadsheetCreator = new AnalyticsSpreadsheet();
            $file = $spreadsheetCreator->printClientsTable($query->all());
            return \Yii::$app->response->sendFile($file);
        }

        $countQuery = Orders::find()
            ->where(['orders.carwash_id' => $cwId])
            ->andWhere(['between', 'orders.date', $ordersFindFromDate, $ordersFindToDate]);
        if (is_string($text) && $text !== '' && $text !== 'false') {
            $countQuery->andWhere(['like', new Expression("CONCAT(orders.car_number, ' ', orders.car_region)"), $text]);
        }
        $totalCount = (int) $countQuery
            ->select(new Expression('COUNT(DISTINCT CONCAT(orders.car_number, orders.car_region))'))
            ->scalar();

        $pages = new Pagination(['totalCount' => $totalCount]);
        $pages->setPageSize(15);
        $orders = $query->offset($pages->offset)->limit($pages->limit)->all();

        return $this->render('clients', [
            'orders' => $orders,
            'ordersPages' => $pages,
        ]);
    }

    /**
     * @param $orderId - любой номер заказа клиента, нужен для сопоставления
     * @throws NotFoundHttpException
     */
    public function actionShowClientByOrder($orderId)
    {
        $cwId = \Yii::$app->user->identity->getCWid() ?? 0;

        $order = Orders::findOne($orderId);
        if (!$order || $order->carwash_id != $cwId) {
            throw new NotFoundHttpException('Данный заказ не найден');
        }

        $orders = Orders::find()->where(['carwash_id' => $cwId])
            ->andWhere(['car_number' => $order->car_number, 'car_region' => $order->car_region])
            ->orderBy('id DESC')->all();
        $totalProfit = 0;
        foreach ($orders as $order) {
            $totalProfit += $order->total_price ?? 0;
        }
        $lastVisitOrder = Orders::find()->where(['carwash_id' => $cwId])
            ->andWhere(['car_number' => $order->car_number, 'car_region' => $order->car_region])
            ->orderBy('date DESC')->limit(1)->one();
        $isSubscriber = false;
        if (isset($lastVisitOrder)) {
            $isSubscriber = ClientHelper::isSubscriberByCarNumber($cwId, $lastVisitOrder->car_number, $lastVisitOrder->car_region);
        }

        return $this->render('client-show', [
            'orders' => $orders,
            'totalProfit' => $totalProfit,
            'isSubscriber' => $isSubscriber,
            'lastVisitOrder' => $lastVisitOrder,
        ]);
    }

    private function prepareDataForPersonalFinance($carwashId, $orders): array
    {
        $personals = Personal::find()->where(['carwash_id' => $carwashId])->all();

        $dataResult = [];
        /* @var Personal $personal */
        foreach ($personals as $personal) {
            $userName = $personal->getShortUsername();
            $ordersCount = 0;
            $totalOrdersSum = 0;
            // По-умолчанию выводим базовую информацию о зарплате
            $salaryText = $personal->getSalaryFullLabel();
            if ($personal->salary_type === Personal::SALARY_TYPE_PERCENT) {
                $salaryText = 0;
            }

            $workDates = [];
            /* @var Orders $order */
            foreach ($orders as $order) {
                // Пропускаем заказы не нашего исполнителя
                if ($order->personal_id !== $personal->id) {
                    continue;
                }
                $workDates[$order->date ?? 0] = 1;
                ++$ordersCount;
                if (!empty($order->total_price)) {
                    $totalOrdersSum += $order->total_price;
                }

                // Подсчет заработанных процентов с заказа
                if ($personal->salary_type === Personal::SALARY_TYPE_PERCENT) {
                    if (!empty($personal->salary) && !empty($order->total_price)) {
                        $salaryText += floor(($personal->salary / 100) * $order->total_price);
                    }
                }
            }

            $dataResult[] = ['personalName' => $userName, 'personalSum' => $totalOrdersSum,
                'salaryType' => $personal->salary_type ?? 0, 'totalOrders' => $ordersCount,
                'personalSalary' => $salaryText, 'workDays' => count($workDates)];
        }

        return $dataResult;
    }

    private function prepareDataForServicesFinance($carwashId, $isDetailing, $orders): array
    {
        $services = Services::find()->where(['carwash_id' => $carwashId])->andWhere(['is_detailing' => (int)$isDetailing])
            ->all();
        $orderIds = ArrayHelper::map($orders, 'id', 'id');

        $dataResult = [];

        /* @var Services $service */
        foreach ($services as $service) {
            $serviceName = $service->name;
            $count = 0;
            $sum = 0;

            $countAndSum = (new Query())
                ->select('COUNT(id) as count, SUM(price) as sum')
                ->from('order_service')
                ->where(['in', 'order_id', $orderIds])
                ->andWhere(['entity_id' => $service->id, 'type' => OrderService::TYPE_SERVICE, 'is_detailing' => (int)$isDetailing])
                ->one();
            if ($countAndSum) {
                $sum = $countAndSum['sum'];
                $count = $countAndSum['count'];
            }
            $dataResult[] = ['name' => $serviceName, 'serviceSum' => $sum, 'totalServices' => $count];
        }

        return $dataResult;
    }

    private function prepareDataForComplexesFinance($carwashId, $isDetailing, $orders): array
    {
        $complexes = Complexes::find()->where(['carwash_id' => $carwashId])->andWhere(['is_detailing' => (int)$isDetailing])
            ->all();
        $orderIds = ArrayHelper::map($orders, 'id', 'id');

        $dataResult = [];

        /* @var Services $complex */
        foreach ($complexes as $complex) {
            $serviceName = $complex->name;
            $count = 0;
            $sum = 0;

            $countAndSum = (new Query())
                ->select('COUNT(id) as count, SUM(price) as sum')
                ->from('order_service')
                ->where(['in', 'order_id', $orderIds])
                ->andWhere(['entity_id' => $complex->id, 'type' => OrderService::TYPE_COMPLEX, 'is_detailing' => (int)$isDetailing])
                ->one();
            if ($countAndSum) {
                $sum = $countAndSum['sum'];
                $count = $countAndSum['count'];
            }
            $dataResult[] = ['name' => $serviceName, 'serviceSum' => $sum, 'totalServices' => $count];
        }

        return $dataResult;
    }

    private function prepareDataForAllServiceFinance($carwashId, $isDetailing, $orders): array
    {
        $servicesData = $this->prepareDataForServicesFinance($carwashId, $isDetailing, $orders);
        $complexesData = $this->prepareDataForComplexesFinance($carwashId, $isDetailing, $orders);

        return array_merge_recursive($servicesData, $complexesData);
    }
}