<?php

namespace app\modules\api\controllers\v1;

use app\commons\exceptions\LogicException;
use app\commons\helpers\FreeTimeCalculate;
use app\commons\helpers\TimeHelper;
use app\models\ar\car\CarBrands;
use app\models\ar\car\CarModels;
use app\models\ar\carwash\Carwash;
use app\models\ar\carwash\CarwashSchedule;
use app\models\ar\carwash\CarwashSettings;
use app\models\ar\order\Orders;
use app\models\ar\personal\Personal;
use app\modules\api\commons\v1\ReturnCodes;
use yii\web\Controller;
use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class CommonController extends Controller
{
    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    public function actionFreeTimeIntervalsAllPosts()
    {
        $cwId = Yii::$app->request->post('cwId');
        $currentOrderId = Yii::$app->request->post('currentOrderId');
        $date = date('Y-m-d', strtotime(Yii::$app->request->post('date')));
        $considerLeftTime = filter_var(Yii::$app->request->post('considerLeftTime', false), FILTER_VALIDATE_BOOLEAN);
        $dayOfWeek = date('w', strtotime($date));
        $carwash = Carwash::findOne($cwId);
        if (!$carwash) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'intervals' => [],
                'message' => 'Не найдена автомойка',
            ];
        }
        $schedule = CarwashSchedule::findOne(['carwash_id' => $cwId]);
        $settings = CarwashSettings::findOne(['carwash_id' => $cwId]);
        if (!$settings || !$schedule) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'intervals' => [],
                'message' => 'Не настроено первичное расписание / настройки',
            ];
        }
        if (!$schedule->isWorkDay($dayOfWeek)) {
            return [
                'intervals' => [],
                'message' => 'Выбран нерабочий день',
            ];
        }
        $timezone = $carwash->timezone;
        $currentTimeWithTimezone = new \DateTime();
        $currentTimeWithTimezone->setTimezone(new \DateTimeZone('+0' . $timezone . '00'));

        $startDayHour = $schedule->getStartDayTimeByDay($dayOfWeek);
        $endDayHour = $schedule->getEndDayTimeByDay($dayOfWeek);

        $startAt = $startDayHour;
        $endAt = $endDayHour;
        $workTimeMin = \Yii::$app->request->post('workTimeMin') ?? 0;

        $dateYesterday = date('Y-m-d', strtotime($date . '-1 day'));

        // Заказы могут заканчиваться следующим днем, поэтому вытаскиваем за два дня
        $query = Orders::find()->where(['in', 'date', [$date, $dateYesterday]]);
        // Исключаем текущий заказ, для показа свободных интервалов
        if (!empty($currentOrderId)) {
            $query->andWhere(['not', ['id' => $currentOrderId]]);
        }
        $orders = $query->all();

        $calculator = new FreeTimeCalculate($cwId);
        $intervals = $calculator->calculateFreePeriodsByPosts($date, $startAt, $endAt, $orders, $workTimeMin, $considerLeftTime);

        return [
            'intervals' => $intervals,
            'message' => '',
        ];
    }


    public function actionFreeTimeIntervals()
    {
        $cwId = Yii::$app->request->post('cwId');
        $post = Yii::$app->request->post('post');
        $currentOrderId = Yii::$app->request->post('currentOrderId');
        $date = date('Y-m-d', strtotime(Yii::$app->request->post('date')));
        $dayOfWeek = date('w', strtotime($date));
        $carwash = Carwash::findOne($cwId);
        if (!$carwash) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'intervals' => [],
                'message' => 'Не найдена автомойка',
            ];
        }
        $schedule = CarwashSchedule::findOne(['carwash_id' => $cwId]);
        $settings = CarwashSettings::findOne(['carwash_id' => $cwId]);
        if (!$settings || !$schedule) {
            \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
            return [
                'intervals' => [],
                'message' => 'Не настроено первичное расписание / настройки',
            ];
        }
        if (!$schedule->isWorkDay($dayOfWeek)) {
            return [
                'intervals' => [],
                'message' => 'Выбран нерабочий день',
            ];
        }
        $timezone = $carwash->timezone;
        $currentTimeWithTimezone = new \DateTime();
        $currentTimeWithTimezone->setTimezone(new \DateTimeZone('+0' . $timezone . '00'));

        $startDayHour = $schedule->getStartDayTimeByDay($dayOfWeek);
        $endDayHour = $schedule->getEndDayTimeByDay($dayOfWeek);

        $startAt = $startDayHour;
        $endAt = $endDayHour;
        $workTimeMin = \Yii::$app->request->post('workTimeMin') ?? 0;

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

        return [
            'intervals' => $intervals,
            'message' => '',
        ];
    }

    public function actionCarBrands()
    {
        $cwId = \Yii::$app->request->get('cwId') ?? null;
        $carBrands = CarBrands::find()->select(['id', 'title', 'icon', 'synonyms'])->where(['carwash_id' => null])->orWhere(['carwash_id' => $cwId])
            ->orderBy(['title' => 'ASC'])->all();

        return [
            'items' => $carBrands
        ];
    }

    public function actionCarModels()
    {
        $cwId = \Yii::$app->request->get('cwId') ?? false;
        $carBrandId = \Yii::$app->request->get('carBrandId') ?? false;
        $search = \Yii::$app->request->get('query') ?? false;

        $query = CarModels::find()->select(['id', 'title', 'synonyms'])->where(['carwash_id' => null])->orWhere(['carwash_id' => $cwId]);
        if ($carBrandId) {
            $query->andWhere(['car_brand_id' => $carBrandId]);
        }
        if ($search) {
            $query->andWhere(['like', 'title', $search]);
        }
        $carModels = $query->orderBy(['title' => 'ASC'])->all();

        return [
            'items' => $carModels
        ];

    }

    public function actionValidateOrderTime()
    {
        $cwId = \Yii::$app->request->post('cwId') ?? false;
        $post = \Yii::$app->request->post('post');
        $orderId = \Yii::$app->request->post('orderId') ?? false;
        $date = \Yii::$app->request->post('date') ?? false;
        $startTime = \Yii::$app->request->post('startTime') ?? false;
        $endTime = \Yii::$app->request->post('endTime') ?? false;

        // TODO: Валидация, рабочий день или нет. По количеству постов

        $carwashSettings = CarwashSettings::findOne(['carwash_id' => $cwId]);

        if ($orderId) {
            $orderModel = Orders::findOne($orderId);
            if (!$orderModel) {
                \Yii::$app->response->setStatusCode(ReturnCodes::NOT_FOUND);
                return [
                    'message' => 'Не найден заказ',
                    'errors' => [
                        'orderId' => 'Заказ не найден'
                    ],
                ];
            }
            if ($orderModel->carwash_id != $cwId) {
                \Yii::$app->response->setStatusCode(ReturnCodes::ACCESS_FORBIDDEN);
                return [
                    'message' => 'Доступ запрещен',
                    'errors' => [
                        'cwId' => 'Нет доступа к данному заказу'
                    ],
                ];
            }
        }
        $startTimeMin = $startTime;
        $endTimeMin = $endTime;
        if ($startTimeMin === $endTimeMin) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ATTRIBUTES_ERROR);
            return [
                'message' => 'Время начала и окончания не может быть равно',
                'errors' => [
                    'startTime' => 'Время начала, равно времени окончания',
                    'endTime' => 'Время окончания, равно времени начала',
                ],
            ];
        }
        $timeChecker = new FreeTimeCalculate($cwId);
        $isIntersection = $timeChecker->checkIntersectionsOrdersByPost($date, $startTimeMin, $endTimeMin, $post, $orderId);
        if ($isIntersection) {
            return [
                'message' => 'Выбранное время пересекается с другим заказом на данном посте',
                'intersection' => true,
            ];
        }
        return [
            'message' => 'Выбранное время доступно для записи',
            'intersection' => false,
        ];
    }

    public function actionGetAboutInfo()
    {
        return [
            'text' => '<h2 style="text-align: center;">О приложении</h2>
<p style="text-align: center;">Мобильное приложение для CRM Carwash.</p>
<p style="text-align: center;">Для корректной работы заполните личный кабинет.</p>'
        ];
    }

    public function actionGetPolicyInfo()
    {
        return [
            'text' => '<h2 style="text-align: center;">Политика конфиденциальности</h2>
<ul>
<li style="text-align: center;">1</li>
<li style="text-align: center;">2</li>
<li style="text-align: center;">3</li>
<li style="text-align: center;">4</li>
<li style="text-align: center;">5</li>
<li style="text-align: center;">6</li>
</ul>'
        ];
    }
}
