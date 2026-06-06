<?php

namespace app\models\ar;

use app\models\ar\carwash\Carwash;
use app\models\ar\carwash\CarwashBlacklist;
use app\models\ar\chat\Chat;
use app\models\ar\chat\ChatMessages;
use app\models\ar\order\Orders;
use Yii;

/**
 * This is the model class for table "clients".
 *
 * @property int $id
 * @property string $guid
 * @property int $carwash_id
 * @property bool $is_subscribed
 * @property string|null $full_name
 * @property string|null $phone
 * @property string|null $email
 * @property int $reputation
 * @property string|null $updated_at
 * @property string|null $created_at
 *
 * @property CarwashBlacklist[] $carwashBlacklists
 * @property Chat[] $chats
 * @property ChatMessages[] $chatMessages
 * @property Carwash $carwash
 * @property Orders[] $orders
 */
class Clients extends \yii\db\ActiveRecord
{
    const REPUTATION_GOOD = 'good';
    const REPUTATION_BAD = 'bad';
    const REPUTATION_UNKNOWN = 'unknown';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clients';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['guid', 'carwash_id'], 'required'],
            [['carwash_id', 'reputation'], 'integer'],
            [['is_subscribed'], 'boolean'],
            [['updated_at', 'created_at'], 'safe'],
            [['guid'], 'string', 'max' => 64],
            [['full_name', 'email'], 'string', 'max' => 128],
            [['phone'], 'string', 'max' => 32],
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
            'guid' => 'Guid',
            'carwash_id' => 'ID автомойки',
            'is_subscribed' => 'Подписчик?',
            'full_name' => 'Полное имя',
            'phone' => 'Телефон',
            'email' => 'Email',
            'reputation' => 'Репутация',
            'updated_at' => 'Дата обновления',
            'created_at' => 'Дата добавления',
        ];
    }

    public function checkInBlacklist($carwashId)
    {
        $inBlockList = false;

        $blockLists = $this->getCarwashBlacklists()->where(['carwash_id' => $carwashId])->all();
        if ($blockLists) {
            $inBlockList = true;
        }

        return $inBlockList;
    }

    /**
     * Текущий статус для репутации пользователя
     * @return string
     */
    public function getReputationStatus(): string
    {
        if ($this->reputation < 3 && $this->reputation > -3) {
            return self::REPUTATION_UNKNOWN;
        } elseif ($this->reputation >= 3) {
            return self::REPUTATION_GOOD;
        }
        return self::REPUTATION_BAD;
    }

    public function getReputationText()
    {
        $reputationText = [
            self::REPUTATION_GOOD => 'У пользователя хорошая репутация',
            self::REPUTATION_UNKNOWN => 'Репутация пользователя неизвестна',
            self::REPUTATION_BAD => 'У пользователя плохая репутация',
        ];

        return $reputationText[$this->getReputationStatus()];
    }

    public function getReputationStyleIcon()
    {
        $reputationStyles = [
            self::REPUTATION_GOOD => '<i class="client-reputation-icon far fa-smile-beam" style="color:#4BB462"></i>',
            self::REPUTATION_UNKNOWN => '<i class="client-reputation-icon far fa-meh" style="color:#ffa800"></i>',
            self::REPUTATION_BAD => '<i class="client-reputation-icon far fa-surprise" style="color:#f64e60"></i>',
        ];

        return $reputationStyles[$this->getReputationStatus()];
    }

    /**
     * Gets query for [[CarwashBlacklists]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarwashBlacklists()
    {
        return $this->hasMany(CarwashBlacklist::className(), ['client_id' => 'id']);
    }

    /**
     * Gets query for [[Chats]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChats()
    {
        return $this->hasMany(Chat::className(), ['client_id' => 'id']);
    }

    /**
     * Gets query for [[ChatMessages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChatMessages()
    {
        return $this->hasMany(ChatMessages::className(), ['client_id' => 'id']);
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
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['client_id' => 'id']);
    }
}
