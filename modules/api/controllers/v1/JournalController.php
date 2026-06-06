<?php

namespace app\modules\api\controllers\v1;

use app\commons\helpers\CarTypeHelper;
use app\commons\helpers\TimeHelper;
use app\models\ar\car\CarBrands;
use app\models\ar\carwash\Carwash;
use app\models\ar\carwash\CarwashSchedule;
use app\models\ar\carwash\CarwashSettings;
use app\models\ar\order\Orders;
use app\models\ar\personal\Personal;
use app\modules\api\commons\ApiHelper;
use app\modules\api\commons\v1\ReturnCodes;
use yii\web\Controller;
use Yii;


class JournalController extends Controller
{

    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }


    public function actionOrdersList()
    {
        $cwId = Yii::$app->request->post('cwId');
        $pId = Yii::$app->request->post('pId');
        $token = Yii::$app->request->post('token');
        $date = date('Y-m-d', strtotime(Yii::$app->request->post('date')));

        $carwash = Carwash::findOne($cwId);
        $personal = Personal::findOne($pId);
        if (!$carwash || !$personal) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);

            return [
                'date' => $date,
                'orders' => [],
                'message' => 'Не найдены автомойка или сотрудник'
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

        if ($personal->carwash_id !== $carwash->id) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ACCESS_FORBIDDEN);

            return [
                'date' => $date,
                'orders' => [],
                'message' => 'В доступе отказано'
            ];
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
            $carBrand = '';
            $carModel = [];


            $ordersArray[] = [
                'id' => $order->id,
                'clientId' => $order->client_id,
                'carNumber' =>  $order->car_number ?? 'A123AA',
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
                'color' => $order->color,
                'carType' => CarTypeHelper::getLabelType($order->car_type),
                'brandTitle' => $order->carBrand->title ?? '',
                'modelTitle' => $order->carModel->title ?? '',
                'clientFullname' => $order->client_fullname ?? '',
                'clientPhone' => $order->client_phone ?? '',
            ];
        }
        return ['date' => $date, 'orders' => $ordersArray];
    }

    public function actionSettings()
    {
        $cwId = Yii::$app->request->post('cwId');
        $pId = Yii::$app->request->post('pId');
        $date = Yii::$app->request->post('date');
        $dayOfWeek = date('w', strtotime($date));

        $carwash = Carwash::findOne($cwId);
        $personal = Personal::findOne($pId);
        if (!$carwash || !$personal) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);

            return [
                'settings' => [],
                'message' => 'Не найдены автомойка или сотрудник'
            ];
        }
        if ($personal->carwash_id !== $carwash->id) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ACCESS_FORBIDDEN);

            return [
                'settings' => [],
                'message' => 'В доступе отказано'
            ];
        }
        $settings = CarwashSettings::findOne(['carwash_id' => $cwId]);
        $schedule = CarwashSchedule::findOne(['carwash_id' => $cwId]);
        if (!$settings || !$schedule) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);

            return [
                'settings' => [],
                'message' => 'Не найдены настройки автомойки'
            ];
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
        $startDayMin = TimeHelper::convertTimeToMin($schedule->getStartDayTimeByDay($dayOfWeek));
        $endDayMin = TimeHelper::convertTimeToMin($schedule->getEndDayTimeByDay($dayOfWeek));
        if ($endDayMin == 1439) {
            $endDayMin = 1440;
        }

        $resultArray['startDayHour'] = (int)$startDayHour;
        $resultArray['startDayMin'] = $startDayMin ?? 0;
        $resultArray['endDayHour'] = (int)$endDayHour;
        $resultArray['endDayMin'] = $endDayMin ?? 1440;
        $resultArray['isWorkDay'] = $isWorkDay;
        $resultArray['maxRecordRange'] = $settings->max_recording_range ?? 30;

        return [
            'settings' => (object)$resultArray
        ];
    }

    public function actionUpdateOrder()
    {
        $cwId = Yii::$app->request->post('cwId');
        $pId = Yii::$app->request->post('pId');
        $token = Yii::$app->request->post('token');
        $orderId = Yii::$app->request->post('orderId');
        $post = Yii::$app->request->post('post');
        $startAt = Yii::$app->request->post('startAt'); // eg. 10:00
        $endAt = Yii::$app->request->post('endAt'); // eg. 14:00
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
        $carwash = Carwash::findOne($cwId);
        $personal = Personal::findOne($pId);
        if (!$carwash || !$personal) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);

            return [
                'result' => false,
                'errors' => [
                    'pId' => 'Сотрудник не найден'
                ],
                'message' => 'Не найдены заданные модели'
            ];
        }
        if ($personal->carwash_id !== $carwash->id) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ACCESS_FORBIDDEN);

            return [
                'result' => false,
                'errors' => [
                    'pId' => 'В доступе отказано'
                ],
                'message' => 'В доступе отказано'
            ];
        }
        $order = Orders::findOne($orderId);
        if (!$order || $order->carwash_id != $personal->carwash_id) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);

            return [
                'result' => false,
                'errors' => [
                    'orderId' => 'Заказ не найден на данной автомойке'
                ],
                'message' => 'Заказ не найден на данной автомойке'
            ];
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
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);

            return [
                'result' => false,
                'errors' => [
                    'cwId' => 'Не удалось найти настройки автомойки'
                ],
                'message' => 'Не удалось найти настройки автомойки'
            ];
        }
        if ($post < 1 || $post > $carwashSettings->post_count) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ATTRIBUTES_ERROR);

            return [
                'result' => false,
                'errors' => [
                    'post' => 'Неверно указан пост записи'
                ],
                'message' => 'Неверно указан пост записи'
            ];
        }
        // Время задержки персонала
        $staffDelayTime = 0;
        if ($carwashSettings->until_last_client) {
            $staffDelayTime = $carwashSettings->staff_delay_time ?? 0;
        }

        $order->start_time = $startTime;

        $startWeekDay = date('w', strtotime($order->date));
        $endWeekDay = date('w', strtotime($order->date));
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
        if (!$order->save()) {
            \Yii::$app->response->setStatusCode(ReturnCodes::SERVER_ERROR);

            return [
                'result' => false,
                'errors' => $order->errors,
                'message' => 'Произошла ошибка при записи изменений заказа'
            ];
        }

        return [
            'result' => true,
            'message' => 'Заказ обновлен'
        ];
    }

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


}
