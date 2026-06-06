<?php

namespace app\models\ar\carwash;

use app\models\ar\Advertising;
use app\models\ar\car\CarBrands;
use app\models\ar\car\CarModels;
use app\models\ar\chat\Chat;
use app\models\ar\Cities;
use app\models\ar\Clients;
use app\models\ar\complex\Complexes;
use app\models\ar\Materials;
use app\models\ar\order\Orders;
use app\models\ar\personal\Personal;
use app\models\ar\service\Services;
use app\models\ar\ticket\Tickets;
use Yii;

/**
 * This is the model class for table "carwash".
 *
 * @property int $id
 * @property int|null $owner_id
 * @property int|null $city_id
 * @property string $name
 * @property string|null $avatar
 * @property string|null $address
 * @property int|null $timezone
 * @property string|null $geo_lat
 * @property string|null $geo_long
 * @property string|null $created_at
 *
 * @property Advertising[] $advertisings
 * @property CarBrands[] $carBrands
 * @property CarModels[] $carModels
 * @property Cities $city
 * @property Personal $owner
 * @property CarwashBlacklist[] $carwashBlacklists
 * @property CarwashComfort[] $carwashComforts
 * @property CarwashContacts[] $carwashContacts
 * @property CarwashImages[] $carwashImages
 * @property CarwashSales[] $carwashSales
 * @property CarwashSchedule[] $carwashSchedules
 * @property CarwashSettings[] $carwashSettings
 * @property Chat[] $chats
 * @property Clients[] $clients
 * @property Complexes[] $complexes
 * @property Materials[] $materials
 * @property Orders[] $orders
 * @property Personal[] $personals
 * @property Services[] $services
 * @property Tickets[] $tickets
 */
class Carwash extends \yii\db\ActiveRecord
{
    public const DEFAULT_NAME = 'Моя автомойка';
    public const DEFAULT_TIMEZONE = 3; // Москва (UTC+3)

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carwash';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['owner_id', 'city_id', 'timezone'], 'integer'],
            [['name'], 'required'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 128],
            [['avatar'], 'string', 'max' => 512],
            [['address'], 'string', 'max' => 255],
            [['name'], 'string', 'min' => 2],
            [['geo_lat', 'geo_long'], 'string', 'max' => 16],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Personal::className(), 'targetAttribute' => ['owner_id' => 'id']],
        ];
    }

    /**
     * @return array|string[]
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'owner_id' => 'ID владельца',
            'city_id' => 'ID города',
            'name' => 'Название',
            'avatar' => 'Аватар',
            'address' => 'Адрес',
            'timezone' => 'Временная зона',
            'geo_lat' => 'Широта',
            'geo_long' => 'Долгота',
            'created_at' => 'Дата добавления',
        ];
    }

    /**
     * @param false $id
     * @return string|string[]
     */
    public function getTimezonesLabels($id = false)
    {
        $timezones = [
            2 => 'Калининград (UTC+2)',
            3 => 'Москва (UTC+3)',
            4 => 'Самара (UTC+4)',
            5 => 'Екатеринбург (UTC+5)',
            6 => 'Омск (UTC+6)',
            7 => 'Красноярск (UTC+7)',
            8 => 'Иркутск (UTC+8)',
            9 => 'Якутск (UTC+9)',
            10 => 'Владивосток (UTC+10)',
            11 => 'Магадан (UTC+11)',
            12 => 'Камчатка (UTC+12)',
        ];
        if ($id) {
            return $timezones[$id] ?? '';
        }
        return $timezones;
    }

    /**
     * Создание дефолтных настроек для автомойки
     */
    public function createDefaultSettings(): void
    {
        if (!$this->timezone) {
            $this->timezone = self::DEFAULT_TIMEZONE;
            $this->save();
        }
        $settings = CarwashSettings::findOne(['carwash_id' => $this->id]);
        $schedule = CarwashSchedule::findOne(['carwash_id' => $this->id]);
        $comfort = CarwashComfort::findOne(['carwash_id' => $this->id]);
        if (!$settings) {
            $settings = new CarwashSettings();
            $settings->carwash_id = $this->id;
            $settings->post_count = 3;
            $settings->online_record = 1;
            $settings->only_subscribers = 0;
            $settings->can_record_blacklist = 1;
            $settings->checkout_time = 0;
            $settings->dense_record = 0;
            $settings->max_recording_range = 7; // неделя
            $settings->average_duration = 0;
            $settings->until_last_client = 0;
            $settings->staff_delay_time = 0;
            $settings->service_time_multiplier = 0;
            $settings->save();
        }
        // Заполняем поле код для подписчиков
        $settings->generateSubscriberCode();
        $settings->save();

        if (!$schedule) {
            $schedule = new CarwashSchedule();
            $schedule->carwash_id = $this->id;
            $schedule->monday_start = $schedule->tuesday_start = $schedule->wednesday_start = $schedule->thursday_start
                = $schedule->friday_start = $schedule->saturday_start = $schedule->sunday_start = '00:00:00';

            $schedule->monday_end = $schedule->tuesday_end = $schedule->wednesday_end = $schedule->thursday_end
                = $schedule->friday_end = $schedule->saturday_end = $schedule->sunday_end = '23:59:00';

            $schedule->is_work_monday = $schedule->is_work_tuesday = $schedule->is_work_wednesday = $schedule->is_work_thursday
                = $schedule->is_work_friday = $schedule->is_work_saturday = $schedule->is_work_sunday = true;

            $schedule->save();
        }

        if (!$comfort) {
            $comfort = new CarwashComfort();
            $comfort->carwash_id = $this->id;
            $comfort->pay_cash = 1;
            $comfort->pay_online = 0;
            $comfort->pay_terminal = 0;
            $comfort->pay_invoice = 0;
            $comfort->cf_ATM = 0;
            $comfort->cf_postomat = 0;
            $comfort->cf_cafe = 0;
            $comfort->cf_toilet = 0;
            $comfort->cf_shop = 0;
            $comfort->cf_rest_zone = 0;
            $comfort->cf_coffee = 0;
            $comfort->cf_TV = 0;
            $comfort->cf_videocam = 0;
            $comfort->cf_rest_zone = 0;
            $comfort->save();
        }
    }

    /**
     * Gets query for [[Advertisings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdvertisings()
    {
        return $this->hasMany(Advertising::className(), ['carwash_id' => 'id']);
    }

    /**
     * Gets query for [[CarBrands]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarBrands()
    {
        return $this->hasMany(CarBrands::className(), ['carwash_id' => 'id']);
    }

    /**
     * Gets query for [[CarModels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarModels()
    {
        return $this->hasMany(CarModels::className(), ['carwash_id' => 'id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(Cities::className(), ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Owner]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOwner()
    {
        return $this->hasOne(Personal::className(), ['id' => 'owner_id']);
    }

    /**
     * Gets query for [[CarwashBlacklists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarwashBlacklists()
    {
        return $this->hasMany(CarwashBlacklist::className(), ['carwash_id' => 'id']);
    }

    /**
     * Gets query for [[CarwashComforts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarwashComforts()
    {
        return $this->hasMany(CarwashComfort::className(), ['carwash_id' => 'id']);
    }

    /**
     * Gets query for [[CarwashContacts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarwashContacts()
    {
        return $this->hasMany(CarwashContacts::className(), ['carwash_id' => 'id']);
    }

    /**
     * Gets query for [[CarwashImages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarwashImages()
    {
        return $this->hasMany(CarwashImages::className(), ['carwash_id' => 'id']);
    }

    /**
     * Gets query for [[CarwashSales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarwashSales()
    {
        return $this->hasMany(CarwashSales::className(), ['carwash_id' => 'id']);
    }

    /**
     * Gets query for [[CarwashSchedules]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarwashSchedules()
    {
        return $this->hasMany(CarwashSchedule::className(), ['carwash_id' => 'id']);
    }

    /**
     * Gets query for [[CarwashSettings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarwashSettings()
    {
        return $this->hasMany(CarwashSettings::className(), ['carwash_id' => 'id']);
    }

    /**
     * Gets query for [[Chats]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChats()
    {
        return $this->hasMany(Chat::className(), ['carwash_id' => 'id']);
    }

    /**
     * Gets query for [[Clients]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClients()
    {
        return $this->hasMany(Clients::className(), ['carwash_id' => 'id']);
    }

    /**
     * Gets query for [[Complexes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComplexes()
    {
        return $this->hasMany(Complexes::className(), ['carwash_id' => 'id']);
    }

    /**
     * Gets query for [[Materials]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterials()
    {
        return $this->hasMany(Materials::className(), ['carwash_id' => 'id']);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['carwash_id' => 'id']);
    }

    /**
     * Gets query for [[Personals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonals()
    {
        return $this->hasMany(Personal::className(), ['carwash_id' => 'id']);
    }

    /**
     * Gets query for [[Services]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(Services::className(), ['carwash_id' => 'id']);
    }

    /**
     * Gets query for [[Tickets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Tickets::className(), ['carwash_id' => 'id']);
    }
}
