<?php


namespace app\commons\helpers;


use app\models\ar\Clients;
use app\models\ar\order\Orders;
use yii\db\Expression;
use yii\db\Query;


/**
 * Class ClientHelper
 * @package app\commons\helpers
 */
class ClientHelper
{
    public const COUNT_ORDERS_TO_SUBSCRIBE = 3;

    /**
     * Проверка является ли клиент подписчиком автомойки
     * @param $carwashId
     * @param $carNumber
     * @param $carRegion
     * @return bool
     */
    public static function isSubscriberByCarNumber($carwashId, $carNumber, $carRegion): bool
    {
        $orders = Orders::find()->where(['carwash_id' => $carwashId, 'car_number' => $carNumber, 'car_region' => $carRegion])
            ->all();
        $countOrders = count($orders);

        if ($countOrders >= self::COUNT_ORDERS_TO_SUBSCRIBE) {
            return true;
        }

        // Проверяем есть ли клиент у одного из заказов, и является ли он подписчиком
        if (isset($orders[0])) {
            /* @var Clients $orderClient */
            $orderClient = $orders[0]->client;
            if (isset($orderClient) && $orderClient->is_subscribed && (int)$orderClient->carwash_id === (int)$carwashId) {
                return true;
            }
        }

        return false;
    }

    public static function countSubscribers($carwashId)
    {
        $ordersCount = (new Query())->select(['COUNT(id) AS countOrders'])->from('orders')
            ->where(['carwash_id' => $carwashId])->groupBy(new Expression("CONCAT(car_number, ' ', car_region)"))
            ->having(['>=', 'countOrders', self::COUNT_ORDERS_TO_SUBSCRIBE])->count();
        $subscribedClientsCount = Clients::find()->where(['carwash_id' => $carwashId])->andWhere(['is_subscribed' => true])->count();

        return $ordersCount + $subscribedClientsCount;
    }
}