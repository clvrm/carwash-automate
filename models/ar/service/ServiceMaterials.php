<?php

namespace app\models\ar\service;

use app\models\ar\Materials;
use Yii;

/**
 * This is the model class for table "service_materials".
 *
 * @property int $id
 * @property int $service_id
 * @property int $material_id
 * @property int $price
 * @property string|null $created_at
 *
 * @property Materials $material
 * @property Services $service
 */
class ServiceMaterials extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'service_materials';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['service_id', 'material_id', 'price'], 'required'],
            [['service_id', 'material_id', 'price'], 'integer'],
            [['created_at'], 'safe'],
            [['material_id'], 'exist', 'skipOnError' => true, 'targetClass' => Materials::className(), 'targetAttribute' => ['material_id' => 'id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Services::className(), 'targetAttribute' => ['service_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_id' => 'ID услуги',
            'material_id' => 'ID материала',
            'price' => 'Цена',
            'created_at' => 'Дата добавления',
        ];
    }

    /**
     * Gets query for [[Material]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterial()
    {
        return $this->hasOne(Materials::className(), ['id' => 'material_id']);
    }

    /**
     * Gets query for [[Service]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Services::className(), ['id' => 'service_id']);
    }
}
