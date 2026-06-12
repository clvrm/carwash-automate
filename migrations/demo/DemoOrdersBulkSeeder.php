<?php

namespace app\migrations\demo;

use app\migrations\demo\data\DemoData;
use app\models\ar\car\CarBrands;
use app\models\ar\car\CarModels;
use app\models\ar\carwash\Carwash;
use app\models\ar\Clients;
use app\models\ar\complex\Complexes;
use app\models\ar\order\Orders;
use app\models\ar\order\OrderService;
use app\models\ar\personal\Personal;
use app\models\ar\service\Services;
use Faker\Factory;
use Faker\Generator;
use Yii;
use yii\db\Exception;

/**
 * Массовая генерация заказов через Faker.
 */
class DemoOrdersBulkSeeder
{
    public const ORDER_COUNT = 1000;
    public const DATE_FROM = '2026-06-10';
    public const DATE_TO = '2026-06-30';

    /** @var int */
    private $carwashId;

    /** @var Generator */
    private $faker;

    /** @var Clients[] */
    private $clients = [];

    /** @var CarBrands[] */
    private $brands = [];

    /** @var CarModels[] */
    private $models = [];

    /** @var Services[] */
    private $washServices = [];

    /** @var Complexes[] */
    private $washComplexes = [];

    /** @var Personal[] */
    private $staff = [];

    /** @var array<string, array<int, array{0: int, 1: int}>> */
    private $occupiedSlots = [];

    public function seed(): int
    {
        $carwash = Carwash::findOne(['name' => DemoData::CARWASH_NAME]);
        if (!$carwash) {
            throw new Exception(
                'Автомойка «' . DemoData::CARWASH_NAME . '» не найдена. Сначала: php yii migrate'
            );
        }

        $this->carwashId = $carwash->id;
        $this->faker = Factory::create('ru_RU');
        $this->faker->seed(20260610);

        $this->loadExistingData();
        $this->preloadOccupiedSlots();

        $slotPool = $this->buildSlotPool();
        $created = 0;

        foreach ($slotPool as [$date, $post, $start]) {
            if ($created >= self::ORDER_COUNT) {
                break;
            }

            $duration = $this->faker->numberBetween(20, 45);
            $end = $start + $duration;
            $dayEnd = 21 * 60 + 30;
            if ($end > $dayEnd || !$this->isSlotFree($date, $post, $start, $end)) {
                continue;
            }

            $this->occupySlot($date, $post, $start, $end);
            $this->createOrder($date, $post, $start, $end);
            $created++;
        }

        return $created;
    }

    /**
     * @return array<int, array{0: string, 1: int, 2: int}>
     */
    private function buildSlotPool(): array
    {
        $pool = [];
        $dayStart = 8 * 60;
        $dayEnd = 21 * 60 + 30;
        $from = new \DateTime(self::DATE_FROM);
        $to = new \DateTime(self::DATE_TO);
        $to->modify('+1 day');

        foreach (new \DatePeriod($from, new \DateInterval('P1D'), $to) as $day) {
            $date = $day->format('Y-m-d');
            for ($post = 1; $post <= 3; $post++) {
                for ($start = $dayStart; $start <= $dayEnd - 20; $start += 15) {
                    $pool[] = [$date, $post, $start];
                }
            }
        }

        shuffle($pool);

        return $pool;
    }

    public function remove(): int
    {
        $carwash = Carwash::findOne(['name' => DemoData::CARWASH_NAME]);
        if (!$carwash) {
            return 0;
        }

        $orderIds = Orders::find()
            ->select('id')
            ->where(['carwash_id' => $carwash->id])
            ->andWhere(['between', 'date', self::DATE_FROM, self::DATE_TO])
            ->column();

        if (!$orderIds) {
            return 0;
        }

        OrderService::deleteAll(['order_id' => $orderIds]);

        return Orders::deleteAll(['id' => $orderIds]);
    }

    public static function exists(): bool
    {
        $carwash = Carwash::findOne(['name' => DemoData::CARWASH_NAME]);
        if (!$carwash) {
            return false;
        }

        $count = (int)Orders::find()
            ->where(['carwash_id' => $carwash->id])
            ->andWhere(['between', 'date', self::DATE_FROM, self::DATE_TO])
            ->count();

        // Пакет считается загруженным, если в диапазоне уже ~1000 заказов
        return $count >= (int)(self::ORDER_COUNT * 0.9);
    }

    private function loadExistingData(): void
    {
        $this->clients = Clients::find()->where(['carwash_id' => $this->carwashId])->all();
        $this->brands = CarBrands::find()->all();
        $this->models = CarModels::find()->all();
        $this->staff = Personal::find()->where(['carwash_id' => $this->carwashId])->all();

        $services = Services::find()->where(['carwash_id' => $this->carwashId])->all();
        $this->washServices = array_values(array_filter($services, static function (Services $s) {
            return !$s->is_detailing;
        }));

        $complexes = Complexes::find()->where(['carwash_id' => $this->carwashId])->all();
        $this->washComplexes = array_values(array_filter($complexes, static function (Complexes $c) {
            return !$c->is_detailing;
        }));

        if (!$this->clients || !$this->washServices) {
            throw new Exception('Недостаточно данных автомойки для генерации заказов.');
        }
    }

    private function preloadOccupiedSlots(): void
    {
        $orders = Orders::find()
            ->where(['carwash_id' => $this->carwashId])
            ->andWhere(['between', 'date', self::DATE_FROM, self::DATE_TO])
            ->andWhere(['not', ['start_time' => null]])
            ->andWhere(['not', ['end_time' => null]])
            ->all();

        foreach ($orders as $order) {
            $this->occupySlot($order->date, (int)$order->post, (int)$order->start_time, (int)$order->end_time);
        }
    }

    private function createOrder(string $date, int $post, int $startTime, int $endTime): void
    {
        $carType = $this->faker->numberBetween(1, 5);
        $status = $this->resolveStatus($date);
        $client = $this->faker->randomElement($this->clients);

        $brand = $this->brands ? $this->faker->randomElement($this->brands) : null;
        $brandModels = $brand
            ? array_values(array_filter($this->models, static function (CarModels $m) use ($brand) {
                return (int)$m->car_brand_id === (int)$brand->id;
            }))
            : [];
        $model = $brandModels
            ? $this->faker->randomElement($brandModels)
            : ($this->models ? $this->faker->randomElement($this->models) : null);

        $personal = $this->pickPersonal($status);

        $order = new Orders();
        $order->carwash_id = $this->carwashId;
        $order->client_id = $client->id;
        $order->personal_id = $personal ? $personal->id : null;
        if ($personal && $status === Orders::STATUS_ARCHIVE) {
            $order->personal_fullname = $personal->getShortUsername();
        }
        $order->date = $date;
        $order->post = $post;
        $order->start_time = $startTime;
        $order->end_time = $endTime;
        $order->car_type = $carType;
        $order->car_number = $this->randomCarNumber();
        $order->car_region = $this->faker->randomElement(DemoData::CAR_REGIONS);
        $order->color = $this->faker->randomElement(DemoData::CAR_COLORS);
        $order->car_brand_id = $brand ? $brand->id : null;
        $order->car_model_id = $model ? $model->id : null;
        $order->client_fullname = $client->full_name;
        $order->client_phone = $client->phone;
        $order->work_time = $endTime - $startTime;
        $order->status = $status;
        $order->admin_comment = $this->faker->optional(0.6)->sentence(6);
        if ($status === Orders::STATUS_NEW_FROM_CLIENT) {
            $order->client_comment = $this->faker->randomElement([
                'Запись через онлайн-форму',
                'Хочу записаться на комплекс Стандарт',
                'Можно пораньше, если освободится пост',
            ]);
        }

        $totalPrice = 0;
        $itemsCount = $this->faker->numberBetween(1, 3);
        $lineItems = [];

        for ($j = 0; $j < $itemsCount; $j++) {
            if ($this->washComplexes && $this->faker->boolean(45)) {
                $entity = $this->faker->randomElement($this->washComplexes);
                $type = OrderService::TYPE_COMPLEX;
                $isDetailing = (bool)$entity->is_detailing;
            } else {
                $entity = $this->faker->randomElement($this->washServices);
                $type = OrderService::TYPE_SERVICE;
                $isDetailing = (bool)$entity->is_detailing;
            }
            $price = $this->getPriceByCarType($entity, $carType);
            $totalPrice += $price;
            $lineItems[] = [$entity, $type, $isDetailing, $price];
        }

        $dow = (int)date('N', strtotime($date));
        $sale = 0;
        if ($client->is_subscribed && $this->faker->boolean(20)) {
            $sale = (int)round($totalPrice * 0.15);
        } elseif ($dow >= 6 && $this->faker->boolean(15)) {
            $sale = (int)round($totalPrice * 0.10);
        }

        $order->total_price = $totalPrice - $sale;
        $order->sale = $sale;
        $order->save(false);

        foreach ($lineItems as [$entity, $type, $isDetailing, $price]) {
            $line = new OrderService();
            $line->order_id = $order->id;
            $line->entity_id = $entity->id;
            $line->type = $type;
            $line->is_detailing = $isDetailing;
            $line->name = $entity->name;
            $line->price = $price;
            $line->save(false);
        }
    }

    private function resolveStatus(string $date): int
    {
        $today = date('Y-m-d');

        if ($date < $today) {
            return $this->faker->randomElement([
                Orders::STATUS_ARCHIVE,
                Orders::STATUS_ARCHIVE,
                Orders::STATUS_ARCHIVE,
                Orders::STATUS_REMOVED,
            ]);
        }

        if ($date === $today) {
            return $this->faker->randomElement([
                Orders::STATUS_WORK,
                Orders::STATUS_NEW_FROM_WASH,
                Orders::STATUS_NEW_FROM_CLIENT,
                Orders::STATUS_ARCHIVE,
            ]);
        }

        return $this->faker->randomElement([
            Orders::STATUS_NEW_FROM_WASH,
            Orders::STATUS_NEW_FROM_CLIENT,
            Orders::STATUS_NEW_FROM_WASH,
        ]);
    }

    private function pickPersonal(int $status): ?Personal
    {
        if (!in_array($status, [Orders::STATUS_ARCHIVE, Orders::STATUS_WORK], true)) {
            return null;
        }

        if (!$this->staff) {
            return null;
        }

        return $this->faker->randomElement($this->staff);
    }

    /**
     * @param Services|Complexes $entity
     */
    private function getPriceByCarType($entity, int $carType): int
    {
        $field = 'type_' . $carType . '_price';

        return (int)($entity->$field ?? 0);
    }

    private function isSlotFree(string $date, int $post, int $start, int $end): bool
    {
        $key = $date . '-' . $post;
        foreach ($this->occupiedSlots[$key] ?? [] as [$s, $e]) {
            if ($start < $e && $end > $s) {
                return false;
            }
        }

        return true;
    }

    private function occupySlot(string $date, int $post, int $start, int $end): void
    {
        $key = $date . '-' . $post;
        $this->occupiedSlots[$key][] = [$start, $end];
    }

    private function randomCarNumber(): string
    {
        $letters = DemoData::CAR_LETTERS;
        $l1 = $this->faker->randomElement($letters);
        $l2 = $this->faker->randomElement($letters);
        $l3 = $this->faker->randomElement($letters);
        $num = str_pad((string)$this->faker->numberBetween(1, 999), 3, '0', STR_PAD_LEFT);

        return $l1 . $num . $l2 . $l3;
    }
}
