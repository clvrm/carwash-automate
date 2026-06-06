<?php

namespace app\commons\notification\triggers;

use app\commons\exceptions\NotifyException;
use app\commons\helpers\TimeHelper;
use app\models\ar\carwash\CarwashSchedule;
use app\models\ar\chat\Chat;
use app\models\ar\order\Orders;
use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalNotification;

class TriggerEditRecordSettings extends NotifyTrigger
{
    protected $eventName = PersonalNotification::EVENT_EDIT_RECORD_SETTINGS;

    public function prepare()
    {
        $personalName = '';
        if (!empty($this->initiatorPersonalId)){
            $personal = Personal::findOne($this->initiatorPersonalId);
            if ($personal){
                $personalName = $personal->getShortUsername() ?? '';
            }
        }
        $this->defaultTitle = $this->pushTitle = 'Настройки online записи изменены';
        $this->defaultMessage = $this->pushMessage = 'Изменения внесены сотрудником ' . $personalName ;

    }
}