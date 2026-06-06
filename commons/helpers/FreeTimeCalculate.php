<?php

namespace app\commons\helpers;


use app\models\ar\carwash\Carwash;
use app\models\ar\carwash\CarwashSettings;
use app\models\ar\order\Orders;
use Spatie\Period\Period;
use Spatie\Period\PeriodCollection;
use Spatie\Period\Precision;
use Spatie\Period\Visualizer;


/**
 * Class FreeTimeCalculate
 * @package app\commons\helpers
 */
class FreeTimeCalculate
{
    public $carwashId = null;
    public $isDenseRecords = false;
    public $averageWorkTime = 0;
    public $postCount = 1;
    public $carwashSettings;
    public $timezone = 3;

    /**
     * FreeTimeCalculate constructor.
     * @param $carwashId
     */
    public function __construct($carwashId)
    {
        $carwash = Carwash::findOne($carwashId);
        $settings = CarwashSettings::findOne(['carwash_id' => $carwashId]);
        $this->carwashId = $carwashId;
        $this->carwashSettings = $settings;
        $this->averageWorkTime = $settings->average_duration ?? 0;
        $this->postCount = $settings->post_count ?? 1;
        $this->isDenseRecords = $settings->dense_record ?? false;
        $this->timezone = $carwash->timezone ?? 3;
    }

    /**
     * TODO: Добавить вывод блока доступных интервалов в автоматической записи
     * @param $date
     * @param $orders
     * @param $startAt
     * @param $endAt
     * @param $workTimeMin
     * @param $post
     * @return array
     * @throws \Exception
     */
    public function getFreeTimeIntervals($date, $orders, $startAt, $endAt, $workTimeMin, $post): array
    {
        $workPeriods = [];
        foreach ($orders as $order) {
            $orderEndDate = $order->date;

            // поля времени хранят в себе инт-значения минут записи
            if ($order->start_time > $order->end_time || $order->end_time == TimeHelper::MINUTES_PER_DAY) {
                $orderEndDate = date('Y-m-d', strtotime($order->date . "+1 days"));
            }

            $workPeriods[$order->post][] = Period::make($order->date . ' ' . TimeHelper::convertMinToTime($order->start_time) . ':00',
                $orderEndDate . ' ' . TimeHelper::convertMinToTime($order->end_time) . ':00', Precision::MINUTE);
        }

        $workTimeCheckPeriod = Period::make($date . ' ' . $startAt, $date . ' ' . $endAt, Precision::MINUTE);

        $freePeriodsArray = [];
        $collections = [];


        // Если на посте есть записи - передаем их в коллекцию
        foreach ($workPeriods as $period) {
            if (isset($period)) {
                $collections[] = new PeriodCollection(...$period);
            } else {
                $collections[] = new PeriodCollection();
            }
        }

        // Ищем пересечения по времени (точно занятое время, на которое невозможно записаться)
        $overlap = (new PeriodCollection($workTimeCheckPeriod))->overlap(...$collections);
        $overlapPeriods = [];
        for ($i = 0; $i < $overlap->count(); $i++) {
            $overlapPeriods[] = $overlap->current();
            $overlap->next();
        }


        $freePeriods = $workTimeCheckPeriod->diff(...$overlapPeriods);

        if ($freePeriods instanceof PeriodCollection) {
            $freePeriods->rewind();
            for ($i = 0; $i < $freePeriods->count(); $i++) {
                $freePeriodsArray[] = $freePeriods->current();
                $freePeriods->next();
            }
        }
        if (!$freePeriodsArray) {
            $freePeriodsArray[] = $workTimeCheckPeriod;
        }
        $resultArray = [];

        /** @var Period $period */
        foreach ($freePeriodsArray as $period) {
            $startTime = $period->getStart()->format('H:i');
            $endTime = $period->getEnd()->format('H:i');

            $resultArray[] = $startTime . ' - ' . $endTime;
        }

        return $resultArray ?? [];
    }

    /**
     * Вычисляем свободное время для записи с разделением по постам
     * @param $date - eg. 2021-06-13
     * @param $startAt - eg. 21:20:00
     * @param $endAt - eg. 22:30:00
     * @param Orders[] $orders
     * @param int $workTimeMin
     * @return array
     * @throws \Exception
     */
    public function calculateFreePeriodsByPosts($date, $startAt, $endAt, $orders, $workTimeMin, $considerLeftTime = false): array
    {
        $workPeriods = [];
        foreach ($orders as $order) {
            $orderEndDate = $order->date;

            // поля времени хранят в себе инт-значения минут записи
            if ($order->start_time > $order->end_time || $order->end_time == TimeHelper::MINUTES_PER_DAY) {
                $orderEndDate = date('Y-m-d', strtotime($order->date . "+1 days"));
            }

            $workPeriods[$order->post][] = Period::make($order->date . ' ' . TimeHelper::convertMinToTime($order->start_time) . ':00',
                $orderEndDate . ' ' . TimeHelper::convertMinToTime($order->end_time) . ':00', Precision::MINUTE);
        }

        $workTimeCheckPeriod = Period::make($date . ' ' . $startAt, $date . ' ' . $endAt, Precision::MINUTE);


        $freePeriodsArray = [];
        foreach (range(1, $this->postCount) as $post) {
            $collections = []; // коллекция записей на посту

            // Если на посте есть записи - передаем их в коллекцию
            if (isset($workPeriods[$post])) {
                $collections[] = new PeriodCollection(...$workPeriods[$post]);
            } else {
                $collections[] = new PeriodCollection();
            }

            // Ищем пересечения по времени (точно занятое время, на которое невозможно записаться)
            $overlap = (new PeriodCollection($workTimeCheckPeriod))->overlap(...$collections);
            $overlapPeriods = [];
            for ($i = 0; $i < $overlap->count(); $i++) {
                $overlapPeriods[] = $overlap->current();
                $overlap->next();
            }

            $freePeriods = $workTimeCheckPeriod->diff(...$overlapPeriods);

            if ($freePeriods instanceof PeriodCollection) {
                $freePeriods->rewind();
                for ($i = 0; $i < $freePeriods->count(); $i++) {
                    $freePeriodsArray[$post][] = $freePeriods->current();
                    $freePeriods->next();
                }
            }
        }

        // Получаем читабельное время с разбивкой по постам и часам
        $freeTimeIntervals = [];
        foreach ($freePeriodsArray as $postId => $freePeriods) {
            $freeTimeIntervals[$postId] = $this->reformTimePeriodByFiveMins($freePeriods, $workTimeMin, $date, $considerLeftTime);
        }


        // Сливаем данные в единый массив со свободным временем, уникализируем значения
        $resultArray = [];
        for ($post = 1; $post <= $this->postCount; $post++) {
            $postIntervals = $freeTimeIntervals[$post];
            foreach ($postIntervals as $hour => $minuteInterval) {
                $hour = (string)$hour;
                if (!empty($resultArray[$hour])) {
                    $resultArray[$hour] = array_unique(array_merge($resultArray[$hour], $minuteInterval));
                } else {
                    $resultArray[$hour] = array_unique($minuteInterval);
                }
                sort($resultArray[$hour]);
            }
        }

        ksort($resultArray);
        return $resultArray;
    }


    /**
     * Устаревший метод для поиска свободных периодов. Не учитывал разбирание на посты
     * @param $date
     * @param $startAt
     * @param $endAt
     * @param $orders
     * @param $countPosts
     * @return array|Period[]|PeriodCollection
     */
    public function calculateGroupedFreePeriods($date, $startAt, $endAt, $orders, $countPosts)
    {
        $workPeriods = [];
        foreach ($orders as $order) {
            $orderEndDate = $order->date;

            // поля времени хранят в себе инт-значения минут записи
            if ($order->start_time > $order->end_time || $order->end_time == TimeHelper::MINUTES_PER_DAY) {
                $orderEndDate = date('Y-m-d', strtotime($order->date . "+1 days"));
            }

            $workPeriods[$order->post][] = Period::make($order->date . ' ' . TimeHelper::convertMinToTime($order->start_time) . ':00',
                $orderEndDate . ' ' . TimeHelper::convertMinToTime($order->end_time) . ':00', Precision::MINUTE);
        }

        $workTimeCheckPeriod = Period::make($date . ' ' . $startAt, $date . ' ' . $endAt, Precision::MINUTE);


        $collections = [];
        foreach (range(1, $countPosts) as $post) {
            // Если на посте есть записи - передаем их в коллекцию
            if (isset($workPeriods[$post])) {
                $collections[] = new PeriodCollection(...$workPeriods[$post]);
            } else {
                $collections[] = new PeriodCollection();
            }
        }
        // Ищем пересечения по времени (точно занятое время, на которое невозможно записаться)
        $overlap = (new PeriodCollection($workTimeCheckPeriod))->overlap(...$collections);
        $overlapPeriods = [];
        for ($i = 0; $i < $overlap->count(); $i++) {
            $overlapPeriods[] = $overlap->current();
            $overlap->next();
        }

        $freePeriods = $workTimeCheckPeriod->diff(...$overlapPeriods);


        $visualizer = new Visualizer(["width" => 100]);
        $a = $visualizer->visualize($collections + [
                "WORKTIME" => $workTimeCheckPeriod,
                "OVERLAP" => $overlap,
                "GAPS" => $freePeriods,
            ]);

//        echo $a;
//        die();

        // diff может вернуть как коллекцию, так и изначальный массив периодов
        if ($freePeriods instanceof PeriodCollection) {
            $freePeriodsArray = [];
            $freePeriods->rewind();
            for ($i = 0; $i < $freePeriods->count(); $i++) {
                $freePeriodsArray[] = $freePeriods->current();
                $freePeriods->next();
            }

            return $freePeriodsArray;
        }

        return $freePeriods;
    }

    /**
     * Формируем массив свободного времени, с учетом текущего времени,
     * временной зоны и разбивкой по пять минут с округлением
     * @param Period[] $freePeriods
     * @param int $workTimeMin
     * @param $selectedDate
     * @param boolean $considerLeftTime
     * @return array
     * @throws \Exception
     */
    private function reformTimePeriodByFiveMins(array $freePeriods, int $workTimeMin, $selectedDate, $considerLeftTime = false)
    {
        $selectedDate = new \DateTime($selectedDate);
        $timezone = $this->timezone;
        $currentTimeWithTimezone = new \DateTime();
        $currentTimeWithTimezone->setTimezone(new \DateTimeZone('+0' . $timezone . '00'));

        // Если выбранный день не равен текущему, то проверка по прошедшему времени не нужна
        if ($selectedDate->format('Y-m-d') !== $currentTimeWithTimezone->format('Y-m-d')) {
            $currentTimeWithTimezone->setTime(00, 00, 01);
        }

        $freeTimeInterval = []; // key - hour; value - minutes
        foreach ($freePeriods as $key => $freePeriod) {
            $startPeriodTime = $freePeriod->getStart();
            $endPeriodTime = $freePeriod->getEnd();
            if (!empty($workTimeMin)) {
                $endPeriodTime = $endPeriodTime->modify('-' . $workTimeMin . ' min');
            }

            if ($startPeriodTime->format('i') % 5 == 1) {
                $startPeriodTime = $startPeriodTime->modify('-1 min');
            }
            if ($startPeriodTime->format('i') % 5 == 2) {
                $startPeriodTime = $startPeriodTime->modify('-2 min');
            }
            if ($startPeriodTime->format('i') % 5 == 3) {
                $startPeriodTime = $startPeriodTime->modify('-3 min');
            }
            if ($startPeriodTime->format('i') % 5 == 4) {
                $startPeriodTime = $startPeriodTime->modify('+1 min');
            }

            if ($endPeriodTime->format('i') % 5 == 4) {
                $endPeriodTime = $endPeriodTime->modify('+1 min');
            }
            if ($endPeriodTime->format('i') % 5 == 3) {
                $endPeriodTime = $endPeriodTime->modify('+2 min');
            }
            if ($endPeriodTime->format('i') % 5 == 2) {
                $endPeriodTime = $endPeriodTime->modify('+3 min');
            }
            if ($endPeriodTime->format('i') % 5 == 1) {
                $endPeriodTime = $endPeriodTime->modify('+4 min');
            }

            if ($startPeriodTime > $endPeriodTime) {
                continue;
            }


            if ($this->isDenseRecords) {
                $tempTime = clone $startPeriodTime;

                $countSteps = 0;

                $tempEndTime = clone $endPeriodTime;
                $tempEndTime = $tempEndTime->modify('+' . $workTimeMin . ' min');
                while ($tempTime <= $tempEndTime) {
                    if ($countSteps == 1) {
                        // Первый отрезок - это с большей вероятностью начало дня, начало времени работы автомойки.
                        if ($key !== array_key_first($freePeriods)) {
                            $tempTime = $tempTime->modify('+' . $this->averageWorkTime . ' min');
                        }
                        // Последний отрезок - это с большей вероятностью конец дня, окончание времени работы автомойки.
                        if ($key !== array_key_last($freePeriods)) {
                            $endPeriodTime = $endPeriodTime->modify('-' . $this->averageWorkTime . ' min');
                        }
                    }

                    $tempHour = $tempTime->format('H');

                    // Если отрезок больше текущего времени с учетом таймзоны - добавляем его в поле
                    if ( $tempTime <= $endPeriodTime &&
                        ($considerLeftTime || $tempTime->format('H:i') >= $currentTimeWithTimezone->format('H:i'))) {
                        $freeTimeInterval[$tempHour][] = $tempTime->format('i');
                    }
                    $countSteps++;

                    $tempTime = $tempTime->modify('+5 min');
                    // Проверка, является ли отрезок последним для данного периода. (для реализации прилипания записи к нижней)
                    if ($tempTime >= $tempEndTime) {
                        $lastOrderEndTime = $freePeriod->getEnd();
                        if ($lastOrderEndTime->format('i') % 5 == 4) {
                            $lastOrderEndTime = $lastOrderEndTime->modify('+1 min');
                        }
                        if ($lastOrderEndTime->format('i') % 5 == 3) {
                            $lastOrderEndTime = $lastOrderEndTime->modify('+2 min');
                        }
                        if ($lastOrderEndTime->format('i') % 5 == 2) {
                            $lastOrderEndTime = $lastOrderEndTime->modify('+3 min');
                        }
                        if ($lastOrderEndTime->format('i') % 5 == 1) {
                            $lastOrderEndTime = $lastOrderEndTime->modify('+4 min');
                        }
//                        if ($tempTime > $tempEndTime) {
//                            $lastOrderEndTime = $lastOrderEndTime->modify('-10 min');
//                        } else {
//                            $tempTime = $tempTime->modify('+1 min');
//                            $lastOrderEndTime = $lastOrderEndTime->modify('-0 min');
//                        }
                        $lastOrderEndTime = $lastOrderEndTime->modify('-' . $workTimeMin . ' min');
                        $tempHour = $lastOrderEndTime->format('H');
                        if ($considerLeftTime || $lastOrderEndTime->format('H:i') >= $currentTimeWithTimezone->format('H:i')) {
                            $freeTimeInterval[$tempHour][] = $lastOrderEndTime->format('i');
                        }
                    }
                }

            } else {
                $tempTime = clone $startPeriodTime;
                while ($tempTime <= $endPeriodTime) {
                    $tempHour = $tempTime->format('H');

                    // Если отрезок больше текущего времени с учетом таймзоны - добавляем его в поле
                    if ($considerLeftTime || $tempTime->format('H:i') >= $currentTimeWithTimezone->format('H:i')) {
                        $freeTimeInterval[$tempHour][] = $tempTime->format('i');
                    }
                    $tempTime = $tempTime->modify('+5 min');
                }
            }
        }
        return $freeTimeInterval;
    }


    /**
     * @param $date
     * @param $startOrderTimeMin
     * @param $endOrderTimeMin
     * @param $post
     * @param int|false $currentOrderId
     * @return bool
     */
    public function checkIntersectionsOrdersByPost($date, $startOrderTimeMin, $endOrderTimeMin, $post, $currentOrderId = false)
    {
        $isNeedTomorrow = false;
        $orderEndDate = $date;
        if ($startOrderTimeMin > $endOrderTimeMin || $endOrderTimeMin == TimeHelper::MINUTES_PER_DAY) {
            $isNeedTomorrow = true;
            $orderEndDate = date('Y-m-d', strtotime($date . " +1 days"));
        }

        $query = Orders::find()->where(['carwash_id' => $this->carwashId, 'post' => $post]);
        if ($isNeedTomorrow) {
            $query->andWhere(['in', 'date', [date('Y-m-d', strtotime($date)), $orderEndDate]]);
        } else {
            $query->andWhere(['date' => $date]);
        }
        if ($currentOrderId) {
            $query->andWhere(['not', ['id' => $currentOrderId]]);
        }
        $ordersForCheck = $query->all();
        $needCheckOrder = Period::make($date . ' ' . TimeHelper::convertMinToTime($startOrderTimeMin) . ':01',
            $orderEndDate . ' ' . TimeHelper::convertMinToTime($endOrderTimeMin) . ':00', Precision::SECOND);

        $ordersCollections = [];
        foreach ($ordersForCheck as $order) {
            $orderEndDate = $order->date;

            // поля времени хранят в себе инт-значения минут записи
            if ($order->start_time > $order->end_time || $order->end_time == TimeHelper::MINUTES_PER_DAY) {
                $orderEndDate = date('Y-m-d', strtotime($order->date . " +1 days"));
            }

            $periods = Period::make($order->date . ' ' . TimeHelper::convertMinToTime($order->start_time) . ':01',
                $orderEndDate . ' ' . TimeHelper::convertMinToTime($order->end_time) . ':00', Precision::SECOND);
            $ordersCollections[] = new PeriodCollection($periods);
        }

        $isIntersect = false;
        foreach ($ordersCollections as $ordersCollection) {
            $overlap = (new PeriodCollection($needCheckOrder))->overlapSingle($ordersCollection)->isEmpty();
            if (!$overlap) {
                $isIntersect = true;
            }
        }
        return $isIntersect;
    }
}