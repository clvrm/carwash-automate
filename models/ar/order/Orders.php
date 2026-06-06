<?php

namespace app\models\ar\order;

use app\models\ar\car\CarBrands;
use app\models\ar\car\CarModels;
use app\models\ar\carwash\Carwash;
use app\models\ar\chat\Chat;
use app\models\ar\Clients;
use app\models\ar\personal\Personal;
use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int $carwash_id
 * @property int|null $client_id
 * @property int|null $personal_id Менеджер
 * @property string|null $personal_fullname После архивирования заказа, указываем фио
 * @property string $date
 * @property int|null $post
 * @property int|null $start_time
 * @property int|null $end_time
 * @property int|null $car_type
 * @property string|null $car_number
 * @property int|null $car_region
 * @property string|null $color
 * @property int|null $car_brand_id
 * @property int|null $car_model_id
 * @property string|null $client_fullname
 * @property string|null $client_phone
 * @property int|null $total_price
 * @property int|null $sale
 * @property int|null $work_time
 * @property int|null $status
 * @property string|null $admin_comment
 * @property string|null $client_comment
 * @property string|null $updated_at
 * @property string|null $created_at
 *
 * @property Chat[] $chats
 * @property OrderService[] $orderServices
 * @property CarBrands $carBrand
 * @property CarModels $carModel
 * @property Carwash $carwash
 * @property Clients $client
 * @property Personal $personal
 */
class Orders extends \yii\db\ActiveRecord
{
    /**
     *
     */
    public const STATUS_NEW_FROM_WASH = 10;
    public const STATUS_NEW_FROM_CLIENT = 11;
    public const STATUS_WORK = 20;
    public const STATUS_ARCHIVE = 30;
    public const STATUS_REMOVED = 40;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carwash_id', 'date'], 'required'],
            [['carwash_id', 'client_id', 'personal_id', 'post', 'start_time', 'end_time', 'car_type', 'car_region', 'car_brand_id', 'car_model_id', 'total_price', 'sale', 'work_time', 'status'], 'integer'],
            [['date', 'updated_at', 'created_at'], 'safe'],
            [['admin_comment', 'client_comment'], 'string'],
            [['personal_fullname'], 'string', 'max' => 255],
            [['car_number'], 'string', 'max' => 50],
            [['color'], 'string', 'max' => 64],
            [['client_fullname'], 'string', 'max' => 128],
            [['client_phone'], 'string', 'max' => 32],
            [['car_brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarBrands::className(), 'targetAttribute' => ['car_brand_id' => 'id']],
            [['car_model_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarModels::className(), 'targetAttribute' => ['car_model_id' => 'id']],
            [['carwash_id'], 'exist', 'skipOnError' => true, 'targetClass' => Carwash::className(), 'targetAttribute' => ['carwash_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['personal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Personal::className(), 'targetAttribute' => ['personal_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'carwash_id' => 'ID автомойки',
            'client_id' => 'ID клиента',
            'personal_id' => 'ID сотрудника',
            'personal_fullname' => 'Назначенный сотрудник',
            'date' => 'Дата',
            'post' => 'Пост',
            'start_time' => 'Время начала',
            'end_time' => 'Время окончания',
            'car_type' => 'Тип авто',
            'car_number' => 'Номер автомобиля',
            'car_region' => 'Регион автомобиля',
            'color' => 'Цвет',
            'car_brand_id' => 'ID бренда',
            'car_model_id' => 'ID модели',
            'client_fullname' => 'Имя клиента',
            'client_phone' => 'Телефон',
            'total_price' => 'Итоговая стоимость',
            'sale' => 'Скидка',
            'work_time' => 'Время на работы',
            'status' => 'Статус',
            'admin_comment' => 'Комментарий администратора',
            'client_comment' => 'Комментарий клиента',
            'updated_at' => 'Дата обновления',
            'created_at' => 'Дата добавления',
        ];
    }

    /**
     * @return string[]
     */
    public static function statusLabels()
    {
        return [
            self::STATUS_NEW_FROM_WASH => 'Новый',
            self::STATUS_NEW_FROM_CLIENT => 'Создан клиентом',
            self::STATUS_WORK => 'В работе',
            self::STATUS_ARCHIVE => 'В архиве',
            self::STATUS_REMOVED => 'Удален',
        ];
    }

    public static function getCssStatusClass($status)
    {
        switch ($status) {
            case 10:
                $statusClass = 'status-new-wash';
                break;
            case 11:
                $statusClass = 'status-new-client';
                break;
            case 20:
                $statusClass = 'status-work';
                break;
            case 30:
                $statusClass = 'status-archive';
                break;
            case 40:
                $statusClass = 'status-block';
                break;
            default :
                $statusClass = 'status-default';
                break;
        }

        return $statusClass ?? '';
    }

    /**
     * @return string
     */
    public function currentStatusLabel(): string
    {
        $labels = self::statusLabels();

        return $labels[$this->status] ?? '';
    }

    /**
     * Gets query for [[Chats]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChats()
    {
        return $this->hasMany(Chat::className(), ['order_id' => 'id']);
    }

    /**
     * Gets query for [[OrderServices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderServices()
    {
        return $this->hasMany(OrderService::className(), ['order_id' => 'id']);
    }

    /**
     * Gets query for [[CarBrand]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarBrand()
    {
        return $this->hasOne(CarBrands::className(), ['id' => 'car_brand_id']);
    }

    /**
     * Gets query for [[CarModel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarModel()
    {
        return $this->hasOne(CarModels::className(), ['id' => 'car_model_id']);
    }

    /**
     * Gets query for [[Carwash]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarwash()
    {
        return $this->hasOne(Carwash::className(), ['id' => 'carwash_id']);
    }

    /**
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Clients::className(), ['id' => 'client_id']);
    }

    /**
     * Gets query for [[Personal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonal()
    {
        return $this->hasOne(Personal::className(), ['id' => 'personal_id']);
    }
}
