<?php

namespace app\models\ar\complex;

use app\models\ar\carwash\Carwash;
use app\models\ar\carwash\CarwashSalesItem;
use Yii;

/**
 * This is the model class for table "complexes".
 *
 * @property int $id
 * @property int $carwash_id
 * @property string $name
 * @property bool $is_detailing
 * @property int|null $type_1_price
 * @property int|null $type_1_time
 * @property int|null $type_2_price
 * @property int|null $type_2_time
 * @property int|null $type_3_price
 * @property int|null $type_3_time
 * @property int|null $type_4_price
 * @property int|null $type_4_time
 * @property int|null $type_5_price
 * @property int|null $type_5_time
 * @property int $position
 * @property string|null $updated_at
 * @property string|null $created_at
 *
 * @property CarwashSalesItem[] $carwashSalesItems
 * @property ComplexMaterials[] $complexMaterials
 * @property ComplexServices[] $complexServices
 * @property Carwash $carwash
 */
class Complexes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'complexes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carwash_id', 'name'], 'required'],
            [['carwash_id', 'type_1_price', 'type_1_time', 'type_2_price', 'type_2_time', 'type_3_price', 'type_3_time', 'type_4_price', 'type_4_time', 'type_5_price', 'type_5_time', 'position'], 'integer'],
            [['is_detailing'], 'boolean'],
            [['updated_at', 'created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
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
            'is_detailing' => 'Дитейлинг?',
            'type_1_price' => 'Легковые стоимость',
            'type_1_time' => 'Легковые длительность',
            'type_2_price' => 'Кроссовер стоимость',
            'type_2_time' => 'Кроссовер длительность',
            'type_3_price' => 'Внедорожник стоимость',
            'type_3_time' => 'Внедорожник длительность',
            'type_4_price' => 'Микроавтобус стоимость',
            'type_4_time' => 'Микроавтобус длительность',
            'type_5_price' => 'Иное стоимость',
            'type_5_time' => 'Иное длительность',
            'position' => 'Позиция',
            'updated_at' => 'Дата обновления',
            'created_at' => 'Дата добавления',
        ];
    }

    /**
     * Gets query for [[ComplexMaterials]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComplexMaterials()
    {
        return $this->hasMany(ComplexMaterials::className(), ['complex_id' => 'id']);
    }

    /**
     * Gets query for [[ComplexServices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComplexServices()
    {
        return $this->hasMany(ComplexServices::className(), ['complex_id' => 'id']);
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
        return $this->hasMany(CarwashSalesItem::className(), ['complex_id' => 'id']);
    }
}
