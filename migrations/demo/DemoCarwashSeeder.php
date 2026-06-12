<?php

namespace app\migrations\demo;

use app\commons\helpers\CarTypeHelper;
use app\commons\helpers\DefaultCarwashParams;
use app\migrations\demo\data\DemoData;
use app\models\ar\Advertising;
use app\models\ar\car\CarBrands;
use app\models\ar\car\CarModels;
use app\models\ar\carwash\Carwash;
use app\models\ar\carwash\CarwashBlacklist;
use app\models\ar\carwash\CarwashComfort;
use app\models\ar\carwash\CarwashContacts;
use app\models\ar\carwash\CarwashSales;
use app\models\ar\carwash\CarwashSalesItem;
use app\models\ar\carwash\CarwashSchedule;
use app\models\ar\chat\Chat;
use app\models\ar\chat\ChatMessages;
use app\models\ar\Cities;
use app\models\ar\Clients;
use app\models\ar\complex\Complexes;
use app\models\ar\complex\ComplexMaterials;
use app\models\ar\Materials;
use app\models\ar\order\Orders;
use app\models\ar\order\OrderService;
use app\models\ar\personal\Personal;
use app\models\ar\rbac\AuthItem;
use app\models\ar\service\ServiceMaterials;
use app\models\ar\service\Services;
use app\models\ar\ticket\TicketMessages;
use app\models\ar\ticket\Tickets;
use app\models\ar\Users;
use Yii;
use yii\db\Exception;

/**
 * Наполнение демо-данными автомойки «ЧистоИТочка».
 */
class DemoCarwashSeeder
{
    /** @var int */
    private $carwashId;

    /** @var Personal[] */
    private $staff = [];

    /** @var Clients[] */
    private $clients = [];

    /** @var CarBrands[] */
    private $brands = [];

    /** @var CarModels[] */
    private $models = [];

    /** @var Services[] */
    private $services = [];

    /** @var Complexes[] */
    private $complexes = [];

    /** @var array<string, array<int, array{0: int, 1: int}>> */
    private $occupiedSlots = [];

    public function seed(): void
    {
        $this->ensureRbac();

        $city = $this->findOrCreateCity();
        $carwash = $this->createCarwash($city->id);
        $this->carwashId = $carwash->id;

        $carwash->createDefaultSettings();
        $this->tuneCarwashSettings();
        $this->createStaff($carwash);
        $this->createCarCatalog();
        $this->createPricelist();
        $this->createClients();
        $this->createOrders();
        $this->createSales();
        $this->createBlacklist();
        $this->createChats();
        $this->createTicket();
        $this->createAdvertising();
    }

    public function remove(): void
    {
        $carwash = Carwash::findOne(['name' => DemoData::CARWASH_NAME]);
        if (!$carwash) {
            return;
        }

        $userIds = Personal::find()
            ->where(['carwash_id' => $carwash->id])
            ->select('user_id')
            ->column();

        $carwash->owner_id = null;
        $carwash->save(false);

        Personal::deleteAll(['carwash_id' => $carwash->id]);
        $cityId = $carwash->city_id;
        $carwash->delete();

        if ($cityId) {
            $cityInUse = Carwash::find()->where(['city_id' => $cityId])->exists();
            if (!$cityInUse) {
                Cities::deleteAll(['id' => $cityId]);
            }
        }

        if ($userIds) {
            Users::deleteAll(['id' => $userIds]);
        }

        $emails = array_column(DemoData::STAFF, 'email');
        Users::deleteAll(['email' => $emails]);
    }

    public static function exists(): bool
    {
        return Carwash::find()->where(['name' => DemoData::CARWASH_NAME])->exists();
    }

    public static function materialsExist(): bool
    {
        $carwash = Carwash::findOne(['name' => DemoData::CARWASH_NAME]);
        if (!$carwash) {
            return false;
        }

        return Materials::find()->where(['carwash_id' => $carwash->id])->exists();
    }

    public function seedMaterials(): void
    {
        $carwash = Carwash::findOne(['name' => DemoData::CARWASH_NAME]);
        if (!$carwash) {
            throw new Exception(
                'Автомойка «' . DemoData::CARWASH_NAME . '» не найдена. Сначала: php yii migrate'
            );
        }

        $this->carwashId = $carwash->id;
        $this->services = Services::find()->where(['carwash_id' => $this->carwashId])->all();
        $this->complexes = Complexes::find()->where(['carwash_id' => $this->carwashId])->all();

        $materialsByName = $this->createMaterials();
        $this->linkServiceMaterials($materialsByName);
        $this->linkComplexMaterials($materialsByName);
    }

    public function removeMaterials(): void
    {
        $carwash = Carwash::findOne(['name' => DemoData::CARWASH_NAME]);
        if (!$carwash) {
            return;
        }

        Materials::deleteAll(['carwash_id' => $carwash->id]);
    }

    private function ensureRbac(): void
    {
        $auth = Yii::$app->authManager;
        if (!$auth->getRole(AuthItem::ROLE_OWNER)) {
            throw new Exception(
                'RBAC не инициализирован. Сначала выполните: php yii utility/init-rbac'
            );
        }
    }

    private function findOrCreateCity(): Cities
    {
        $city = Cities::findOne(['name' => DemoData::CITY_NAME]);
        if ($city) {
            return $city;
        }

        $city = new Cities();
        $city->name = DemoData::CITY_NAME;
        $city->save(false);

        return $city;
    }

    private function createCarwash(int $cityId): Carwash
    {
        $carwash = new Carwash();
        $carwash->city_id = $cityId;
        $carwash->name = DemoData::CARWASH_NAME;
        $carwash->address = DemoData::CARWASH_ADDRESS;
        $carwash->timezone = 3;
        $carwash->geo_lat = '55.751244';
        $carwash->geo_long = '37.618423';
        $carwash->save(false);

        return $carwash;
    }

    private function tuneCarwashSettings(): void
    {
        $schedule = CarwashSchedule::findOne(['carwash_id' => $this->carwashId]);
        if ($schedule) {
            foreach (['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'] as $day) {
                $schedule->{$day . '_start'} = '08:00:00';
                $schedule->{$day . '_end'} = '22:00:00';
                $schedule->{'is_work_' . $day} = true;
            }
            $schedule->save(false);
        }

        $comfort = CarwashComfort::findOne(['carwash_id' => $this->carwashId]);
        if ($comfort) {
            $comfort->pay_cash = 1;
            $comfort->pay_terminal = 1;
            $comfort->cf_toilet = 1;
            $comfort->cf_rest_zone = 1;
            $comfort->cf_coffee = 1;
            $comfort->save(false);
        }

        $contacts = CarwashContacts::findOne(['carwash_id' => $this->carwashId]);
        if (!$contacts) {
            $contacts = new CarwashContacts();
            $contacts->carwash_id = $this->carwashId;
        }
        $contacts->phone_1 = '+7 (495) 123-45-67';
        $contacts->phone_2 = '+7 (926) 555-12-34';
        $contacts->email = 'info@chistoitochka.local';
        $contacts->site = 'https://chistoitochka.local';
        $contacts->telegram = '@chistoitochka_msk';
        $contacts->whatsapp = '+79265551234';
        $contacts->save(false);
    }

    private function createStaff(Carwash $carwash): void
    {
        $auth = Yii::$app->authManager;
        $ownerPersonal = null;

        foreach (DemoData::STAFF as $row) {
            $user = new Users();
            $user->guid = Yii::$app->security->generateRandomString(32);
            $user->status = Users::STATUS_ACTIVE;
            $user->firstname = $row['firstname'];
            $user->lastname = $row['lastname'];
            $user->patronymic = $row['patronymic'];
            $user->email = $row['email'];
            $user->lang_id = Users::LANG_DEFAULT;
            $user->setPassword(DemoData::PASSWORD);
            $user->save(false);

            $personal = new Personal();
            $personal->user_id = $user->id;
            $personal->carwash_id = $this->carwashId;
            $personal->is_approved = Personal::IS_APPROVED;
            $personal->post = $row['post'];
            $personal->salary_type = $row['salary_type'];
            $personal->salary = $row['salary'];
            $personal->save(false);

            $role = $auth->getRole($row['role']);
            if ($role) {
                $auth->assign($role, $personal->id);
            }
            foreach ($row['permissions'] as $permName) {
                $perm = $auth->getPermission($permName);
                if ($perm) {
                    $auth->assign($perm, $personal->id);
                }
            }

            $this->staff[] = $personal;

            if ($row['post'] === Personal::POST_OWNER) {
                $ownerPersonal = $personal;
            }
        }

        if ($ownerPersonal) {
            $carwash->owner_id = $ownerPersonal->id;
            $carwash->save(false);
        }
    }

    private function createCarCatalog(): void
    {
        foreach (DemoData::CAR_BRANDS as $brandTitle => $modelTitles) {
            $brand = new CarBrands();
            $brand->carwash_id = null;
            $brand->title = $brandTitle;
            $brand->save(false);
            $this->brands[] = $brand;

            foreach ($modelTitles as $modelTitle) {
                $model = new CarModels();
                $model->car_brand_id = $brand->id;
                $model->carwash_id = null;
                $model->title = $modelTitle;
                $model->save(false);
                $this->models[] = $model;
            }
        }
    }

    private function createPricelist(): void
    {
        $helper = new DefaultCarwashParams($this->carwashId);
        $helper->createDefaultPrices();

        $this->services = Services::find()->where(['carwash_id' => $this->carwashId])->all();
        $this->complexes = Complexes::find()->where(['carwash_id' => $this->carwashId])->all();

        $materialsByName = $this->createMaterials();
        $this->linkServiceMaterials($materialsByName);
        $this->linkComplexMaterials($materialsByName);
    }

    /**
     * @return array<string, Materials>
     */
    private function createMaterials(): array
    {
        $byName = [];

        foreach (DemoData::WASH_MATERIALS as $row) {
            $byName[$row['name']] = $this->findOrCreateMaterial($row['name'], $row['price'], false);
        }

        foreach (DemoData::DETAILING_MATERIALS as $row) {
            $byName[$row['name']] = $this->findOrCreateMaterial($row['name'], $row['price'], true);
        }

        return $byName;
    }

    private function findOrCreateMaterial(string $name, int $price, bool $isDetailing): Materials
    {
        $existing = Materials::findOne([
            'carwash_id' => $this->carwashId,
            'name' => $name,
        ]);
        if ($existing) {
            return $existing;
        }

        $material = new Materials();
        $material->carwash_id = $this->carwashId;
        $material->is_detailing = $isDetailing ? 1 : 0;
        $material->name = $name;
        $material->price = $price;
        $material->save(false);

        return $material;
    }

    /**
     * @param array<string, Materials> $materialsByName
     */
    private function linkServiceMaterials(array $materialsByName): void
    {
        foreach (DemoData::SERVICE_MATERIAL_LINKS as $serviceName => $links) {
            $service = Services::findOne([
                'carwash_id' => $this->carwashId,
                'name' => $serviceName,
            ]);
            if (!$service) {
                continue;
            }

            foreach ($links as $link) {
                $material = $materialsByName[$link['material']] ?? null;
                if (!$material) {
                    continue;
                }

                $exists = ServiceMaterials::find()
                    ->where(['service_id' => $service->id, 'material_id' => $material->id])
                    ->exists();
                if ($exists) {
                    continue;
                }

                $serviceMaterial = new ServiceMaterials();
                $serviceMaterial->service_id = $service->id;
                $serviceMaterial->material_id = $material->id;
                $serviceMaterial->price = $link['price'];
                $serviceMaterial->save(false);
            }
        }
    }

    /**
     * @param array<string, Materials> $materialsByName
     */
    private function linkComplexMaterials(array $materialsByName): void
    {
        foreach (DemoData::COMPLEX_MATERIAL_LINKS as $complexName => $links) {
            $complex = Complexes::findOne([
                'carwash_id' => $this->carwashId,
                'name' => $complexName,
            ]);
            if (!$complex) {
                continue;
            }

            foreach ($links as $link) {
                $material = $materialsByName[$link['material']] ?? null;
                if (!$material) {
                    continue;
                }

                $exists = ComplexMaterials::find()
                    ->where(['complex_id' => $complex->id, 'material_id' => $material->id])
                    ->exists();
                if ($exists) {
                    continue;
                }

                $complexMaterial = new ComplexMaterials();
                $complexMaterial->complex_id = $complex->id;
                $complexMaterial->material_id = $material->id;
                $complexMaterial->price = $link['price'];
                $complexMaterial->save(false);
            }
        }
    }

    private function createClients(): void
    {
        foreach (DemoData::CLIENTS as $row) {
            $client = new Clients();
            $client->guid = Yii::$app->security->generateRandomString(32);
            $client->carwash_id = $this->carwashId;
            $client->full_name = $row['full_name'];
            $client->phone = $row['phone'];
            $client->email = $row['email'];
            $client->is_subscribed = $row['subscribed'];
            $client->reputation = 0;
            $client->save(false);
            $this->clients[] = $client;
        }
    }

    private function createOrders(): void
    {
        mt_srand(2025);

        $washServices = array_values(array_filter($this->services, static function (Services $s) {
            return !$s->is_detailing;
        }));
        $washComplexes = array_values(array_filter($this->complexes, static function (Complexes $c) {
            return !$c->is_detailing;
        }));

        $managerPersonal = $this->findStaffByPost(Personal::POST_MANAGER);
        $adminPersonal = $this->findStaffByPost(Personal::POST_ADMIN);
        $washers = array_values(array_filter($this->staff, static function (Personal $p) {
            return (int)$p->post === Personal::POST_WASHER;
        }));

        $statusPool = array_merge(
            array_fill(0, 150, Orders::STATUS_ARCHIVE),
            array_fill(0, 16, Orders::STATUS_WORK),
            array_fill(0, 7, Orders::STATUS_NEW_FROM_WASH),
            array_fill(0, 7, Orders::STATUS_NEW_FROM_CLIENT),
            array_fill(0, 20, Orders::STATUS_REMOVED)
        );
        shuffle($statusPool);

        $today = strtotime('today');
        $orderCount = 200;

        for ($i = 0; $i < $orderCount; $i++) {
            $isFuture = $i < 12;
            if ($isFuture) {
                $dayOffset = mt_rand(1, 14);
                $status = mt_rand(0, 1) ? Orders::STATUS_NEW_FROM_WASH : Orders::STATUS_NEW_FROM_CLIENT;
            } else {
                $dayOffset = -mt_rand(0, 90);
                $status = $statusPool[$i - 12] ?? Orders::STATUS_ARCHIVE;
            }

            $date = date('Y-m-d', strtotime("$dayOffset days", $today));
            $dow = (int)date('N', strtotime($date));
            if (!$isFuture && ($dow >= 6) && mt_rand(0, 100) < 35) {
                // выходные чуть загруженнее — перегенерируем слот
            }

            $carType = mt_rand(1, 5);
            $duration = mt_rand(20, 75);
            $slot = $this->pickFreeSlot($date, $duration);
            if (!$slot) {
                continue;
            }

            [$post, $startTime, $endTime] = $slot;

            $client = $this->clients[array_rand($this->clients)];
            $brand = $this->brands[array_rand($this->brands)];
            $brandModels = array_values(array_filter($this->models, static function (CarModels $m) use ($brand) {
                return (int)$m->car_brand_id === (int)$brand->id;
            }));
            $model = $brandModels ? $brandModels[array_rand($brandModels)] : $this->models[array_rand($this->models)];

            $personal = null;
            if ($status === Orders::STATUS_ARCHIVE || $status === Orders::STATUS_WORK) {
                $personal = mt_rand(0, 1) ? $managerPersonal : (mt_rand(0, 1) ? $adminPersonal : $washers[array_rand($washers)]);
            }

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
            $order->car_region = DemoData::CAR_REGIONS[array_rand(DemoData::CAR_REGIONS)];
            $order->color = DemoData::CAR_COLORS[array_rand(DemoData::CAR_COLORS)];
            $order->car_brand_id = $brand->id;
            $order->car_model_id = $model->id;
            $order->client_fullname = $client->full_name;
            $order->client_phone = $client->phone;
            $order->work_time = $endTime - $startTime;
            $order->status = $status;
            $order->admin_comment = DemoData::ORDER_COMMENTS[array_rand(DemoData::ORDER_COMMENTS)];
            if ($status === Orders::STATUS_NEW_FROM_CLIENT) {
                $order->client_comment = 'Запись через онлайн-форму';
            }

            $totalPrice = 0;
            $itemsCount = mt_rand(1, 3);
            $lineItems = [];

            for ($j = 0; $j < $itemsCount; $j++) {
                if (mt_rand(0, 1) && $washComplexes) {
                    $entity = $washComplexes[array_rand($washComplexes)];
                    $type = OrderService::TYPE_COMPLEX;
                    $isDetailing = (bool)$entity->is_detailing;
                } else {
                    $entity = $washServices[array_rand($washServices)];
                    $type = OrderService::TYPE_SERVICE;
                    $isDetailing = (bool)$entity->is_detailing;
                }
                $price = $this->getPriceByCarType($entity, $carType);
                $totalPrice += $price;
                $lineItems[] = [$entity, $type, $isDetailing, $price];
            }

            $sale = 0;
            if ($client->is_subscribed && mt_rand(0, 100) < 20) {
                $sale = (int)round($totalPrice * 0.15);
            } elseif ($dow >= 6 && mt_rand(0, 100) < 15) {
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
    }

    /**
     * @param Services|Complexes $entity
     */
    private function getPriceByCarType($entity, int $carType): int
    {
        $field = 'type_' . $carType . '_price';

        return (int)($entity->$field ?? 0);
    }

    private function pickFreeSlot(string $date, int $duration): ?array
    {
        $dayStart = 8 * 60;
        $dayEnd = 21 * 60 + 30;

        for ($attempt = 0; $attempt < 40; $attempt++) {
            $post = mt_rand(1, 3);
            $start = $dayStart + mt_rand(0, (int)(($dayEnd - $dayStart - $duration) / 15)) * 15;
            $end = $start + $duration;

            if ($this->isSlotFree($date, $post, $start, $end)) {
                $this->occupySlot($date, $post, $start, $end);

                return [$post, $start, $end];
            }
        }

        return null;
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
        $l1 = $letters[array_rand($letters)];
        $l2 = $letters[array_rand($letters)];
        $l3 = $letters[array_rand($letters)];
        $num = str_pad((string)mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

        return $l1 . $num . $l2 . $l3;
    }

    private function createSales(): void
    {
        $weekendSale = new CarwashSales();
        $weekendSale->carwash_id = $this->carwashId;
        $weekendSale->name = 'Скидка выходного дня';
        $weekendSale->description = 'Скидка 10% на все услуги мойки по субботам и воскресеньям';
        $weekendSale->start_at = date('Y-m-01');
        $weekendSale->end_at = date('Y-12-31');
        $weekendSale->only_subscribers = CarwashSales::SALE_FOR_ALL;
        $weekendSale->for_service_type = CarwashSales::SERVICE_TYPE_SERVICE;
        $weekendSale->sale_type = CarwashSales::SALE_TYPE_PERCENT;
        $weekendSale->sale = 10;
        $weekendSale->rounding_to = CarwashSales::ROUND_NONE;
        $weekendSale->sum_up_discount = CarwashSales::NOT_SUM_UP_DISCOUNT;
        $weekendSale->apply_greater = CarwashSales::NOT_APPLY_GREATER;
        $weekendSale->position = 1;
        $weekendSale->save(false);

        $subSale = new CarwashSales();
        $subSale->carwash_id = $this->carwashId;
        $subSale->name = 'Для подписчиков';
        $subSale->description = 'Скидка 15% для клиентов с подпиской';
        $subSale->start_at = date('Y-m-01');
        $subSale->end_at = date('Y-12-31');
        $subSale->only_subscribers = CarwashSales::SALE_FOR_SUBSCRIBER;
        $subSale->for_service_type = CarwashSales::SERVICE_TYPE_COMPLEX;
        $subSale->sale_type = CarwashSales::SALE_TYPE_PERCENT;
        $subSale->sale = 15;
        $subSale->rounding_to = CarwashSales::ROUND_NONE;
        $subSale->sum_up_discount = CarwashSales::NOT_SUM_UP_DISCOUNT;
        $subSale->apply_greater = CarwashSales::NOT_APPLY_GREATER;
        $subSale->position = 2;
        $subSale->save(false);

        $standardComplex = null;
        foreach ($this->complexes as $complex) {
            if ($complex->name === 'Стандарт') {
                $standardComplex = $complex;
                break;
            }
        }
        if ($standardComplex) {
            $item = new CarwashSalesItem();
            $item->sale_id = $subSale->id;
            $item->complex_id = $standardComplex->id;
            $item->save(false);
        }
    }

    private function createBlacklist(): void
    {
        // Клиент №22 — Чернов, был груб с персоналом
        $client = $this->clients[22] ?? $this->clients[0];

        $entry = new CarwashBlacklist();
        $entry->carwash_id = $this->carwashId;
        $entry->client_id = $client->id;
        $entry->car_number = 'Х777ХХ';
        $entry->car_region = '77';
        $entry->save(false);
    }

    private function createChats(): void
    {
        $admin = $this->findStaffByPost(Personal::POST_ADMIN);

        foreach (DemoData::CHAT_DIALOGS as $dialog) {
            $client = $this->clients[$dialog['client_index']] ?? $this->clients[0];

            $chat = new Chat();
            $chat->carwash_id = $this->carwashId;
            $chat->client_id = $client->id;
            $chat->car_number = $this->randomCarNumber();
            $chat->car_region = '77';
            $chat->save(false);

            foreach ($dialog['messages'] as $msg) {
                $message = new ChatMessages();
                $message->chat_id = $chat->id;
                $message->text = $msg['text'];
                $message->is_viewed = ChatMessages::IS_VIEWED;
                if ($msg['from'] === 'client') {
                    $message->client_id = $client->id;
                } else {
                    $message->personal_id = $admin ? $admin->id : null;
                }
                $message->save(false);
            }
        }
    }

    private function createTicket(): void
    {
        $owner = $this->findStaffByPost(Personal::POST_OWNER);
        if (!$owner) {
            return;
        }

        $ticket = new Tickets();
        $ticket->carwash_id = $this->carwashId;
        $ticket->personal_id = $owner->id;
        $ticket->text = 'Не могу изменить расписание на праздничные дни — кнопка сохранения не срабатывает.';
        $ticket->is_closed = false;
        $ticket->save(false);

        $reply = new TicketMessages();
        $reply->ticket_id = $ticket->id;
        $reply->personal_id = $owner->id;
        $reply->text = 'Попробуйте обновить страницу. Если не поможет — напишите, на каком браузере работаете.';
        $reply->save(false);
    }

    private function createAdvertising(): void
    {
        $ad = new Advertising();
        $ad->carwash_id = $this->carwashId;
        $ad->type = 'client-lk';
        $ad->title = 'Весенняя акция';
        $ad->text = 'Комплекс «Стандарт» со скидкой 10% до конца месяца. Записывайтесь онлайн!';
        $ad->phone = '+7 (495) 123-45-67';
        $ad->status = Advertising::STATUS_APPROVED;
        $ad->views = 127;
        $ad->save(false);
    }

    private function findStaffByPost(int $post): ?Personal
    {
        foreach ($this->staff as $personal) {
            if ((int)$personal->post === $post) {
                return $personal;
            }
        }

        return null;
    }
}
