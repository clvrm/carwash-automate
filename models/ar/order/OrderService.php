<?php

namespace app\models\ar\order;

use Yii;

/**
 * This is the model class for table "order_service".
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $entity_id id услуги / комплекса / материала исходя из типа
 * @property int|null $type Услуга / комплекс / материалы
 * @property bool|null $is_detailing
 * @property string $name
 * @property int|null $price
 * @property string|null $created_at
 *
 * @property Orders $order
 */
class OrderService extends \yii\db\ActiveRecord
{
    public const TYPE_SERVICE = 1;
    public const TYPE_COMPLEX = 2;
    public const TYPE_MATERIAL = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_service';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'name'], 'required'],
            [['order_id', 'entity_id', 'type', 'price'], 'integer'],
            [['created_at', 'is_detailing'], 'safe'],
            [['name'], 'string', 'max' => 255],
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
            'order_id' => 'ID заказа',
            'entity_id' => 'Товар исходя из типа',
            'type' => 'Тип товара',
            'is_detailing' => 'Детейлинг?',
            'name' => 'Название',
            'price' => 'Цена',
            'created_at' => 'Дата добавления',
        ];
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
}
