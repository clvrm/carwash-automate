<?php

namespace app\models\ar\carwash;

use Yii;

/**
 * This is the model class for table "carwash_comfort".
 *
 * @property int $id
 * @property int $carwash_id
 * @property bool|null $pay_cash
 * @property bool|null $pay_online
 * @property bool|null $pay_terminal
 * @property bool|null $pay_invoice
 * @property bool|null $cf_ATM
 * @property bool|null $cf_postomat
 * @property bool|null $cf_cafe
 * @property bool|null $cf_toilet
 * @property bool|null $cf_shop
 * @property bool|null $cf_rest_zone
 * @property bool|null $cf_coffee
 * @property bool|null $cf_TV
 * @property bool|null $cf_videocam
 * @property string|null $created_at
 *
 * @property Carwash $carwash
 */
class CarwashComfort extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carwash_comfort';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carwash_id'], 'required'],
            [['carwash_id'], 'integer'],
            [['pay_cash', 'pay_online', 'pay_terminal', 'pay_invoice', 'cf_ATM', 'cf_postomat', 'cf_cafe', 'cf_toilet', 'cf_shop', 'cf_rest_zone', 'cf_coffee', 'cf_TV', 'cf_videocam'], 'boolean'],
            [['created_at'], 'safe'],
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
            'pay_cash' => 'Оплата наличными',
            'pay_online' => 'Оплата онлайн',
            'pay_terminal' => 'Терминал оплаты',
            'pay_invoice' => 'Оплата по счету',
            'cf_ATM' => 'ATM',
            'cf_postomat' => 'Постомат',
            'cf_cafe' => 'Кафе',
            'cf_toilet' => 'Туалет',
            'cf_shop' => 'Магазин',
            'cf_rest_zone' => 'Зона отдыха',
            'cf_coffee' => 'Кофемашина',
            'cf_TV' => 'Телевизор',
            'cf_videocam' => 'Видеонаблюдение',
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
}
