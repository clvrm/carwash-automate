<?php

namespace app\models\ar\carwash;

use Yii;

/**
 * This is the model class for table "carwash_sales".
 *
 * @property int $id
 * @property int $carwash_id
 * @property string $name
 * @property string|null $description
 * @property string $start_at
 * @property string $end_at
 * @property int|null $only_subscribers Для подписчиков / всех
 * @property int|null $for_service_type комплекс / услуга
 * @property int|null $sale_type Процент / скидка
 * @property int|null $sale
 * @property int|null $rounding_to Округлять до? Нет / 10 / 100
 * @property int|null $sum_up_discount Суммировать скидку
 * @property int|null $apply_greater Применять бо'льшую скидку?
 * @property int $position
 * @property string|null $created_at
 *
 * @property Carwash $carwash
 * @property CarwashSalesItem[] $carwashSalesItems
 *
 */
class CarwashSales extends \yii\db\ActiveRecord
{
    public const SALE_FOR_ALL = 0; // для всех
    public const SALE_FOR_SUBSCRIBER = 1; // для подписчиков
    public const SERVICE_TYPE_SERVICE = 0; // действует на услугу
    public const SERVICE_TYPE_COMPLEX = 1; // действует на комплекс
    public const SALE_TYPE_PERCENT = 0; // Процент скидки
    public const SALE_TYPE_PRICE = 1; // От стоимости
    public const ROUND_NONE = 0; // Нет округления скидки
    public const SUM_UP_DISCOUNT = 1; // Суммируется ли скидка
    public const NOT_SUM_UP_DISCOUNT = 0;
    public const APPLY_GREATER = 1; // Применять ли большую скидку
    public const NOT_APPLY_GREATER = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carwash_sales';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carwash_id', 'name', 'start_at', 'end_at'], 'required'],
            [['carwash_id', 'only_subscribers', 'for_service_type', 'sale_type', 'sale', 'rounding_to', 'sum_up_discount', 'apply_greater', 'position'], 'integer'],
            [['start_at', 'end_at', 'created_at'], 'safe'],
            [['name'], 'string', 'max' => 128],
            [['description'], 'string', 'max' => 512],
            [['carwash_id'], 'exist', 'skipOnError' => true, 'targetClass' => Carwash::className(), 'targetAttribute' => ['carwash_id' => 'id']],
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
            'name' => 'Название',
            'description' => 'Описание',
            'start_at' => 'Дата начала',
            'end_at' => 'Дата окончания',
            'only_subscribers' => 'Только для подписчиков?',
            'for_service_type' => 'Тип услуги',
            'sale_type' => 'Тип скидки',
            'sale' => 'Скидка',
            'rounding_to' => 'Округление до',
            'sum_up_discount' => 'Суммировать скидку',
            'apply_greater' => 'Применять только большую скидку?',
            'position' => 'Порядок',
            'created_at' => 'Дата добавления',
        ];
    }

    /**
     * @return string
     */
    public function saleForLabel(): string
    {
        $salesFor = [
            self::SALE_FOR_ALL => 'Для всех',
            self::SALE_FOR_SUBSCRIBER => 'Для подписчиков',
        ];

        return $salesFor[$this->only_subscribers] ?? '';
    }


    public function saleItemsList(): string
    {
        $salesItems = $this->getCarwashSalesItems()->all();

        $servicesList = [];
        $complexesList = [];

        /** @var CarwashSalesItem $item */
        foreach ($salesItems as $item) {
            if (!empty($item->service_id)) {
                $servicesList[] = $item->service->name ?? '';
            }
            if (!empty($item->complex_id)) {
                $complexesList[] = $item->complex->name ?? '';
            }
        }

        $itemsString = '';
        if ($complexesList) {

            if (count($complexesList) == 1) {
                $itemsString .= 'Комплекс: ';
            } else {
                $itemsString .= 'Комплексы: ';
            }
            $itemsString .= implode(', ', $complexesList);
        }
        if ($servicesList) {
            if (!empty($itemsString)) {
                $itemsString .= '; ';
            }
            if (count($servicesList) == 1) {
                $itemsString .= 'Услуга: ';
            } else {
                $itemsString .= 'Услуги: ';
            }
            $itemsString .= implode(', ', $servicesList);
        }

        return $itemsString ?? '';
    }


    /**
     * @return string
     */
    public function saleWithTypeLabel(): string
    {
        if ($this->sale_type == self::SALE_TYPE_PERCENT) {
            return $this->sale . '% от суммы';
        }
        if ($this->sale_type == self::SALE_TYPE_PRICE) {
            return $this->sale . ' руб. от суммы';
        }

        return 'Не определено';
    }

    public static function roundSaleByValue($price, $round)
    {
        if (!$round || $round === self::ROUND_NONE) {
            return $price;
        }
        $value = $price / $round;
        $value = floor($value);
        $value = $round * $value;
        return $value;
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
     * Gets query for [[CarwashSalesItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarwashSalesItems()
    {
        return $this->hasMany(CarwashSalesItem::className(), ['sale_id' => 'id']);
    }
}
