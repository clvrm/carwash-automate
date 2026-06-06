<?php

namespace app\models\ar\complex;

use app\models\ar\service\Services;
use Yii;

/**
 * This is the model class for table "complex_services".
 *
 * @property int $id
 * @property int|null $complex_id
 * @property int|null $service_id
 * @property int|null $position
 * @property string|null $created_at
 *
 * @property Complexes $complex
 * @property Services $service
 */
class ComplexServices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'complex_services';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['complex_id', 'service_id', 'position'], 'integer'],
            [['created_at'], 'safe'],
            [['complex_id'], 'exist', 'skipOnError' => true, 'targetClass' => Complexes::className(), 'targetAttribute' => ['complex_id' => 'id']],
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
            'complex_id' => 'ID комплекса',
            'service_id' => 'ID услуги',
            'position' => 'Порядок',
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
     * Gets query for [[Service]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Services::className(), ['id' => 'service_id']);
    }
}
