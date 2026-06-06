<?php

namespace app\commons\notification\triggers;

use app\commons\exceptions\NotifyException;
use app\commons\helpers\TimeHelper;
use app\models\ar\chat\Chat;
use app\models\ar\order\Orders;
use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalNotification;

class TriggerNewReview extends NotifyTrigger
{
    protected $eventName = PersonalNotification::EVENT_NEW_REVIEW;

    public function prepare()
    {
        if (!isset($this->data['chatId'])) {
            throw new NotifyException('Невозможно получить информацию о чате,т.к. не передан номер заказа');
        }
        $chat = Chat::findOne($this->data['chatId']);
        if (!isset($chat)) {
            throw new NotifyException('Чат не найден');
        }
        $order = $chat->order;
        $client = $chat->client;
        if (isset($this->data['messageId'])) {
            $message = $chat->getChatMessages()->where(['id' => $this->data['messageId']])->one();
        } else {
            $message = $chat->getChatMessages()->one();
        }
        $messageText = '';
        if ($message) {
            $messageText = $message->text ?? '';
        }
        $carNumber = ($order->car_number ?? 'A123AA') . ($order->car_region ?? '777');
        $orderDate = $order->date;
        $orderPhone = $order->client_phone ?? '';
        $orderEndTime = TimeHelper::convertMinToTime($order->end_time) ?? '';
        $orderBrand = $order->carBrand->title ?? '';
        $isPersonalReview = isset($message->personal_id);
        if ($isPersonalReview) {
            $personal = Personal::findOne($message->personal_id);
            $personalName = '';
            if ($personal) {
                $personalName = $personal->getShortUsername() ?? '';
            }
            $this->defaultTitle = $this->pushTitle = 'Создан ответ на отзыв';
            $this->defaultMessage = $this->pushMessage = $orderDate . ' ' . $personalName . ' ответ на отзыв ' . $orderBrand . ' ' . $carNumber . ': ' .  substr($messageText, 0, 30);
        } else {
            $this->defaultTitle = $this->pushTitle = 'Создан новый отзыв';
            $this->defaultMessage = $this->pushMessage = $orderDate . ' Клиент ' . ($client->phone ?: $orderPhone) . ' ' . $orderBrand . ' ' . $carNumber . ': ' . substr($messageText, 0, 30);
        }
    }
}