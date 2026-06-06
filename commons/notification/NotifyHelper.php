<?php

namespace app\commons\notification;

use app\commons\exceptions\LogicException;
use app\commons\exceptions\NotifyException;
use app\commons\notification\triggers\TriggerCancelOrder;
use app\commons\notification\triggers\TriggerEditPrice;
use app\commons\notification\triggers\TriggerEditRecordSettings;
use app\commons\notification\triggers\TriggerEditSalary;
use app\commons\notification\triggers\TriggerEditSchedule;
use app\commons\notification\triggers\TriggerNewOrder;
use app\commons\notification\triggers\TriggerNewPersonal;
use app\commons\notification\triggers\TriggerNewReview;
use app\commons\notification\triggers\TriggerReportGenerated;
use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalNotification;
use app\models\ar\personal\PersonalNotification as PN;
use app\models\ar\Users;
use yii\helpers\ArrayHelper;

class NotifyHelper
{
    protected function triggerAliases()
    {
        return [
            PN::EVENT_NEW_ORDER => new TriggerNewOrder(),
            PN::EVENT_NEW_REVIEW => new TriggerNewReview(),
            PN::EVENT_CANCEL_ORDER => new TriggerCancelOrder(),
            PN::EVENT_NEW_SALES_REPORT => new TriggerReportGenerated(),
            PN::EVENT_EDIT_SCHEDULE => new TriggerEditSchedule(),
            PN::EVENT_EDIT_RECORD_SETTINGS => new TriggerEditRecordSettings(),
            PN::EVENT_EDIT_PRICE_LIST => new TriggerEditPrice(),
            PN::EVENT_EDIT_PERSONAL_SALARY => new TriggerEditSalary(),
            PN::EVENT_NEW_PERSONAL => new TriggerNewPersonal(),

            PN::EVENT_CHANGE_ONLINE_RECORD_STATUS => new TriggerNewOrder(),
        ];
    }

    public function trigger($event, $data = [])
    {
        try {
            if (!isset($this->triggerAliases()[$event])) {
                throw new LogicException('Запущен неизвестный триггер');
            }
            $trigger = $this->triggerAliases()[$event];
            $trigger->setData($data);
            $trigger->prepare();
            $trigger->sendAllSystems();
        } catch (\Exception $exception) {
            // TODO: LOG
        }
    }
}