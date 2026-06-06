<?php

namespace app\models\ar\chat;

use app\models\ar\carwash\Carwash;
use app\models\ar\Clients;
use app\models\ar\order\Orders;
use Yii;

/**
 * This is the model class for table "chat".
 *
 * @property int $id
 * @property int|null $carwash_id
 * @property int|null $client_id
 * @property int|null $order_id
 * @property string|null $car_number
 * @property string|null $car_region
 * @property string|null $created_at
 *
 * @property Carwash $carwash
 * @property Clients $client
 * @property Orders $order
 * @property ChatMessages[] $chatMessages
 */
class Chat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carwash_id', 'client_id', 'order_id'], 'integer'],
            [['created_at'], 'safe'],
            [['car_number', 'car_region'], 'string', 'max' => 50],
            [['carwash_id'], 'exist', 'skipOnError' => true, 'targetClass' => Carwash::className(), 'targetAttribute' => ['carwash_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::className(), 'targetAttribute' => ['order_id' => 'id']],
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
            'order_id' => 'ID заказа',
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

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Orders::className(), ['id' => 'order_id']);
    }

    /**
     * Gets query for [[ChatMessages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChatMessages()
    {
        return $this->hasMany(ChatMessages::className(), ['chat_id' => 'id']);
    }
}
