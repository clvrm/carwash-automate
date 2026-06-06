<?php

namespace app\models\ar\carwash;

use app\models\ar\Clients;
use Yii;

/**
 * This is the model class for table "carwash_blacklist".
 *
 * @property int $id
 * @property int $carwash_id
 * @property int $client_id
 * @property string $car_number
 * @property string|null $car_region
 * @property string|null $created_at
 *
 * @property Carwash $carwash
 * @property Clients $client
 */
class CarwashBlacklist extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carwash_blacklist';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carwash_id', 'client_id', 'car_number'], 'required'],
            [['carwash_id', 'client_id'], 'integer'],
            [['created_at'], 'safe'],
            [['car_number'], 'string', 'max' => 16],
            [['car_region'], 'string', 'max' => 8],
            [['carwash_id'], 'exist', 'skipOnError' => true, 'targetClass' => Carwash::className(), 'targetAttribute' => ['carwash_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::className(), 'targetAttribute' => ['client_id' => 'id']],
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
            'car_number' => 'Номер авто',
            'car_region' => 'Регион авто',
            'created_at' => 'Дата добавления',
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
     * Gets query for [[Client]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Clients::className(), ['id' => 'client_id']);
    }
}
