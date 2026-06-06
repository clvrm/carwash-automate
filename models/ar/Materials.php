<?php

namespace app\models\ar;

use app\models\ar\carwash\Carwash;
use app\models\ar\complex\ComplexMaterials;
use app\models\ar\service\ServiceMaterials;
use Yii;

/**
 * This is the model class for table "materials".
 *
 * @property int $id
 * @property int $carwash_id
 * @property int|null $is_detailing
 * @property string|null $name
 * @property int|null $price
 * @property string|null $created_at
 *
 * @property ComplexMaterials[] $complexMaterials
 * @property Carwash $carwash
 * @property ServiceMaterials[] $serviceMaterials
 */
class Materials extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'materials';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carwash_id'], 'required'],
            [['carwash_id', 'is_detailing', 'price'], 'integer'],
            [['created_at'], 'safe'],
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
            'is_detailing' => 'Дитейлинг?',
            'name' => 'Название',
            'price' => 'Цена',
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
        return $this->hasMany(ComplexMaterials::className(), ['material_id' => 'id']);
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
     * Gets query for [[ServiceMaterials]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServiceMaterials()
    {
        return $this->hasMany(ServiceMaterials::className(), ['material_id' => 'id']);
    }
}
