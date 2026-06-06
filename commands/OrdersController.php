<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\commons\helpers\TimeHelper;
use app\models\ar\carwash\Carwash;
use app\models\ar\order\Orders;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Time;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\StudentT;
use yii\console\Controller;
use yii\console\ExitCode;


/**
 * Class OrdersController
 * @package app\commands
 */
class OrdersController extends Controller
{

    /**
     * Переводим активные заказы в архив, спустя время
     * @return int
     */
    public function actionConvertToArchive(): int
    {
        $neededDay = date('Y-m-d', strtotime(date('Y-m-d') . '-2 day'));
        $orders = Orders::find()->where(['<=', 'date', $neededDay])->all();
        echo 'Дата затронутых заказов <= ' . $neededDay . PHP_EOL;
        foreach ($orders as $order) {
            if (in_array($order->status, [Orders::STATUS_NEW_FROM_WASH, Orders::STATUS_NEW_FROM_CLIENT, Orders::STATUS_WORK])) {
                $order->status = Orders::STATUS_ARCHIVE;
                $order->save();
            }
        }

        return ExitCode::OK;
    }

    /**
     * Переводим заказы в рабочий статус
     * @return int
     */
    public function actionConvertToInWork(): int
    {
        $actualTimezones = array_keys((new Carwash())->getTimezonesLabels());

        foreach ($actualTimezones as $timezone) {
            $dateWithTimezone = TimeHelper::getCurrentDateBasedOnTimezone('Y-m-d',(int)$timezone);
            $timeWithTimezone = TimeHelper::getCurrentDateBasedOnTimezone('H:i',(int)$timezone);
            $normalizedTime = TimeHelper::convertTimeToMin($timeWithTimezone);
            $orders = Orders::find()->joinWith('carwash')->where(['date' => $dateWithTimezone])
                ->andWhere(['carwash.timezone' => $timezone])
                ->andWhere(['in', 'status',[Orders::STATUS_NEW_FROM_WASH, Orders::STATUS_NEW_FROM_CLIENT]])
                ->andWhere(['<=', 'start_time', $normalizedTime])
                ->all();
            foreach ($orders as $order){
                $order->status = Orders::STATUS_WORK;
                $order->save();
            }
        }

        return ExitCode::OK;
    }
}
