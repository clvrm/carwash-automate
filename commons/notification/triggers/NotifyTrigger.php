<?php

namespace app\commons\notification\triggers;

use app\commons\exceptions\NotifyException;
use app\commons\notification\FirebaseHelper;
use app\models\ar\order\Orders;
use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalNotification;
use app\models\ar\personal\PersonalNotification as PN;
use yii\helpers\ArrayHelper;
use Yii;

class NotifyTrigger implements NotifyTriggerInterface
{
    protected $eventName = '';

    protected $defaultTitle = '';
    protected $defaultMessage = '';
    protected $defaultData = [];

    protected $data = []; // Дополнительные данные для формирования уведомления
    protected $initiatorPersonalId = false; // Сотрудник, который запустил данный триггер
    protected $carwashId = false; // Мойка с которой запустили данный триггер
    protected $subscribedPersonals = []; // Те, кто подписан на данное событие

    public function __construct()
    {
        $this->carwashId = Yii::$app->user->identity->getCWid();
    }

    public function setData(array $data)
    {
        $this->data = $data;
        if (isset($data['initiatorPersonalId']) && !empty($data['initiatorPersonalId'])) {
            $this->setInitiatorPersonalId($data['initiatorPersonalId']);
        }
    }

    public function setCarwashId(int $carwashId): void
    {
        $this->carwashId = $carwashId;
    }

    public function setInitiatorPersonalId(int $initiatorPersonalId): void
    {
        $this->initiatorPersonalId = $initiatorPersonalId;
    }


    public function prepare()
    {
        // Индивидуален для каждого триггера
    }

    protected function rolesNotify()
    {
        return [
            Personal::POST_OWNER => [PN::EVENT_NEW_PERSONAL, PN::EVENT_EDIT_PERSONAL_SALARY, PN::EVENT_CHANGE_ONLINE_RECORD_STATUS,
                PN::EVENT_CANCEL_ORDER, PN::EVENT_NEW_REVIEW, PN::EVENT_NEW_ORDER, PN::EVENT_NEW_SALES_REPORT,
                PN::EVENT_EDIT_PRICE_LIST, PN::EVENT_EDIT_RECORD_SETTINGS, PN::EVENT_EDIT_SCHEDULE],
            Personal::POST_MANAGER => [PN::EVENT_NEW_PERSONAL, PN::EVENT_EDIT_PERSONAL_SALARY, PN::EVENT_CHANGE_ONLINE_RECORD_STATUS,
                PN::EVENT_CANCEL_ORDER, PN::EVENT_NEW_REVIEW, PN::EVENT_NEW_ORDER, PN::EVENT_EDIT_PRICE_LIST,
                PN::EVENT_EDIT_RECORD_SETTINGS, PN::EVENT_EDIT_SCHEDULE],
            Personal::POST_ADMIN => [PN::EVENT_CHANGE_ONLINE_RECORD_STATUS, PN::EVENT_NEW_PERSONAL, PN::EVENT_NEW_REVIEW,
                PN::EVENT_CANCEL_ORDER, PN::EVENT_NEW_ORDER],
            Personal::POST_WASHER => [PN::EVENT_NEW_ORDER, PN::EVENT_CANCEL_ORDER],
        ];
    }

    protected function getSubscribers()
    {
        $personals = Personal::find()->joinWith('personalNotifications')->where(['carwash_id' => $this->carwashId])
            ->andWhere(['personal_notification.key' => $this->eventName,
                'personal_notification.type' => PersonalNotification::TYPE_NOTIFY_EVENTS, 'personal_notification.value' => 1])->all();

        foreach ($personals as $personal) {
            if (!in_array($this->eventName, $this->rolesNotify()[$personal->post], false)) {
                continue;
            }
            $this->subscribedPersonals[] = $personal;
        }
    }

    public function sendAllSystems()
    {
        $this->getSubscribers();

        foreach ($this->subscribedPersonals as $personal) {
            $availableSystems = ArrayHelper::map(PersonalNotification::find()->where(['personal_id' => $personal->id, 'type' => PN::TYPE_NOTIFY_SYSTEM, 'value' => 1])->all(),
                'key', 'key');

            if (isset($availableSystems[PersonalNotification::SYSTEM_PUSH]) && !empty($personal->firebase_token)) {

                $firebaseHelper = new FirebaseHelper();
                $title = $this->pushTitle ?? $this->defaultTitle;
                $message = $this->pushMessage ?? $this->defaultMessage;
                $data = $this->pushData ?? $this->defaultData;
                $firebaseHelper->sendMessage($personal->firebase_token, $title, $message, $data);
                unset($title, $message, $data);
            }
        }
    }
}