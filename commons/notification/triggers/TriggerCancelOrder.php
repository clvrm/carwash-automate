<?php

namespace app\commons\notification\triggers;

use app\commons\exceptions\NotifyException;
use app\commons\helpers\TimeHelper;
use app\models\ar\carwash\CarwashSchedule;
use app\models\ar\chat\Chat;
use app\models\ar\order\Orders;
use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalNotification;

class TriggerCancelOrder extends NotifyTrigger
{
    protected $eventName = PersonalNotification::EVENT_CANCEL_ORDER;
    private $order;


    public function prepare()
    {
        if (!isset($this->data['orderId'])) {
            throw new NotifyException('Невозможно получить информацию о заказе,т.к. не передан номер заказа');
        }
        $order = Orders::findOne($this->data['orderId']);
        if (!isset($order)) {
            throw new NotifyException('Заказ не найден');
        }
        $this->order = $order;
        $carNumber = ($order->car_number ?? 'A123AA') . ($order->car_region ?? '777');
        $orderDate = $order->date;
        $orderPhone = $order->client_phone ?? '';
        $orderEndTime = TimeHelper::convertMinToTime($order->end_time) ?? '';
        $orderStartTime = TimeHelper::convertMinToTime($order->start_time) ?? '';
        $orderBrand = $order->carBrand->title ?? '';
        if (isset($this->data['isPersonalDelete']) && $this->data['isPersonalDelete'] == true) {
            $personalName = '';
            if ($this->initiatorPersonalId) {
                $personal = Personal::findOne($this->initiatorPersonalId);
                if ($personal) {
                    $personalName = $personal->getShortUsername() ?? '';
                }
            }
            $this->defaultTitle = $this->pushTitle = 'Заказ удален';
            $this->defaultMessage = $this->pushMessage = 'Заказ от ' . $orderDate . ' на ' . $orderStartTime . ' ' . $orderBrand . ' ' . $carNumber . ' удален сотрудником ' . $personalName;
        } else {
            $this->defaultTitle = $this->pushTitle = 'Заказ удален';
            $this->defaultMessage = $this->pushMessage = 'Заказ от ' . $orderDate . ' на ' . $orderStartTime . ' ' . $orderBrand . ' ' . $carNumber . ' удален клиентом ' . $orderPhone;
        }
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
            // Сбрасываем мойщиков, если они не назначены на заказ
            if (isset($this->order, $this->order->personal_id) && $personal->post == Personal::POST_WASHER && $this->order->personal_id != $personal->id){
                continue;
            }
            $this->subscribedPersonals[] = $personal;
        }
    }
}