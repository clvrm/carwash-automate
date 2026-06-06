<?php


namespace app\commons\helpers;


use app\models\ar\carwash\Carwash;
use app\models\ar\carwash\CarwashSettings;
use app\models\ar\order\Orders;
use Spatie\Period\Period;
use Spatie\Period\Precision;

class AutoOrder
{
    private $carwashSettings = null;
    private $carwash = null;
    private $date = null;
    private $startTime = null;
    private $workTimeMin = null;
    private $endTime = null;

    public function __construct($carwashId, $date, $startTime, $workTime)
    {
        $this->carwash = Carwash::findOne($carwashId);
        $this->carwashSettings = CarwashSettings::findOne(['carwash_id' => $carwashId]);
        $this->date = $date;
        $this->startTime = $startTime;
        $this->workTimeMin = $workTime;

        $this->endTime = $this->calculateEndTime();
    }

    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Поиск оптимального поста для мойки, исходя из промежуточных заказов на постах
     * @return int|string
     */
    public function findOptimalPost()
    {
        $startTimeMinutes = TimeHelper::convertTimeToMin($this->startTime);
        $endTimeMinutes = TimeHelper::convertTimeToMin($this->endTime);

        $maxDate = date('Y-m-d', strtotime($this->date . "+1 days"));
        $orders = Orders::find()
            ->where(['date' => $this->date])
            ->orWhere(['date' => $maxDate])->orderBy('post')->all();
        $postOrders = [];
        foreach ($orders as $order) {
            $postOrders[$order->post][] = $order;
        }
        $totalPosts = $this->carwashSettings->post_count;
        foreach (range(1, $totalPosts) as $post) {
            $orders = isset($postOrders[$post]) ? $postOrders[$post] : [];
            $postPeriods = [];
            foreach ($orders as $order) {
                $orderEndDate = $order->date;

                // поля времени хранят в себе инт-значения минут записи
                if ($order->start_time > $order->end_time) {
                    $orderEndDate = date('Y-m-d', strtotime($order->date . "+1 days"));
                }
                $postPeriods[] = Period::make($order->date . ' ' . TimeHelper::convertMinToTime($order->start_time) . ':01',
                    $orderEndDate . ' ' . TimeHelper::convertMinToTime($order->end_time) . ':00', Precision::SECOND);
            }
            $orderEndDate = $this->date;
            if (strtotime($this->startTime) > strtotime($this->endTime)) {
                $orderEndDate = date('Y-m-d', strtotime($this->date . "+1 days"));
            }
            // Поправка на одну секунду потому, что иначе будет соприкосновение дат. Секунда не влияет на нормализацию поиска
            $autoOrderPeriod = Period::make($this->date . ' ' . $this->startTime . ':01',
                $orderEndDate . ' ' . $this->endTime . ':00', Precision::SECOND);
            // Если на данном посту нет записи - отлично
            if (empty($postPeriods)) {
                return $post;
            }

            $overlap = $autoOrderPeriod->overlap(...$postPeriods);
            // Если пересечений нет - значит записи сформированы правильно. Разрешаем записаться на данное время
            if ($overlap->isEmpty()) {
                return $post;
            }
        }

        return false;
    }

    private function calculateEndTime()
    {
        $time = new \DateTime($this->date . ' ' . $this->startTime);
        $time->add(new \DateInterval('PT' . $this->workTimeMin . 'M'));
        return $time->format('H:i');
    }

}