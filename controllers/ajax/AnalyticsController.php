<?php


namespace app\controllers\ajax;

use app\commons\helpers\ClientHelper;
use app\commons\helpers\GlobalTranslates;
use app\models\ar\carwash\Carwash;
use app\models\ar\personal\Personal;
use Yii;
use app\models\ar\order\Orders;
use yii\db\Exception;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\Controller;


class AnalyticsController extends Controller
{
    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    /**
     * @param $cwId
     * @param $pId
     * @param string $type - week | month | year
     * @return array|string
     */
    public function actionGetOrdersForChart($cwId, $pId, string $type)
    {
        $carwash = Carwash::findOne($cwId);
        $personal = Personal::findOne($pId);
        if (!$carwash || !$personal) {
            return ['result' => false, 'message' => 'Не найдены заданные модели'];
        }
        if ($personal->carwash_id !== $carwash->id) {
            return ['result' => false, 'message' => 'В доступе отказано'];
        }

        if (!in_array($type, ['week', 'month', 'year'])) {
            return ['result' => false, 'message' => 'Не найден отрезок для получения'];
        }

        $daysInType = $this->getTotalDaysInChartType($type);

        $ordersFindFromDate = date('Y-m-d', strtotime(date('Y-m-d') . "-" . $daysInType . " days"));
        $ordersFindToDate = date('Y-m-d');

        if ($type === 'week') {
            $data = $this->calculateOrdersByWeek($ordersFindFromDate, $ordersFindToDate, $cwId);
        }
        if ($type === 'month') {
            $data = $this->calculateOrdersByMonth($ordersFindFromDate, $ordersFindToDate, $cwId);
        }
        if ($type === 'year') {
            $data = $this->calculateOrdersByYear($ordersFindFromDate, $ordersFindToDate, $cwId);
        }

        $chartCategories = [];
        $chartOrdersCount = [];
        if (isset($data['categories'], $data['counts'])) {
            $chartCategories = $data['categories'];
            $chartOrdersCount = $data['counts'];
        }

        $todayOrders = Orders::find()->where(['carwash_id' => $cwId])->andWhere(['date' => $ordersFindToDate])->count();

        return ['result' => true, 'categories' => array_reverse($chartCategories), 'counts' => array_reverse($chartOrdersCount), 'todayCounts' => $todayOrders];
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @param $carwashId
     * @return array
     * @throws Exception
     */
    private function calculateOrdersByYear($fromDate, $toDate, $carwashId)
    {
        $data = Yii::$app->getDb()->createCommand(
            'SELECT COUNT(id) as count, MONTH(`date`) as date FROM orders
WHERE carwash_id = :carwashId AND `date` BETWEEN :fromDate AND :toDate
GROUP BY MONTH(`date`)
ORDER BY `date` DESC'
        )->bindValues([
            ':carwashId' => $carwashId,
            ':fromDate' => $fromDate,
            ':toDate' => $toDate,
        ])->queryAll();
        $ordersMap = ArrayHelper::map($data, 'date', 'count');

        $chartCategories = [];
        $chartCount = [];

        for ($i = 1; $i <= date('n'); $i++) {
            $date = $i;

            $chartCategories[] = GlobalTranslates::getMonthLabel($date);

            if (!isset($chartCount[$date])) {
                $chartCount[$date] = 0;
            }
            if (isset($ordersMap[$date])) {
                $chartCount[$date] += (int)$ordersMap[$date];
            } else {
                $chartCount[$date] = 0;
            }
        }

        return ['categories' => array_reverse(array_values($chartCategories)), 'counts' => array_reverse(array_values($chartCount))];

    }

    /**
     * @param $fromDate
     * @param $toDate
     * @param $carwashId
     * @return array
     * @throws Exception
     */
    private function calculateOrdersByMonth($fromDate, $toDate, $carwashId)
    {
        $data = Yii::$app->getDb()->createCommand(
            'SELECT COUNT(id) as count, `date` as date FROM orders
WHERE carwash_id = :carwashId AND `date` BETWEEN :fromDate AND :toDate
GROUP BY `date`
ORDER BY `date` DESC'
        )->bindValues([
            ':carwashId' => $carwashId,
            ':fromDate' => $fromDate,
            ':toDate' => $toDate,
        ])->queryAll();
        $ordersMap = ArrayHelper::map($data, 'date', 'count');

        $limit = 4;
        $chartCategories = [];
        $chartCount = [];
        $totalDays = $this->getTotalDaysInChartType('month');
        for ($i = 1; $i <= $totalDays; $i++) {
            $date = date('Y-m-d', strtotime(date('Y-m-d') . '-' . ($i - 1) . ' day'));
            $currentRange = $i / ($totalDays / $limit);
            if (!isset($chartCategories[$currentRange])) {
                $chartCategories[$currentRange] = $date;
            }

            if (!isset($chartCount[$currentRange])) {
                $chartCount[$currentRange] = 0;
            }
            if (isset($ordersMap[$date])) {
                $chartCount[$currentRange] += (int)$ordersMap[$date];
            }
        }

        return ['categories' => array_values($chartCategories), 'counts' => array_values($chartCount)];
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @param $carwashId
     * @return array
     * @throws Exception
     */
    private function calculateOrdersByWeek($fromDate, $toDate, $carwashId)
    {
        $data = Yii::$app->getDb()->createCommand(
            'SELECT COUNT(id) as count, `date` as date FROM orders
WHERE carwash_id = :carwashId AND `date` BETWEEN :fromDate AND :toDate
GROUP BY `date`
ORDER BY `date` DESC'
        )->bindValues([
            ':carwashId' => $carwashId,
            ':fromDate' => $fromDate,
            ':toDate' => $toDate,
        ])->queryAll();
        $ordersMap = ArrayHelper::map($data, 'date', 'count');

        $limit = 7;
        $chartCategories = [];
        $chartCount = [];
        $totalDays = $this->getTotalDaysInChartType('week');
        for ($i = 1; $i <= $totalDays; $i++) {
            $date = date('Y-m-d', strtotime(date('Y-m-d') . '-' . ($i - 1) . ' day'));
            $currentRange = $i / ($totalDays / $limit);
            if (!isset($chartCategories[$currentRange])) {
                $chartCategories[$currentRange] = $date;
            }

            if (!isset($chartCount[$currentRange])) {
                $chartCount[$currentRange] = 0;
            }
            if (isset($ordersMap[$date])) {
                $chartCount[$currentRange] += (int)$ordersMap[$date];
            }
        }

        return ['categories' => array_values($chartCategories), 'counts' => array_values($chartCount)];
    }

    /**
     * @param $cwId
     * @param $pId
     * @param string $type
     * @return array
     * @throws Exception
     */
    public function actionGetClientsForChart($cwId, $pId, string $type)
    {
        $carwash = Carwash::findOne($cwId);
        $personal = Personal::findOne($pId);
        if (!$carwash || !$personal) {
            return ['result' => false, 'message' => 'Не найдены заданные модели'];
        }
        if ($personal->carwash_id !== $carwash->id) {
            return ['result' => false, 'message' => 'В доступе отказано'];
        }

        if (!in_array($type, ['week', 'month', 'year'])) {
            return ['result' => false, 'message' => 'Не найден отрезок для получения'];
        }

        $daysInType = $this->getTotalDaysInChartType($type);

        $ordersFindFromDate = date('Y-m-d', strtotime(date('Y-m-d') . "-" . $daysInType . " days"));
        $ordersFindToDate = date('Y-m-d');

        if ($type === 'week') {
            $data = $this->calculateClientsByWeek($ordersFindFromDate, $ordersFindToDate, $cwId);
        }
        if ($type === 'month') {
            $data = $this->calculateClientsByMonth($ordersFindFromDate, $ordersFindToDate, $cwId);
        }
        if ($type === 'year') {
            $data = $this->calculateClientsByYear($ordersFindFromDate, $ordersFindToDate, $cwId);
        }

        $chartCategories = [];
        $chartClientsCount = [];
        $chartSubscribersCount = [];
        if (isset($data['categories'], $data['subscribers'], $data['clients'])) {
            $chartCategories = $data['categories'];
            $chartSubscribersCount = $data['subscribers'];
            $chartClientsCount = $data['clients'];
        }

        $todayOrders = (int) Orders::find()->where(['carwash_id' => $cwId])->andWhere(['date' => $ordersFindToDate])
            ->select(new Expression('COUNT(DISTINCT CONCAT(orders.car_number, orders.car_region))'))
            ->scalar();

        return ['result' => true, 'categories' => array_reverse($chartCategories), 'clients' => array_reverse($chartClientsCount),
            'subscribers' => array_reverse($chartSubscribersCount), 'todayCounts' => $todayOrders];
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @param $carwashId
     * @return array
     * @throws Exception
     */
    private function calculateClientsByYear($fromDate, $toDate, $carwashId)
    {
        $data = Yii::$app->getDb()->createCommand(
            'SELECT orders.car_number, orders.car_region, MAX(`date`) AS date
FROM orders
WHERE carwash_id = :carwashId AND `date` BETWEEN :fromDate AND :toDate
GROUP BY orders.car_number, orders.car_region
ORDER BY `date` DESC'
        )->bindValues([
            ':carwashId' => $carwashId,
            ':fromDate' => $fromDate,
            ':toDate' => $toDate,
        ])->queryAll();

        $ordersMap = [];
        foreach ($data as $datum) {
            $date = $datum['date'];
            $carNumber = $datum['car_number'];
            $carRegion = $datum['car_region'];
            $isSubscriber = ClientHelper::isSubscriberByCarNumber($carwashId, $carNumber, $carRegion);
            if ($isSubscriber) {
                $ordersMap[$date]['subscribers'] = ($ordersMap[$date]['subscribers'] ?? 0) + 1;
            } else {
                $ordersMap[$date]['clients'] = ($ordersMap[$date]['subscribers'] ?? 0) + 1;
            }
        }
        $limit = 12;
        $chartCategories = [];
        $subscribersCounts = [];
        $clientsCounts = [];

        $totalDays = $this->getTotalDaysInChartType('year');
        for ($i = 1; $i <= $totalDays; $i++) {
            $date = date('Y-m-d', strtotime(date('Y-m-d') . '-' . ($i - 1) . ' day'));
            $currentRange = $i / ($totalDays / $limit);
            if (!isset($chartCategories[$currentRange])) {
                $chartCategories[$currentRange] = $date;
            }

            $dayOrders = $ordersMap[$date] ?? [];
            if (isset($dayOrders)) {
                if (isset($dayOrders['clients'])) {
                    $clientsCounts[$currentRange] = ($clientsCounts[$currentRange] ?? 0) + $dayOrders['clients'];
                } else {
                    $clientsCounts[$currentRange] = ($clientsCounts[$currentRange] ?? 0) + 0;
                }

                if (isset($dayOrders['subscribers'])) {
                    $subscribersCounts[$currentRange] = ($subscribersCounts[$currentRange] ?? 0) + $dayOrders['subscribers'];
                } else {
                    $subscribersCounts[$currentRange] = ($subscribersCounts[$currentRange] ?? 0) + 0;
                }
            }
        }

        return ['categories' => array_values($chartCategories), 'subscribers' => array_values($subscribersCounts),
            'clients' => array_values($clientsCounts)];
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @param $carwashId
     * @return array
     * @throws Exception
     */
    private function calculateClientsByMonth($fromDate, $toDate, $carwashId)
    {
        $data = Yii::$app->getDb()->createCommand(
            'SELECT orders.car_number, orders.car_region, MAX(`date`) AS date
FROM orders
WHERE carwash_id = :carwashId AND `date` BETWEEN :fromDate AND :toDate
GROUP BY orders.car_number, orders.car_region
ORDER BY `date` DESC'
        )->bindValues([
            ':carwashId' => $carwashId,
            ':fromDate' => $fromDate,
            ':toDate' => $toDate,
        ])->queryAll();

        $ordersMap = [];
        foreach ($data as $datum) {
            $date = $datum['date'];
            $carNumber = $datum['car_number'];
            $carRegion = $datum['car_region'];
            $isSubscriber = ClientHelper::isSubscriberByCarNumber($carwashId, $carNumber, $carRegion);
            if ($isSubscriber) {
                $ordersMap[$date]['subscribers'] = ($ordersMap[$date]['subscribers'] ?? 0) + 1;
            } else {
                $ordersMap[$date]['clients'] = ($ordersMap[$date]['subscribers'] ?? 0) + 1;
            }
        }
        $limit = 4;
        $chartCategories = [];
        $subscribersCounts = [];
        $clientsCounts = [];

        $totalDays = $this->getTotalDaysInChartType('month');
        for ($i = 1; $i <= $totalDays; $i++) {
            $date = date('Y-m-d', strtotime(date('Y-m-d') . '-' . ($i - 1) . ' day'));
            $currentRange = $i / ($totalDays / $limit);
            if (!isset($chartCategories[$currentRange])) {
                $chartCategories[$currentRange] = $date;
            }

            $dayOrders = $ordersMap[$date] ?? [];
            if (isset($dayOrders)) {
                if (isset($dayOrders['clients'])) {
                    $clientsCounts[$currentRange] = ($clientsCounts[$currentRange] ?? 0) + $dayOrders['clients'];
                } else {
                    $clientsCounts[$currentRange] = ($clientsCounts[$currentRange] ?? 0) + 0;
                }

                if (isset($dayOrders['subscribers'])) {
                    $subscribersCounts[$currentRange] = ($subscribersCounts[$currentRange] ?? 0) + $dayOrders['subscribers'];
                } else {
                    $subscribersCounts[$currentRange] = ($subscribersCounts[$currentRange] ?? 0) + 0;
                }
            }
        }

        return ['categories' => array_values($chartCategories), 'subscribers' => array_values($subscribersCounts),
            'clients' => array_values($clientsCounts)];
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @param $carwashId
     * @return array
     * @throws Exception
     */
    private function calculateClientsByWeek($fromDate, $toDate, $carwashId)
    {
        $data = Yii::$app->getDb()->createCommand(
            'SELECT orders.car_number, orders.car_region, MAX(`date`) AS date
FROM orders
WHERE carwash_id = :carwashId AND `date` BETWEEN :fromDate AND :toDate
GROUP BY orders.car_number, orders.car_region
ORDER BY `date` DESC'
        )->bindValues([
            ':carwashId' => $carwashId,
            ':fromDate' => $fromDate,
            ':toDate' => $toDate,
        ])->queryAll();

        $ordersMap = [];
        foreach ($data as $datum) {
            $date = $datum['date'];
            $carNumber = $datum['car_number'];
            $carRegion = $datum['car_region'];
            $isSubscriber = ClientHelper::isSubscriberByCarNumber($carwashId, $carNumber, $carRegion);
            if ($isSubscriber) {
                $ordersMap[$date]['subscribers'] = ($ordersMap[$date]['subscribers'] ?? 0) + 1;
            } else {
                $ordersMap[$date]['clients'] = ($ordersMap[$date]['subscribers'] ?? 0) + 1;
            }
        }

        $limit = 7;
        $chartCategories = [];

        $subscribersCounts = [];
        $clientsCounts = [];
        $totalDays = $this->getTotalDaysInChartType('week');
        for ($i = 1; $i <= $totalDays; $i++) {
            $date = date('Y-m-d', strtotime(date('Y-m-d') . '-' . ($i - 1) . ' day'));
            $currentRange = $i / ($totalDays / $limit);
            if (!isset($chartCategories[$currentRange])) {
                $chartCategories[$currentRange] = $date;
            }
            $dayOrders = $ordersMap[$date] ?? [];
            if (isset($dayOrders)) {
                if (isset($dayOrders['clients'])) {
                    $clientsCounts[$currentRange] = ($clientsCounts[$currentRange] ?? 0) + $dayOrders['clients'];
                } else {
                    $clientsCounts[$currentRange] = ($clientsCounts[$currentRange] ?? 0) + 0;
                }

                if (isset($dayOrders['subscribers'])) {
                    $subscribersCounts[$currentRange] = ($subscribersCounts[$currentRange] ?? 0) + $dayOrders['subscribers'];
                } else {
                    $subscribersCounts[$currentRange] = ($subscribersCounts[$currentRange] ?? 0) + 0;
                }
            }
        }

        return ['categories' => array_values($chartCategories), 'subscribers' => array_values($subscribersCounts),
            'clients' => array_values($clientsCounts)];
    }

    public function actionGetFinanceForChart($cwId, $pId, string $type, $category)
    {
        $carwash = Carwash::findOne($cwId);
        $personal = Personal::findOne($pId);
        if (!$carwash || !$personal) {
            return ['result' => false, 'message' => 'Не найдены заданные модели'];
        }
        if ($personal->carwash_id !== $carwash->id) {
            return ['result' => false, 'message' => 'В доступе отказано'];
        }

        if (!in_array($type, ['week', 'month', 'year'])) {
            return ['result' => false, 'message' => 'Не найден отрезок для получения'];
        }

        if (!in_array($category, ['all', 'wash', 'detail'])) {
            return ['result' => false, 'message' => 'Не найдена запрашиваемая категория'];
        }

        $daysInType = $this->getTotalDaysInChartType($type);

        $ordersFindFromDate = date('Y-m-d', strtotime(date('Y-m-d') . "-" . $daysInType . " days"));
        $ordersFindToDate = date('Y-m-d');

        if ($type === 'week') {
            $data = $this->calculateFinancesByWeek($ordersFindFromDate, $ordersFindToDate, $cwId, $category);
        }
        if ($type === 'month') {
            $data = $this->calculateFinancesByMonth($ordersFindFromDate, $ordersFindToDate, $cwId, $category);
        }
        if ($type === 'year') {
            $data = $this->calculateFinancesByYear($ordersFindFromDate, $ordersFindToDate, $cwId, $category);
        }

        $chartCategories = [];
        $chartFinances = [];
        if (isset($data['categories'], $data['finances'])) {
            $chartCategories = $data['categories'];
            $chartFinances = $data['finances'];
        }

        return ['result' => true, 'categories' => array_reverse($chartCategories),
            'finances' => array_reverse($chartFinances)];
    }

    private function calculateFinancesByWeek($fromDate, $toDate, $carwashId, $category)
    {
        $detailingFilter = '';
        if ($category === 'wash') {
            $detailingFilter = ' AND is_detailing = false ';
        } elseif ($category === 'detail') {
            $detailingFilter = ' AND is_detailing = true ';
        }

        $data = Yii::$app->getDb()->createCommand("SELECT SUM(sum) AS sum, date FROM 
(SELECT order_service.order_id, SUM(price) as sum, MAX(o.date) as date FROM order_service
LEFT JOIN orders AS o ON o.id = order_service.order_id
WHERE o.carwash_id = :carwashId AND o.date BETWEEN :fromDate AND :toDate $detailingFilter
GROUP BY order_service.order_id) o GROUP BY date")
            ->bindValues([':carwashId' => $carwashId, ':fromDate' => $fromDate, ':toDate' => $toDate])
            ->queryAll();

        $financeMap = ArrayHelper::map($data, 'date', 'sum');

        $limit = 7;
        $chartCategories = [];
        $finances = [];

        $totalDays = $this->getTotalDaysInChartType('week');
        for ($i = 1; $i <= $totalDays; $i++) {
            $date = date('Y-m-d', strtotime(date('Y-m-d') . '-' . ($i - 1) . ' day'));
            $currentRange = $i / ($totalDays / $limit);
            if (!isset($chartCategories[$currentRange])) {
                $chartCategories[$currentRange] = $date;
            }
            if (isset($financeMap[$date])) {
                $finances[] = $financeMap[$date];
            } else {
                $finances[] = 0;
            }
        }

        return ['categories' => array_values($chartCategories), 'finances' => array_values($finances)];
    }

    private function calculateFinancesByMonth($fromDate, $toDate, $carwashId, $category)
    {
        $detailingFilter = '';
        if ($category === 'wash') {
            $detailingFilter = ' AND is_detailing = false ';
        } elseif ($category === 'detail') {
            $detailingFilter = ' AND is_detailing = true ';
        }

        $data = Yii::$app->getDb()->createCommand("SELECT SUM(sum) AS sum, date FROM 
(SELECT order_service.order_id, SUM(price) as sum, MAX(o.date) as date FROM order_service
LEFT JOIN orders AS o ON o.id = order_service.order_id
WHERE o.carwash_id = :carwashId AND o.date BETWEEN :fromDate AND :toDate $detailingFilter
GROUP BY order_service.order_id) o GROUP BY date")
            ->bindValues([':carwashId' => $carwashId, ':fromDate' => $fromDate, ':toDate' => $toDate])
            ->queryAll();

        $financeMap = ArrayHelper::map($data, 'date', 'sum');

        $limit = 4;
        $chartCategories = [];
        $finances = [];

        $totalDays = $this->getTotalDaysInChartType('month');
        for ($i = 1; $i <= $totalDays; $i++) {
            $date = date('Y-m-d', strtotime(date('Y-m-d') . '-' . ($i - 1) . ' day'));
            $currentRange = $i / ($totalDays / $limit);
            if (!isset($chartCategories[$currentRange])) {
                $chartCategories[$currentRange] = $date;
            }

            if (!isset($finances[$currentRange])) {
                $finances[$currentRange] = 0;
            }
            if (isset($financeMap[$date])) {
                $finances[$currentRange] += $financeMap[$date];
            } else {
                $finances[$currentRange] += 0;
            }
        }

        return ['categories' => array_values($chartCategories), 'finances' => array_values($finances)];
    }

    private function calculateFinancesByYear($fromDate, $toDate, $carwashId, $category)
    {
        $detailingFilter = '';
        if ($category === 'wash') {
            $detailingFilter = ' AND is_detailing = false ';
        } elseif ($category === 'detail') {
            $detailingFilter = ' AND is_detailing = true ';
        }

        $data = Yii::$app->getDb()->createCommand("SELECT SUM(sum) AS sum, date FROM 
(SELECT order_service.order_id, SUM(price) as sum, MAX(o.date) as date FROM order_service
LEFT JOIN orders AS o ON o.id = order_service.order_id
WHERE o.carwash_id = :carwashId AND o.date BETWEEN :fromDate AND :toDate $detailingFilter
GROUP BY order_service.order_id) o GROUP BY date")
            ->bindValues([':carwashId' => $carwashId, ':fromDate' => $fromDate, ':toDate' => $toDate])
            ->queryAll();

        $financeMap = ArrayHelper::map($data, 'date', 'sum');

        $limit = 12;
        $chartCategories = [];
        $finances = [];

        $totalDays = $this->getTotalDaysInChartType('year');
        for ($i = 1; $i <= $totalDays; $i++) {
            $date = date('Y-m-d', strtotime(date('Y-m-d') . '-' . ($i - 1) . ' day'));
            $currentRange = $i / ($totalDays / $limit);
            if (!isset($chartCategories[$currentRange])) {
                $chartCategories[$currentRange] = $date;
            }
            if (!isset($finances[$currentRange])) {
                $finances[$currentRange] = 0;
            }
            if (isset($financeMap[$date])) {
                $finances[$currentRange] += $financeMap[$date];
            } else {
                $finances[$currentRange] += 0;
            }
        }

        return ['categories' => array_values($chartCategories), 'finances' => array_values($finances)];
    }


    /**
     * @param $type
     * @return int
     */
    private function getTotalDaysInChartType($type)
    {
        switch ($type) {
            case 'week':
                $days = 7;
                break;
            case 'month' :
                $days = 31;
                break;
            case 'year' :
                $days = 365;
                break;
            default :
                $days = 0;
        }
        return $days ?? 0;
    }
}