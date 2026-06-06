<?php


namespace app\controllers\ajax;


use app\commons\helpers\CarTypeHelper;
use app\commons\helpers\FreeTimeCalculate;
use app\commons\helpers\SaleHelper;
use app\commons\helpers\TimeHelper;
use app\commons\notification\NotifyHelper;
use app\models\ar\carwash\Carwash;
use app\models\ar\carwash\CarwashBlacklist;
use app\models\ar\carwash\CarwashSchedule;
use app\models\ar\carwash\CarwashSettings;
use app\models\ar\Clients;
use app\models\ar\complex\Complexes;
use app\models\ar\order\Orders;
use app\models\ar\order\OrderService;
use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalNotification;
use app\models\ar\service\Services;
use Spatie\Period\Period;
use Spatie\Period\PeriodCollection;
use Spatie\Period\Precision;
use Spatie\Period\Visualizer;
use yii\db\Exception;
use yii\web\Controller;
use Yii;
use yii\web\ForbiddenHttpException;

/**
 * Class OrdersController
 * @package app\controllers\ajax
 */
class OrdersController extends Controller
{
    /**
     * @var bool
     */
    public $enableCsrfValidation = false;

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function actionCalcLiveOrderPrice()
    {
        $cwId = Yii::$app->request->post('cwId');
        $carType = Yii::$app->request->post('carType');
        $servicesIds = Yii::$app->request->post('servicesIds');
        $complexesIds = Yii::$app->request->post('complexesIds');

        $carwash = Carwash::findOne($cwId);
        if (!$carwash) {
            throw new Exception('Не найдена данная автомойка');
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

//        return ['result' => false, 'tw' => $totalWorkTime, 'tp' => $totalPrice, 'ia' => $itemsArray, 'services' => $services, 'complexes' => $complexes];

        // Округляем время работы в большую сторону кратным 5

        $workTimeWithMultiplier = (int)($totalWorkTime * ((100 + $serviceTimeMultiplier) / 100));
        $totalWorkTime = $this->roundUpToFive($totalWorkTime) ?? 0;
        $workTimeWithMultiplier = $this->roundUpToFive($workTimeWithMultiplier) ?? 0;

        return ['result' => true, 'totalPrice' => $totalPrice, 'sale' => $sale, 'saleName' => $saleName,
            'workTime' => $totalWorkTime, 'workTimeWithMultiplier' => $workTimeWithMultiplier, 'items' => $itemsArray];
    }


    /**
     * Получение актуальных цен
     * @return array
     */
    public function actionGetPrices()
    {
        $cwId = Yii::$app->request->post('cwId');
        $carType = Yii::$app->request->post('carType');

        $carwash = Carwash::findOne($cwId);
        $services = Services::findAll(['carwash_id' => $cwId]);
        $complexes = Complexes::findAll(['carwash_id' => $cwId]);

        $itemsArray = [];
        foreach ($complexes as $complex) {
            $price = $complex->{'type_' . $carType . '_price'} ?? 0;
            $itemsArray[] = [
                'id' => $complex->id,
                'type' => 'complex',
                'name' => $complex->name,
                'price' => $price,
                'oldPrice' => null,
            ];
        }

        foreach ($services as $service) {
            $price = $service->{'type_' . $carType . '_price'} ?? 0;
            $workTime = $service->{'type_' . $carType . '_time'};

            $itemsArray[] = [
                'id' => $service->id,
                'type' => 'service',
                'name' => $service->name,
                'price' => $price,
                'oldPrice' => null,
            ];
        }


        return ['result' => true, 'items' => $itemsArray];
    }

    /**
     * Получение настроек журнала
     * @return array
     */
    public function actionJournalGetSettings()
    {
        $cwId = Yii::$app->request->post('cwId');
        $pId = Yii::$app->request->post('pId');
        $date = Yii::$app->request->post('date');
        $dayOfWeek = date('w', strtotime($date));

        $carwash = Carwash::findOne($cwId);
        $personal = Personal::findOne($pId);
        if (!$carwash || !$personal) {
            return ['result' => false, 'message' => 'Не найдены заданные модели'];
        }
        if ($personal->carwash_id !== $carwash->id) {
            return ['result' => false, 'message' => 'В доступе отказано'];
        }
        $settings = CarwashSettings::findOne(['carwash_id' => $cwId]);
        $schedule = CarwashSchedule::findOne(['carwash_id' => $cwId]);
        if (!$settings || !$schedule) {
            return ['result' => false, 'message' => $schedule->id . ' Не найдены настройки автомойки'];
        }
        // Время задержки персонала
        $staffDelayTime = 0;
        if ($settings->until_last_client) {
            $staffDelayTime = $settings->staff_delay_time ?? 0;
        }

        $resultArray = [];
        $resultArray['posts'] = $settings->post_count ?? 1;
//        $resultArray['cellMinutes'] = 60;
        $startDayHour = $schedule->getStartDayTimeByDay($dayOfWeek);
        $endDayHour = $schedule->getEndDayTimeByDay($dayOfWeek);
        $isWorkDay = $schedule->isWorkDay($dayOfWeek);

        $startDayHour = date('H', strtotime($startDayHour));
        $endDayHour = date('H', strtotime($endDayHour)) + 1; // Чтобы день заканчивался следующим часом

        if ($staffDelayTime > 0) {
            $addHour = ceil($staffDelayTime / 60);
            $endDayHour = $endDayHour + $addHour;
            if ($endDayHour > 24) {
                $endDayHour = 24;
            }
        }

        if ($startDayHour == '00') {
            $startDayHour = 0;
        }
        // В случае, если у нас круглосуточная запись выходит за пределы дня
        if ($endDayHour < $startDayHour) {
            $endDayHour = 24;
        }
        $resultArray['startDayHour'] = $startDayHour;
        $resultArray['endDayHour'] = $endDayHour;
        $resultArray['isWorkDay'] = $isWorkDay;

        return ['result' => true, 'settings' => (object)$resultArray];
    }

    /**
     * Получение списка заказов в журнале
     * @return array
     */
    public function actionJournalGetOrders()
    {
        $cwId = Yii::$app->request->post('cwId');
        $pId = Yii::$app->request->post('pId');
        $date = date('Y-m-d', strtotime(Yii::$app->request->post('date')));

        $carwash = Carwash::findOne($cwId);
        $personal = Personal::findOne($pId);
        if (!$carwash || !$personal) {
            return ['result' => false, 'message' => 'Не найдены заданные модели'];
        }
        if ($personal->carwash_id !== $carwash->id) {
            return ['result' => false, 'message' => 'В доступе отказано'];
        }
        $dateYesterday = date('Y-m-d', strtotime($date . '-1 day'));

        $orders = Orders::find()->where(['in', 'date', [$date, $dateYesterday]])->andWhere(['carwash_id' => $cwId])->all();

        $ordersArray = [];

        /** @var Orders[] $orders * */
        foreach ($orders as $order) {
            $endDate = $order->date;
            $workMinutes = $order->end_time - $order->start_time;
            // Если время начала больше чем время окончания - значит заказ заканчивается на следующий день
            if ($order->start_time > $order->end_time) {
                $endDate = date('Y-m-d', strtotime($endDate . "+1 day"));
                $workMinutes = (TimeHelper::MINUTES_PER_DAY - $order->start_time) + $order->end_time;
            }
            $reputation = 0; // В зависимости от статуса пользователя и его посещений
            if (isset($order->client->reputation)) {
                $reputation = $order->client->reputation ?? 0;
            }

            $orderText = $this->getOrderShortTextArray($order);
            $startDateTime = $order->date . ' ' . TimeHelper::convertMinToTime($order->start_time);
            $endDateTime = $endDate . ' ' . TimeHelper::convertMinToTime($order->end_time);

            $ordersArray[] = [
                'id' => $order->id,
                'clientId' => $order->client_id,
                'carNumber' => $order->car_number ?? 'A123AA',
                'carRegion' => $order->car_region ?? '777',
                'totalPrice' => $order->total_price,
                'personalId' => $order->personal_id,
                'personalFullname' => $order->personal_fullname,
                'adminComment' => $order->admin_comment,
                'clientComment' => $order->client_comment,
                'status' => $order->status,
                'workMin' => $workMinutes,
                'reputation' => $reputation ?? 0,
                'postId' => $order->post,
                'textArray' => $orderText,
                'startDate' => $startDateTime,
                'endDate' => $endDateTime,
            ];
        }
        return ['result' => true, 'date' => $date, 'orders' => $ordersArray];
    }

    /**
     * Обновление поста и времени заказа
     * @return array
     */
    public function actionUpdateOrderDatetime()
    {
        $cwId = Yii::$app->request->post('cwId');
        $pId = Yii::$app->request->post('pId');
        $orderId = Yii::$app->request->post('orderId');
        $post = Yii::$app->request->post('post');
        $startAt = Yii::$app->request->post('startAt');
        $endAt = Yii::$app->request->post('endAt');

        $carwash = Carwash::findOne($cwId);
        $personal = Personal::findOne($pId);
        if (!$carwash || !$personal) {
            return ['result' => false, 'message' => 'Не найдены заданные модели'];
        }
        if ($personal->carwash_id !== $carwash->id) {
            return ['result' => false, 'message' => 'В доступе отказано'];
        }
        $order = Orders::findOne($orderId);
        if (!$order || $personal->carwash_id != $order->carwash_id) {
            return ['result' => false, 'message' => 'В доступе отказано или заказ не найден'];
        }
        $startTime = TimeHelper::convertTimeToMin(date('H:i', strtotime($startAt)));
        $endTime = TimeHelper::convertTimeToMin(date('H:i', strtotime($endAt)));

        // Фикс перетаскивания записи, когда она заканчивается в 00 следующего дня
        if ($endTime == 0) {
            $endAt = date('Y-m-d H:i:s', strtotime($endAt . '-1 minutes'));
            $endTime = 1439;
        }

        $carwashSettings = CarwashSettings::findOne(['carwash_id' => $carwash->id]);
        $carwashSchedule = CarwashSchedule::findOne(['carwash_id' => $carwash->id]);
        if (!$carwashSettings | !$carwashSchedule) {
            return ['result' => false, 'message' => 'Не удалось найти настройки автомойки'];
        }
        if ($post < 1 || $post > $carwashSettings->post_count) {
            return ['result' => false, 'message' => 'Неверно указан пост записи'];
        }
        // Время задержки персонала
        $staffDelayTime = 0;
        if ($carwashSettings->until_last_client) {
            $staffDelayTime = $carwashSettings->staff_delay_time ?? 0;
        }

        $order->start_time = $startTime;
        $startWeekDay = date('w', strtotime($startAt));
        $endWeekDay = date('w', strtotime($endAt));
        $startScheduleEndTime = $carwashSchedule->getEndDayTimeByDay($startWeekDay);
        if ($staffDelayTime > 0) {
            $startScheduleEndTime = date('H:i:s', strtotime($startScheduleEndTime . '+' . $staffDelayTime . ' minutes'));
        }

        // Если заказ явно переносится на следующий день
        if ($startWeekDay != $endWeekDay) {
//            var_dump('День переносится на следующий');
            if ($carwashSchedule->isWorkDay($endWeekDay) &&
                date('H:i', strtotime($endAt)) > $carwashSchedule->getStartDayTimeByDay($endWeekDay)
                && date('H:i', strtotime($endAt)) < $carwashSchedule->getEndDayTimeByDay($endWeekDay)) {
//                var_dump('Заканчивается в рабочее время след.дня');
                // Всё в порядке, заказ заканчивается следующим днём
            } else {
//                var_dump('Заканчивается вне рабочего времени');
                $endTime = TimeHelper::convertTimeToMin(date('H:i', strtotime($startScheduleEndTime)));
            }
        } else {
//            var_dump('Заказ заканчивается этим же днём');
            // Фактическое время окончания дня
            $currentDayEndTime = $carwashSchedule->getEndDayTimeByDay($startWeekDay);

            if (date('H:i', strtotime($endAt)) > date('H:i', strtotime($currentDayEndTime))) {
//                var_dump('Заканчивается вне рабочего времени');
                $endTime = TimeHelper::convertTimeToMin(date('H:i', strtotime($startScheduleEndTime)));
            }
        }
        $order->end_time = $endTime;

        $order->post = $post;
        if ($order->save()) {
            return ['result' => true, 'message' => 'Запись успешно обновлена'];
        }
        return ['result' => false, 'message' => 'Произошла ошибка при записи изменений заказа'];
    }

    public function actionGetFreeTimes()
    {
        $cwId = Yii::$app->request->post('cwId');
        $pId = Yii::$app->request->post('pId');
        $workTimeMin = Yii::$app->request->post('workTimeMin');
        $date = date('Y-m-d', strtotime(Yii::$app->request->post('date')));
        $dayOfWeek = date('w', strtotime($date));

        $carwash = Carwash::findOne($cwId);
        $personal = Personal::findOne($pId);
        if (!$carwash || !$personal) {
            return ['result' => false, 'message' => 'Не найдены заданные модели'];
        }
        if ($personal->carwash_id !== $carwash->id) {
            return ['result' => false, 'message' => 'В доступе отказано'];
        }
        $schedule = CarwashSchedule::findOne(['carwash_id' => $cwId]);
        $settings = CarwashSettings::findOne(['carwash_id' => $cwId]);
        if (!$schedule || !$settings) {
            return ['result' => false, 'message' => 'Не настроено первичное расписание / настройки'];
        }
        $timezone = $carwash->timezone;
        $currentTimeWithTimezone = new \DateTime();
        $currentTimeWithTimezone->setTimezone(new \DateTimeZone('+0' . $timezone . '00'));


        $startDayHour = $schedule->getStartDayTimeByDay($dayOfWeek);
        $endDayHour = $schedule->getEndDayTimeByDay($dayOfWeek);

        $dateYesterday = date('Y-m-d', strtotime($date . '-1 day'));

        // Заказы могут заканчиваться следующим днем, поэтому вытаскиваем за два дня
        $orders = Orders::find()->where(['in', 'date', [$date, $dateYesterday]])->all();

        /**
         * @var Period[]|null $freePeriods
         */

        $freeTimeCalculate = new FreeTimeCalculate($carwash->id);
        $freeTimeInterval = $freeTimeCalculate->calculateFreePeriodsByPosts($date, $startDayHour, $endDayHour, $orders, $workTimeMin);

        return ['result' => true, 'items' => $freeTimeInterval];
    }


    public function actionGetFreeTimeIntervals()
    {
        $cwId = Yii::$app->request->post('cwId');
        $post = Yii::$app->request->post('post');
        $currentOrderId = Yii::$app->request->post('currentOrderId');
        $date = date('Y-m-d', strtotime(Yii::$app->request->post('date')));
        $dayOfWeek = date('w', strtotime($date));
        $carwash = Carwash::findOne($cwId);
        if (!$carwash) {
            return ['result' => false, 'message' => 'Не найдены заданные модели'];
        }
        $schedule = CarwashSchedule::findOne(['carwash_id' => $cwId]);
        $settings = CarwashSettings::findOne(['carwash_id' => $cwId]);
        if (!$settings || !$schedule) {
            return ['result' => false, 'message' => 'Не настроено первичное расписание / настройки'];
        }
        if (!$schedule->isWorkDay($dayOfWeek)){
            return ['result' => true, 'intervals' => []]; // День не рабочий, а значит, промежутков нет
        }
        $timezone = $carwash->timezone;
        $currentTimeWithTimezone = new \DateTime();
        $currentTimeWithTimezone->setTimezone(new \DateTimeZone('+0' . $timezone . '00'));

        $startDayHour = $schedule->getStartDayTimeByDay($dayOfWeek);
        $endDayHour = $schedule->getEndDayTimeByDay($dayOfWeek);

        $startAt = $startDayHour;
        $endAt = $endDayHour;
        $workTimeMin = Yii::$app->request->post('workTimeMin') ?? 0;

        $dateYesterday = date('Y-m-d', strtotime($date . '-1 day'));

        // Заказы могут заканчиваться следующим днем, поэтому вытаскиваем за два дня
        $query = Orders::find()->where(['in', 'date', [$date, $dateYesterday]])
            ->andWhere(['post' => $post]);
        // Исключаем текущий заказ, для показа свободных интервалов
        if (!empty($currentOrderId)) {
            $query->andWhere(['not', ['id' => $currentOrderId]]);
        }
        $orders = $query->all();

        $calculator = new FreeTimeCalculate($cwId);
        $intervals = $calculator->getFreeTimeIntervals($date, $orders, $startAt, $endAt, $workTimeMin, $post);

        return ['result' => true, 'intervals' => $intervals];
    }

    /**
     * @throws \Exception
     */
    public function actionGetFreeTimesByPosts()
    {
        $cwId = Yii::$app->request->post('cwId');
        $pId = Yii::$app->request->post('pId');
        $workTimeMin = Yii::$app->request->post('workTimeMin');
        $date = date('Y-m-d', strtotime(Yii::$app->request->post('date')));
        $dayOfWeek = date('w', strtotime($date));

        $carwash = Carwash::findOne($cwId);
        $personal = Personal::findOne($pId);
        if (!$carwash || !$personal) {
            return ['result' => false, 'message' => 'Не найдены заданные модели'];
        }
        if ($personal->carwash_id !== $carwash->id) {
            return ['result' => false, 'message' => 'В доступе отказано'];
        }
        $schedule = CarwashSchedule::findOne(['carwash_id' => $cwId]);
        $settings = CarwashSettings::findOne(['carwash_id' => $cwId]);
        if (!$schedule || !$settings) {
            return ['result' => false, 'message' => 'Не настроено первичное расписание / настройки'];
        }
        $timezone = $carwash->timezone;
        $currentTimeWithTimezone = new \DateTime();
        $currentTimeWithTimezone->setTimezone(new \DateTimeZone('+0' . $timezone . '00'));


        $startDayHour = $schedule->getStartDayTimeByDay($dayOfWeek);
        $endDayHour = $schedule->getEndDayTimeByDay($dayOfWeek);

        $dateYesterday = date('Y-m-d', strtotime($date . '-1 day'));

        // Заказы могут заканчиваться следующим днем, поэтому вытаскиваем за два дня
        $orders = Orders::find()->where(['in', 'date', [$date, $dateYesterday]])->all();

        /**
         * @var Period[]|null $freePeriods
         */
        $freeTimeCalculate = new FreeTimeCalculate($carwash->id);
        return $freeTimeCalculate->calculateFreePeriodsByPosts($date, $startDayHour, $endDayHour, $orders, $workTimeMin);
    }

    public function actionDelete()
    {
        $orderId = Yii::$app->request->post('orderId');
        $pId = Yii::$app->request->post('pId');

        $personal = Personal::findOne($pId);
        $order = Orders::findOne($orderId);
        if (!$personal || !$order) {
            return ['result' => false, 'message' => 'Заказ не найден'];
        }
        if ($order->carwash_id != $personal->carwash_id) {
            return ['result' => false, 'message' => 'Нет  доступа к данному заказу'];
        }
        OrderService::deleteAll(['order_id' => $orderId]);
        try {
            $notify = new NotifyHelper();
            $notify->trigger(PersonalNotification::EVENT_CANCEL_ORDER, ['orderId' => $orderId, 'initiatorPersonalId' => $pId, 'isPersonalDelete' => true]);

            if ($order->delete()) {
                return ['result' => true];
            }
        } catch (\Exception $exception) {
        }
        return ['result' => false, 'message' => 'Не удалось удалить заказ'];
    }

    public function actionChangeStatus()
    {
        $orderId = Yii::$app->request->post('orderId');
        $pId = Yii::$app->request->post('pId');
        $status = Yii::$app->request->post('status');

        $personal = Personal::findOne($pId);
        $order = Orders::findOne($orderId);
        if (!$personal || !$order) {
            return ['result' => false, 'message' => 'Заказ не найден'];
        }
        if ($order->carwash_id != $personal->carwash_id) {
            return ['result' => false, 'message' => 'Нет  доступа к данному заказу'];
        }

        if (!in_array($status, [Orders::STATUS_NEW_FROM_CLIENT, Orders::STATUS_NEW_FROM_WASH, Orders::STATUS_WORK,
            Orders::STATUS_ARCHIVE, Orders::STATUS_REMOVED], false)){
            return ['result' => false, 'message' => 'Не найден подходящий статус к заказу'];
        }

        $order->status = $status;
        try {
            if ($order->save()) {
                return ['result' => true];
            }
        } catch (\Exception $exception) {
        }
        return ['result' => false, 'message' => 'Не удалось удалить заказ'];
    }

    /**
     * @return array
     */
    public function actionChangeOrderPersonal()
    {
        $orderId = Yii::$app->request->post('orderId');
        $pId = Yii::$app->request->post('pId');
        $selectedPersonalId = Yii::$app->request->post('selectedPersonal');

        $personal = Personal::findOne($pId);
        $selectedPersonal = Personal::findOne($selectedPersonalId);
        $order = Orders::findOne($orderId);
        if (!$personal || !$order || !$selectedPersonal) {
            return ['result' => false, 'message' => 'Функционал недоступен'];
        }
        if ($order->carwash_id != $personal->carwash_id) {
            return ['result' => false, 'message' => 'Нет  доступа к данному заказу'];
        }
        if ($order->carwash_id != $selectedPersonal->carwash_id) {
            return ['result' => false, 'message' => 'У выбранного пользователя нет доступа к данному заказу'];
        }
        $order->personal_id = $selectedPersonal->id;
        $order->personal_fullname = $selectedPersonal->getShortUsername();
        $order->save();

        return ['result' => true, 'message' => 'Пользователь успешно обновлен'];
    }


    public function actionAddClientToBlacklist()
    {
        $clientId = Yii::$app->request->post('clientId');
        $cwId = Yii::$app->request->post('cwId');
        $pId = Yii::$app->request->post('pId');

        $client = Clients::findOne($clientId);
        if (!$client) {
            return ['result' => false, 'message' => 'Не найден клиент'];
        }
        $blacklist = CarwashBlacklist::findOne(['client_id' => $clientId, 'carwash_id' => $cwId]);
        if (!$blacklist) {
            $lastClientOrder = Orders::find()->where(['client_id' => $client->id, 'carwash_id' => $cwId])->orderBy('date DESC')->one();
            if (!$lastClientOrder) {
                return ['result' => false, 'message' => 'Не найдены заказы у клиента на выбранной автомойке'];
            }
            $blacklist = new CarwashBlacklist();
            $blacklist->client_id = $client->id;
            $blacklist->carwash_id = $cwId;
            $blacklist->car_number = (string)$lastClientOrder->car_number ?? 'A111AA';
            $blacklist->car_region = (string)$lastClientOrder->car_region ?? "";
            if ($blacklist->save()) {
                return ['result' => true, 'message' => 'Клиент добавлен в черный список'];
            }
            return ['result' => false, 'errpr' => $blacklist->car_region, 'message' => 'Не найдены заказы у клиента на выбранной автомойке'];
        }
        return ['result' => false, 'message' => 'Пользователь уже в черном списке'];
    }


    /**
     * @param Orders $order
     * @return array
     */
    private function getOrderShortTextArray(Orders $order): array
    {
        $orderTextArray = [];
        if ($order->car_type) {
            $orderTextArray[] = CarTypeHelper::getLabelType($order->car_type);
        }
        if ($order->color) {
            $orderTextArray[] = 'Цвет: ' . $order->color;
        }
        if ($order->carBrand) {
            $orderTextArray[] = 'Марка: ' . $order->carBrand->title ?? 'не указан';
        }
        if ($order->carModel) {
            $orderTextArray[] = 'Модель: ' . $order->carModel->title ?? 'не указана';
        }
        if ($order->client_fullname) {
            $orderTextArray[] = 'Клиент: ' . $order->client_fullname ?? '---';
        }
        if ($order->client_phone) {
            $orderTextArray[] = 'Телефон: ' . $order->client_phone ?? '---';
        }

        return $orderTextArray;
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