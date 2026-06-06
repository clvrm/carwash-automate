<?php

namespace app\models\ar\carwash;

use app\models\ar\complex\Complexes;
use app\models\ar\service\Services;
use Yii;

/**
 * This is the model class for table "carwash_sales_item".
 *
 * @property int $id
 * @property int $sale_id
 * @property int|null $complex_id
 * @property int|null $service_id
 * @property string $created_at
 *
 * @property CarwashSales $sale
 * @property Complexes $complex
 * @property Services $service
 */
class CarwashSalesItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carwash_sales_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sale_id'], 'required'],
            [['sale_id', 'complex_id', 'service_id'], 'integer'],
            [['created_at'], 'safe'],
            [['sale_id'], 'exist', 'skipOnError' => true, 'targetClass' => CarwashSales::className(), 'targetAttribute' => ['sale_id' => 'id']],
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
            'sale_id' => 'Sale ID',
            'complex_id' => 'Complex ID',
            'service_id' => 'Service ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Sale]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSale()
    {
        return $this->hasOne(CarwashSales::className(), ['id' => 'sale_id']);
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
