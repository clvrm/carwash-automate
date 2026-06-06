<?php

namespace app\commons\notification\triggers;

use app\commons\exceptions\NotifyException;
use app\commons\helpers\TimeHelper;
use app\models\ar\carwash\CarwashSchedule;
use app\models\ar\chat\Chat;
use app\models\ar\order\Orders;
use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalNotification;

class TriggerReportGenerated extends NotifyTrigger
{
    protected $eventName = PersonalNotification::EVENT_NEW_SALES_REPORT;

    public function prepare()
    {
        $orders = Orders::find()->where(['carwash_id' => $this->carwashId, 'date' => date('Y-m-d', strtotime('-1 days'))])->all();
        $totalOrders = count($orders) ?? 0;
        $totalPrices = 0;

        foreach ($orders as $order){
            $totalPrices += $order->total_price ?? 0;
        }

        $this->defaultTitle = $this->pushTitle = 'Отчет сформирован';
        $this->defaultMessage = $this->pushMessage = 'За ' . date('Y-m-d', strtotime('-1 days')). "\nЗаказов: " .$totalOrders . " шт. \n" ."Поступления: " .$totalPrices ." руб." ;

    }
}