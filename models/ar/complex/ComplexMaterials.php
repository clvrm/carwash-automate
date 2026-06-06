<?php

namespace app\models\ar\complex;

use app\models\ar\Materials;
use Yii;

/**
 * This is the model class for table "complex_materials".
 *
 * @property int $id
 * @property int $complex_id
 * @property int $material_id
 * @property int $price
 * @property string|null $created_at
 *
 * @property Complexes $complex
 * @property Materials $material
 */
class ComplexMaterials extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'complex_materials';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['complex_id', 'material_id', 'price'], 'required'],
            [['complex_id', 'material_id', 'price'], 'integer'],
            [['created_at'], 'safe'],
            [['complex_id'], 'exist', 'skipOnError' => true, 'targetClass' => Complexes::className(), 'targetAttribute' => ['complex_id' => 'id']],
            [['material_id'], 'exist', 'skipOnError' => true, 'targetClass' => Materials::className(), 'targetAttribute' => ['material_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'complex_id' => 'ID комплекса',
            'material_id' => 'ID материала',
            'price' => 'Цена',
            'created_at' => 'Дата добавления',
        ];
    }

    /**
     * Gets query for [[Complex]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComplex()
    {
        return $this->hasOne(Complexes::className(), ['id' => 'complex_id']);
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
}
