<?php

namespace app\modules\api\controllers\v1;

use app\commons\helpers\AutoOrder;
use app\commons\helpers\CarTypeHelper;
use app\commons\helpers\FreeTimeCalculate;
use app\commons\helpers\SaleHelper;
use app\commons\helpers\TimeHelper;
use app\models\ar\car\CarBrands;
use app\models\ar\car\CarModels;
use app\models\ar\carwash\Carwash;
use app\models\ar\carwash\CarwashSettings;
use app\models\ar\complex\Complexes;
use app\models\ar\order\Orders;
use app\models\ar\order\OrderService;
use app\models\ar\personal\Personal;
use app\models\ar\service\Services;
use app\modules\api\commons\ApiHelper;
use app\modules\api\commons\v1\ReturnCodes;
use yii\db\Exception;
use yii\web\Controller;
use Yii;

class OrdersController extends Controller
{
    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    public function actionAutoCreate()
    {
        $cwId = Yii::$app->request->post('cwId');
        $pId = Yii::$app->request->post('pId');
        $token = Yii::$app->request->post('token');
        // Данные заказа
        $date = Yii::$app->request->post('date');
        $startTime = Yii::$app->request->post('startTime');
        $carType = Yii::$app->request->post('carType') ?? 1;
        $carNumber = Yii::$app->request->post('carNumber') ?? 'A111AA';
        $carRegion = Yii::$app->request->post('carRegion') ?? '000';
        $totalPrice = Yii::$app->request->post('totalPrice') ?? 0;
        $sale = Yii::$app->request->post('sale') ?? 0;
        $workTime = (int)(Yii::$app->request->post('workTime') ?? 0);


        if (!ApiHelper::tokenValidator($token, $pId)) {
            \Yii::$app->response->setStatusCode(ReturnCodes::INVALID_TOKEN);

            return [
                'errors' => [
                    'token' => 'Токен доступа не совпал'
                ],
                'message' => 'Требуется повторная авторизация'
            ];
        }
        $personal = Personal::findOne($pId);
        if (!$personal || $personal->carwash_id != $cwId) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);

            return [
                'errors' => [
                    'pId' => 'Нет доступа к созданию заказа'
                ],
                'message' => 'Неверно передан сотрудник'
            ];
        }
        $carwash = Carwash::findOne($cwId);
        $carwashSettings = CarwashSettings::findOne(['carwash_id' => $cwId]);
        if (!$carwash || !$carwashSettings) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'errors' => [
                    'cwId' => 'Не найдены базовые данные'
                ],
                'message' => 'Неверно передана информация об автомойке'
            ];
        }

        $errors = [];
        if (!$date || !TimeHelper::isValidDate($date)) {
            $errors['date'] = 'Дата указана неверно';
        }
        $order = new Orders();
        $order->carwash_id = $cwId;
        $order->date = $date;

        if (!$startTime || $startTime < 0 || $startTime > TimeHelper::MINUTES_PER_DAY) {
            $errors['startTime'] = 'Время начала не соответствует требуемому';
        }

        $order->start_time = $startTime;

        if ($carType && !in_array($carType, [CarTypeHelper::TYPE_SEDAN, CarTypeHelper::TYPE_CROSSOVERS,
                CarTypeHelper::TYPE_SUV, CarTypeHelper::TYPE_MINIBUSES, CarTypeHelper::TYPE_OTHER], false)) {
            $errors['carType'] = 'Неверный тип автомобиля';
        }
        $order->car_type = $carType;
        $order->car_number = $carNumber;
        $order->car_region = $carRegion;

        $order->total_price = $totalPrice;
        $order->sale = $sale;
        if ($workTime < $carwashSettings->checkout_time) {
            $workTime = $carwashSettings->checkout_time;
        }
        $order->work_time = $workTime;
        $order->status = Orders::STATUS_NEW_FROM_WASH;

        if ($errors) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ATTRIBUTES_ERROR);

            return [
                'errors' => $errors,
                'result' => false
            ];
        }
        $autoOrder = new AutoOrder($cwId, $date, TimeHelper::convertMinToTime($startTime), $workTime);
        $order->post = $autoOrder->findOptimalPost();

        if ($order->post === false) {
            \Yii::$app->response->setStatusCode(ReturnCodes::CANT_CREATE);

            return [
                'errors' => [
                    'startTime' => 'Не удается записать с указанным временем начала'
                ],
                'message' => 'Не удается записать с указанным временем начала, так как все посты заняты',
                'result' => false
            ];
        }
        // Время окончания рассчитывается автоматически, исходя из времени потраченного на работу
        $order->end_time = TimeHelper::convertTimeToMin($autoOrder->getEndTime());


        if (!$order->save()) {
            \Yii::$app->response->setStatusCode(ReturnCodes::SERVER_ERROR);

            return [
                'errors' => $order->errors,
                'result' => false
            ];
        }

        // Добавление товаров в заказ
        $this->saveSelectedServices($order->id, $carType);

        return [
            'orderId' => $order->id,
            'result' => true,
        ];
    }


    public function actionCreate()
    {
        $cwId = Yii::$app->request->post('cwId');
        $pId = Yii::$app->request->post('pId');
        $token = Yii::$app->request->post('token');
        // Данные заказа
        $date = Yii::$app->request->post('date');
        $post = Yii::$app->request->post('post');
        $startTime = Yii::$app->request->post('startTime');
        $endTime = Yii::$app->request->post('endTime');
        $carType = Yii::$app->request->post('carType') ?? 1;
        $carNumber = Yii::$app->request->post('carNumber') ?? 'A111AA';
        $carRegion = Yii::$app->request->post('carRegion') ?? '000';
        $color = Yii::$app->request->post('color');
        $carBrandId = Yii::$app->request->post('carBrandId');
        $carModelId = Yii::$app->request->post('carModelId');
        $clientFullname = Yii::$app->request->post('clientFullname');
        $clientPhone = Yii::$app->request->post('clientPhone');
        $totalPrice = Yii::$app->request->post('totalPrice') ?? 0;
        $sale = Yii::$app->request->post('sale') ?? 0;
        $workTime = (int)(Yii::$app->request->post('workTime') ?? 0);
        $status = Yii::$app->request->post('status') ?? Orders::STATUS_NEW_FROM_WASH;
        $adminComment = Yii::$app->request->post('adminComment');
        $clientComment = Yii::$app->request->post('clientComment');
        $assignedPersonal = Yii::$app->request->post('assignedPersonal');
        $assignedPersonalFullname = Yii::$app->request->post('assignedPersonalFullname');

        if (!ApiHelper::tokenValidator($token, $pId)) {
            \Yii::$app->response->setStatusCode(ReturnCodes::INVALID_TOKEN);

            return [
                'errors' => [
                    'token' => 'Токен доступа не совпал'
                ],
                'message' => 'Требуется повторная авторизация'
            ];
        }
        $personal = Personal::findOne($pId);
        if (!$personal || $personal->carwash_id != $cwId) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);

            return [
                'errors' => [
                    'pId' => 'Нет доступа к созданию заказа'
                ],
                'message' => 'Неверно передан сотрудник'
            ];
        }
        $carwash = Carwash::findOne($cwId);
        $carwashSettings = CarwashSettings::findOne(['carwash_id' => $cwId]);
        if (!$carwash || !$carwashSettings) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'errors' => [
                    'cwId' => 'Не найдены базовые данные'
                ],
                'message' => 'Неверно передана информация об автомойке'
            ];
        }

        $errors = [];
        if (!$date || !TimeHelper::isValidDate($date)) {
            $errors['date'] = 'Дата указана неверно';
        }
        $order = new Orders();
        $order->carwash_id = $cwId;
        $order->date = $date;
        if (!$post || $post > $carwashSettings->post_count) {
            $errors['post'] = 'Переданный пост больше, чем количество постов указанное в настройках';
        } elseif ($post <= 0) {
            $errors['post'] = 'Переданный пост меньше минимально возможного';
        }
        $order->post = $post;

        if (!isset($startTime) || $startTime < 0 || $startTime > TimeHelper::MINUTES_PER_DAY) {
            $errors['startTime'] = 'Время начала не соответствует требуемому';
        }
        // Валидируем время
        $timeChecker = new FreeTimeCalculate($cwId);
        $isIntersection = $timeChecker->checkIntersectionsOrdersByPost($date, $startTime, $endTime, $post);
        if ($isIntersection) {
            $errors['startTime'] = 'Выбранное время уже занято на переданном посте';
        }
        $order->start_time = $startTime;

        if (!isset($endTime) || $endTime < 0 || $endTime > TimeHelper::MINUTES_PER_DAY) {
            $errors['endTime'] = 'Время окончания не соответствует требуемому';
        }
        $order->end_time = $endTime;

        if ($carType && !in_array($carType, [CarTypeHelper::TYPE_SEDAN, CarTypeHelper::TYPE_CROSSOVERS,
                CarTypeHelper::TYPE_SUV, CarTypeHelper::TYPE_MINIBUSES, CarTypeHelper::TYPE_OTHER], false)) {
            $errors['carType'] = 'Неверный тип автомобиля';
        }
        $order->car_type = $carType;
        $order->car_number = $carNumber;
        $order->car_region = $carRegion;
        $order->color = $color;
        if ($carBrandId && !CarBrands::findOne($carBrandId)) {
            $errors['carBrandId'] = 'Неверно передана марка автомобиля';
        }
        $order->car_brand_id = $carBrandId;

        if ($carModelId && !CarModels::findOne($carModelId)) {
            $errors['carModelId'] = 'Неверно передана модель автомобиля';
        }
        $order->car_model_id = $carModelId;

        $order->client_fullname = $clientFullname;
        $order->client_phone = $clientPhone;
        $order->total_price = $totalPrice;
        $order->sale = $sale;
        if ($workTime < $carwashSettings->checkout_time) {
            $workTime = $carwashSettings->checkout_time;
        }
        $order->work_time = $workTime;
        if ($status && !in_array($status, [Orders::STATUS_NEW_FROM_WASH, Orders::STATUS_NEW_FROM_CLIENT, Orders::STATUS_WORK,
                Orders::STATUS_ARCHIVE, Orders::STATUS_REMOVED], false)) {
            $errors['status'] = 'Статус не является одним из допустимых';
        }
        $order->status = $status;
        $order->admin_comment = $adminComment;
        $order->client_comment = $clientComment;

        if ($assignedPersonal) {
            $order->personal_id = $assignedPersonal;
        }
        if ($assignedPersonalFullname){
            $order->personal_fullname = $assignedPersonalFullname;
        }

        if ($errors) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ATTRIBUTES_ERROR);

            return [
                'errors' => $errors,
                'result' => false
            ];
        }

        if (!$order->save()) {
            \Yii::$app->response->setStatusCode(ReturnCodes::SERVER_ERROR);

            return [
                'errors' => $order->errors,
                'result' => false
            ];
        }

        // Добавление товаров в заказ
        $this->saveSelectedServices($order->id, $carType);

        return [
            'orderId' => $order->id,
            'result' => true,
        ];
    }

    public function actionEdit()
    {
        $id = Yii::$app->request->post('id');
        $cwId = Yii::$app->request->post('cwId');
        $pId = Yii::$app->request->post('pId');
        $token = Yii::$app->request->post('token');
        // Данные заказа
        $date = Yii::$app->request->post('date');
        $post = Yii::$app->request->post('post');
        $startTime = Yii::$app->request->post('startTime');
        $endTime = Yii::$app->request->post('endTime');
        $carType = Yii::$app->request->post('carType');
        $carNumber = Yii::$app->request->post('carNumber');
        $carRegion = Yii::$app->request->post('carRegion');
        $color = Yii::$app->request->post('color');
        $carBrandId = Yii::$app->request->post('carBrandId');
        $carModelId = Yii::$app->request->post('carModelId');
        $clientFullname = Yii::$app->request->post('clientFullname');
        $clientPhone = Yii::$app->request->post('clientPhone');
        $totalPrice = Yii::$app->request->post('totalPrice') ?? 0;
        $sale = Yii::$app->request->post('sale') ?? 0;
        $workTime = (int)(Yii::$app->request->post('workTime') ?? 0);
        $status = Yii::$app->request->post('status');
        $adminComment = Yii::$app->request->post('adminComment');
        $clientComment = Yii::$app->request->post('clientComment');

        if (!ApiHelper::tokenValidator($token, $pId)) {
            \Yii::$app->response->setStatusCode(ReturnCodes::INVALID_TOKEN);

            return [
                'errors' => [
                    'token' => 'Токен доступа не совпал'
                ],
                'message' => 'Требуется повторная авторизация'
            ];
        }
        $order = Orders::findOne($id);
        if (!$order) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'errors' => [
                    'id' => 'Заказ не найден'
                ],
                'message' => 'Не удалось найти заказ'
            ];
        }

        $personal = Personal::findOne($pId);

        if (!$personal || $personal->carwash_id != $order->carwash_id) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ACCESS_FORBIDDEN);

            return [
                'errors' => [
                    'pId' => 'Нет доступа к данному заказу'
                ],
                'message' => 'Неверно передан сотрудник'
            ];
        }
        $carwash = Carwash::findOne($cwId);
        $carwashSettings = CarwashSettings::findOne(['carwash_id' => $cwId]);
        if (!$carwash || !$carwashSettings) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'errors' => [
                    'cwId' => 'Не найдены базовые данные'
                ],
                'message' => 'Неверно передана информация об автомойке'
            ];
        }

        $errors = [];
        if (!$date || !TimeHelper::isValidDate($date)) {
            $errors['date'] = 'Дата указана неверно';
        }
        $order->date = $date;

        if (!$post || $post > $carwashSettings->post_count) {
            $errors['post'] = 'Переданный пост больше, чем количество постов указанное в настройках';
        } elseif ($post <= 0) {
            $errors['post'] = 'Переданный пост меньше минимально возможного';
        }
        $order->post = $post;

        if (!$startTime || $startTime < 0 || $startTime > TimeHelper::MINUTES_PER_DAY) {
            $errors['startTime'] = 'Время начала не соответствует требуемому';
        }
        // Валидируем время
        $timeChecker = new FreeTimeCalculate($cwId);
        $isIntersection = $timeChecker->checkIntersectionsOrdersByPost($date, $startTime, $endTime, $post, $id);
        if ($isIntersection) {
            $errors['startTime'] = 'Выбранное время уже занято на переданном посте';
        }
        $order->start_time = $startTime;

        if (!$endTime || $endTime < 0 || $endTime > TimeHelper::MINUTES_PER_DAY) {
            $errors['endTime'] = 'Время окончания не соответствует требуемому';
        }
        $order->end_time = $endTime;

        if ($carType && !in_array($carType, [CarTypeHelper::TYPE_SEDAN, CarTypeHelper::TYPE_CROSSOVERS,
                CarTypeHelper::TYPE_SUV, CarTypeHelper::TYPE_MINIBUSES, CarTypeHelper::TYPE_OTHER], false)) {
            $errors['carType'] = 'Неверный тип автомобиля';
        }
        $order->car_type = $carType;
        $order->car_number = $carNumber;
        $order->car_region = $carRegion;
        $order->color = $color;
        if ($carBrandId && !CarBrands::findOne($carBrandId)) {
            $errors['carBrandId'] = 'Неверно передана марка автомобиля';
        }
        $order->car_brand_id = $carBrandId;

        if ($carModelId && !CarModels::findOne($carModelId)) {
            $errors['carModelId'] = 'Неверно передана модель автомобиля';
        }
        $order->car_model_id = $carModelId;

        $order->client_fullname = $clientFullname;
        $order->client_phone = $clientPhone;
        $order->total_price = $totalPrice;
        $order->sale = $sale;
        if ($workTime < $carwashSettings->checkout_time) {
            $workTime = $carwashSettings->checkout_time;
        }
        $order->work_time = $workTime;
        if ($status && !in_array($status, [Orders::STATUS_NEW_FROM_WASH, Orders::STATUS_NEW_FROM_CLIENT, Orders::STATUS_WORK,
                Orders::STATUS_ARCHIVE, Orders::STATUS_REMOVED], false)) {
            $errors['status'] = 'Статус не является одним из допустимых';
        }
        $order->status = $status;
        $order->admin_comment = $adminComment;
        $order->client_comment = $clientComment;

        if ($errors) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ATTRIBUTES_ERROR);

            return [
                'errors' => $errors,
                'result' => false
            ];
        }

        if (!$order->save()) {
            \Yii::$app->response->setStatusCode(ReturnCodes::SERVER_ERROR);

            return [
                'errors' => $order->errors,
                'result' => false
            ];
        }

        // Очищаем ранее сохраненные услуги к заказу
        OrderService::deleteAll(['order_id' => $order->id]);

        // Добавление товаров в заказ
        $this->saveSelectedServices($order->id, $carType);

        return [
            'orderId' => $order->id,
            'result' => true,
        ];
    }

    public function actionShow($id, $pId, $token)
    {
        $personal = Personal::findOne($pId);
        $order = Orders::findOne($id);
        if (!$order || !$personal) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'errors' => [
                    'orderId' => 'Не найдены базовые модели'
                ],
                'message' => 'Не найден заказ'
            ];
        }
        if (!ApiHelper::tokenValidator($token, $pId)) {
            \Yii::$app->response->setStatusCode(ReturnCodes::INVALID_TOKEN);
            return [
                'result' => false,
                'errors' => [
                    'token' => 'Токен доступа не совпал'
                ],
                'message' => 'Требуется повторная авторизация'
            ];
        }
        if ($personal->carwash_id !== $order->carwash_id) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ACCESS_FORBIDDEN);
            return [
                'errors' => [
                    'pId' => 'Нет доступа к заказу'
                ],
                'message' => 'Нет доступа к заказу'
            ];
        }
        $selectedServicesForOrder = $order->getOrderServices()->all();
        $orderComplexes = [];
        $orderServices = [];
        /** @var OrderService[] $selectedServicesForOrder */
        foreach ($selectedServicesForOrder as $selectedService) {
            if ($selectedService->type == OrderService::TYPE_SERVICE) {
                $orderServices[] = [
                    'id' => $selectedService->entity_id,
                    'name' => $selectedService->name,
                    'is_detailing' => (bool)$selectedService->is_detailing
                ];
            }
            if ($selectedService->type == OrderService::TYPE_COMPLEX) {
                $orderComplexes[] = [
                    'id' => $selectedService->entity_id,
                    'name' => $selectedService->name,
                    'is_detailing' => (bool)$selectedService->is_detailing
                ];
            }
        }
        $carBrand = ($carBrand = CarBrands::findOne($order->car_brand_id)) ? $carBrand->toArray() : [];
        $carModel = ($carModel = CarModels::findOne($order->car_model_id)) ? $carModel->toArray() : [];

        return [
            'id' => $order->id,
            'carwashId' => $order->carwash_id,
            'personalId' => $order->personal_id,
            'personalFullname' => $order->personal_fullname,
            'date' => $order->date,
            'post' => $order->post,
            'startTime' => $order->start_time,
            'endTime' => $order->end_time,
            'carType' => $order->car_type ?? 1,
            'carNumber' => $order->car_number ?? 'A123AA',
            'carRegion' => $order->car_region ?? '777',
            'color' => $order->color ?? '',
            'carBrand' => $carBrand,
            'carModel' => $carModel,
            'clientFullname' => $order->client_fullname ?? '',
            'clientPhone' => $order->client_phone ?? '',
            'totalPrice' => $order->total_price ?? 0,
            'sale' => $order->sale ?? 0,
            'workTime' => $order->work_time ?? 0,
            'status' => $order->status,
            'adminComment' => $order->admin_comment ?? '',
            'clientComment' => $order->client_comment ?? '',
            'created_at' => $order->created_at,
            'services' => $orderServices,
            'complexes' => $orderComplexes,
        ];
    }

    public function actionCalculatePrice()
    {
        $cwId = Yii::$app->request->post('cwId');
        $pId = Yii::$app->request->post('pId');
        $carType = Yii::$app->request->post('carType');
        $servicesIds = Yii::$app->request->post('servicesIds') ?? [];
        $complexesIds = Yii::$app->request->post('complexesIds') ?? [];

        if ($servicesIds && is_string($servicesIds)) {
            $servicesIds = explode(',', $servicesIds);
        } else {
            $servicesIds = [];
        }
        if ($complexesIds && is_string($complexesIds)) {
            $complexesIds = explode(',', $complexesIds);
        } else {
            $complexesIds = [];
        }


        $carwash = Carwash::findOne($cwId);
        if (!$carwash) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'errors' => [
                    'cwId' => 'Не найдена автомойка'
                ],
                'message' => 'Не найдена базовая модель автомойки'
            ];
        }

        $services = [];
        if ($servicesIds && is_array($servicesIds)) {
            $services = Services::find()->where(['in', 'id', $servicesIds])->all();
        }
        $complexes = [];
        if ($complexesIds && is_array($complexesIds)) {
            $complexes = Complexes::find()->where(['in', 'id', $complexesIds])->all();
        }

        $settings = CarwashSettings::findOne(['carwash_id' => $cwId]);
        $timeCheckOut = $settings->checkout_time ?? 5;
        $serviceTimeMultiplier = $settings->service_time_multiplier ?? 0;

        $totalWorkTime = 0 + $timeCheckOut;
        $totalPrice = 0;
        $workTimeWithMultiplier = 0;
        $itemsArray = [];
        $sale = 0;
        $saleName = null;

        $saleHelper = new SaleHelper($carwash->id);
        $saleResult = $saleHelper->calculateTotalSales(false, $carType, $servicesIds ?? [], $complexesIds ?? []);
        if (isset($saleResult['saleName'])) {
            $saleName = $saleResult['saleName'];
        }
        /** @var Complexes $complex */
        foreach ($complexes as $complex) {
            $oldPrice = null;
            $price = $complex->{'type_' . $carType . '_price'} ?? 0;
            if (is_int($price)) {
                $totalPrice += $price;
            }
            if (isset($saleResult['complexes'][$complex->id]['price'])) {
                $oldPrice = $price;
                $price = (int)$saleResult['complexes'][$complex->id]['price'];
                $sale += $oldPrice - $price;
            }

            $workTime = $complex->{'type_' . $carType . '_time'} ?? 0;
            if (is_int($workTime)) {
                $totalWorkTime += $workTime;
            }

            $itemsArray[] = [
                'id' => $complex->id,
                'type' => 'complex',
                'name' => $complex->name,
                'price' => $price,
                'oldPrice' => $oldPrice,
            ];
        }

        /** @var Services $service */
        foreach ($services as $service) {
            $oldPrice = null;
            $price = $service->{'type_' . $carType . '_price'} ?? 0;
            if (is_int($price)) {
                $totalPrice += $price;
            }
            if (isset($saleResult['services'][$service->id]['price'])) {
                $oldPrice = $price;
                $price = (int)$saleResult['services'][$service->id]['price'];
                $sale += $oldPrice - $price;
            }
            $workTime = $service->{'type_' . $carType . '_time'} ?? 0;
            if (is_int($workTime)) {
                $totalWorkTime += $workTime;
            }

            $itemsArray[] = [
                'id' => $service->id,
                'type' => 'service',
                'name' => $service->name,
                'price' => $price,
                'oldPrice' => $oldPrice,
            ];
        }

        if ($sale) {
            $totalPrice -= $sale;
        }
        if ($totalPrice < 0) {
            $totalPrice = 0;
        }

        // Округляем время работы в большую сторону кратным 5
        $workTimeWithMultiplier = (int)($totalWorkTime * ((100 + $serviceTimeMultiplier) / 100));
        $totalWorkTime = $this->roundUpToFive($totalWorkTime) ?? 0;
        $workTimeWithMultiplier = $this->roundUpToFive($workTimeWithMultiplier) ?? 0;

        return ['result' => true, 'totalPrice' => $totalPrice, 'sale' => $sale, 'saleName' => $saleName,
            'workTime' => $totalWorkTime, 'workTimeWithMultiplier' => $workTimeWithMultiplier, 'items' => $itemsArray];
    }

    public function actionChangeStatus()
    {
        $id = Yii::$app->request->post('id');
        $pId = Yii::$app->request->post('pId');
        $status = Yii::$app->request->post('status');

        $personal = Personal::findOne($pId);
        $order = Orders::findOne($id);
        if (!$order || !$personal) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'errors' => [
                    'orderId' => 'Не найдены базовые модели'
                ],
                'message' => 'Не найден заказ'
            ];
        }
        if ($personal->carwash_id !== $order->carwash_id) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ACCESS_FORBIDDEN);
            return [
                'errors' => [
                    'pId' => 'Нет доступа к заказу'
                ],
                'message' => 'Нет доступа к заказу'
            ];
        }
        if (!in_array($status, [Orders::STATUS_NEW_FROM_WASH, Orders::STATUS_NEW_FROM_CLIENT, Orders::STATUS_WORK,
            Orders::STATUS_ARCHIVE, Orders::STATUS_REMOVED], false)) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ATTRIBUTES_ERROR);
            return [
                'errors' => [
                    'status' => 'Передан неверный статус для заказа'
                ],
                'message' => 'Нет возможности изменить статус заказа'
            ];
        }
        $order->status = $status;
        if (!$order->save()) {
            \Yii::$app->response->setStatusCode(ReturnCodes::SERVER_ERROR);
            return [
                'errors' => [
                    $order->errors
                ],
                'message' => 'Не удалось обновить статус заказа'
            ];
        }

        return [
            'result' => true
        ];

    }

    public function actionDelete()
    {
        $id = Yii::$app->request->post('id');
        $pId = Yii::$app->request->post('pId');
        $token = Yii::$app->request->post('token');

        $personal = Personal::findOne($pId);
        $order = Orders::findOne($id);
        if (!$order || !$personal) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'errors' => [
                    'orderId' => 'Не найдены базовые модели'
                ],
                'message' => 'Не найден заказ'
            ];
        }
        if (!ApiHelper::tokenValidator($token, $pId)) {
            \Yii::$app->response->setStatusCode(ReturnCodes::INVALID_TOKEN);
            return [
                'result' => false,
                'errors' => [
                    'token' => 'Токен доступа не совпал'
                ],
                'message' => 'Требуется повторная авторизация'
            ];
        }
        if ($personal->carwash_id !== $order->carwash_id) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ACCESS_FORBIDDEN);
            return [
                'errors' => [
                    'pId' => 'Нет доступа к заказу'
                ],
                'message' => 'Нет доступа к заказу'
            ];
        }

        if ($order->delete()) {
            return [
                'result' => true
            ];
        }

        return [
            'result' => false
        ];
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

    /**
     * Округление в большую сторону, кратное 5
     * @param $value
     * @return int
     */
    private function roundUpToFive($value)
    {
        if ($value % 5 == 1) {
            $value += 4;
        } elseif ($value % 5 == 2) {
            $value += 3;
        } elseif ($value % 5 == 3) {
            $value += 2;
        } elseif ($value % 5 == 4) {
            $value += 1;
        }
        return $value;
    }

}
