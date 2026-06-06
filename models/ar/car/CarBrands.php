<?php

namespace app\models\ar\car;

use app\models\ar\carwash\Carwash;
use app\models\ar\order\Orders;
use Yii;

/**
 * This is the model class for table "car_brands".
 *
 * @property int $id
 * @property int|null $carwash_id
 * @property string $title
 * @property string|null $icon
 * @property string|null $synonyms
 * @property string|null $created_at
 *
 * @property Carwash $carwash
 * @property CarModels[] $carModels
 * @property Orders[] $orders
 */
class CarBrands extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'car_brands';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['carwash_id'], 'integer'],
            [['created_at'], 'safe'],
            [['title'], 'string', 'max' => 128],
            [['icon'], 'string', 'max' => 255],
            [['synonyms'], 'string', 'max' => 1024],
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
            'carwash_id' => 'ID связанной автомойки',
            'title' => 'Название',
            'icon' => 'Иконка',
            'synonyms' => 'Синонимы',
            'created_at' => 'Дата создания',
        ];
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
     * Gets query for [[CarModels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarModels()
    {
        return $this->hasMany(CarModels::className(), ['car_brand_id' => 'id']);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['car_brand_id' => 'id']);
    }
}
