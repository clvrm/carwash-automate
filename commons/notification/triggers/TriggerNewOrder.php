<?php

namespace app\commons\notification\triggers;

use app\commons\exceptions\NotifyException;
use app\commons\helpers\TimeHelper;
use app\models\ar\order\Orders;
use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalNotification;

class TriggerNewOrder extends NotifyTrigger
{
    protected $eventName = PersonalNotification::EVENT_NEW_ORDER;
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
        $orderBrand = $order->carBrand->title ?? '';
        if ($order->client_id) {
            $this->defaultTitle = $this->pushTitle = 'У Вас новый заказ!';
            $this->defaultMessage = $this->pushMessage = 'Запись клиента ' . $orderPhone . ' от ' . $orderDate . ' на ' . $orderEndTime . ' ' . $orderBrand . ' ' . $carNumber . ' создана';
        } else {
            $this->defaultTitle = $this->pushTitle = 'У Вас новый заказ!';
            $this->defaultMessage = $this->pushMessage = 'Запись сотрудника от ' . $orderDate . ' на ' . $orderEndTime . ' ' . $orderBrand . ' ' . $carNumber . ' создана';
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