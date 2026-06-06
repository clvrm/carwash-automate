<?php

namespace app\models\ar\car;

use app\models\ar\carwash\Carwash;
use app\models\ar\order\Orders;
use Yii;

/**
 * This is the model class for table "car_models".
 *
 * @property int $id
 * @property int $car_brand_id
 * @property int|null $carwash_id
 * @property string $title
 * @property string|null $synonyms
 * @property string|null $created_at
 *
 * @property CarBrands $carBrand
 * @property Carwash $carwash
 * @property Orders[] $orders
 */
class CarModels extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'car_models';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['car_brand_id', 'title'], 'required'],
            [['car_brand_id', 'carwash_id'], 'integer'],
            [['created_at'], 'safe'],
            [['title'], 'string', 'max' => 128],
            [['synonyms'], 'string', 'max' => 1024],
            [['car_brand_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarBrands::className(), 'targetAttribute' => ['car_brand_id' => 'id']],
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
            'car_brand_id' => 'ID бренда',
            'carwash_id' => 'ID связанной автомойки',
            'title' => 'Модель',
            'synonyms' => 'Синоним',
            'created_at' => 'Дата добавления',
        ];
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
     * Gets query for [[Carwash]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarwash()
    {
        return $this->hasOne(Carwash::className(), ['id' => 'carwash_id']);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['car_model_id' => 'id']);
    }
}
