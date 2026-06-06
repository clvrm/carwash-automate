<?php

namespace app\models\ar\personal;

use app\models\ar\carwash\Carwash;
use app\models\ar\chat\ChatMessages;
use app\models\ar\order\Orders;
use app\models\ar\rbac\AuthAssignment;
use app\models\ar\rbac\AuthItem;
use app\models\ar\ticket\TicketMessages;
use app\models\ar\ticket\Tickets;
use app\models\ar\Users;
use Yii;

/**
 * This is the model class for table "personal".
 *
 * @property int $id
 * @property int $user_id
 * @property int $carwash_id
 * @property string|null $firebase_token
 * @property int $is_approved
 * @property int|null $post
 * @property int|null $salary_type
 * @property float|null $salary
 * @property string|null $updated_at
 * @property string|null $created_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property AuthItem[] $itemNames
 * @property Carwash[] $carwashes
 * @property ChatMessages[] $chatMessages
 * @property Orders[] $orders
 * @property Carwash $carwash
 * @property Users $user
 * @property PersonalNotification[] $personalNotifications
 * @property TicketMessages[] $ticketMessages
 * @property Tickets[] $tickets
 */
class Personal extends \yii\db\ActiveRecord
{
    public const POST_OWNER = 10; // Владелец автомойки
    public const POST_MANAGER = 20; // Управляющий
    public const POST_ADMIN = 30; // Администратор
    public const POST_WASHER = 40; // Мойщик

    public const IS_NOT_APPROVED = 0;
    public const IS_APPROVED = 1;

    public const SALARY_TYPE_NONE = 0; // Не учитываем
    public const SALARY_TYPE_PERCENT = 1; // Процент от заказов
    public const SALARY_TYPE_FIXED = 2; // Фиксированная

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'personal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'carwash_id'], 'required'],
            [['user_id', 'carwash_id', 'is_approved', 'post', 'salary_type'], 'integer'],
            [['salary'], 'number'],
            [['firebase_token'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['carwash_id'], 'exist', 'skipOnError' => true, 'targetClass' => Carwash::className(), 'targetAttribute' => ['carwash_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'ID пользователя',
            'carwash_id' => 'ID автомойки',
            'firebase_token' => 'Firebase-токен',
            'is_approved' => 'Подтвержден?',
            'post' => 'Позиция',
            'salary_type' => 'Тип зарплаты',
            'salary' => 'Зарплата',
            'updated_at' => 'Дата обновления',
            'created_at' => 'Дата добавления',
        ];
    }

    /**
     * @param $post
     * @return string
     */
    public static function getPostLabel($post): string
    {
        $posts = [
            self::POST_OWNER => 'Владелец',
            self::POST_MANAGER => 'Управляющий',
            self::POST_ADMIN => 'Администратор',
            self::POST_WASHER => 'Мойщик',
        ];

        return $posts[$post] ?? '';
    }

    /**
     * @return string
     */
    public function getSalaryFullLabel(): string
    {
        if (!$this->salary_type || empty($this->salary)) {
            return 'Нет данных';
        }
        if ($this->salary_type == self::SALARY_TYPE_PERCENT) {
            $salaryType = '%';
        } else {
            $salaryType = '₽/мес';
        }

        return $this->salary . ' ' . $salaryType ?? '';
    }

    /**
     * Короткое имя. Формат: Имя Ф.О.
     * @return string
     */
    public function getShortUsername()
    {
        $firstName = $this->user->firstname ?? '';
        $lastName = mb_substr($this->user->lastname, 0, 1) ?? '';
        $patronymic = mb_substr($this->user->patronymic, 0, 1) ?? '';
        if (empty($firstName)) {
            return 'Имя не задано';
        }
        return "{$firstName} {$lastName}.{$patronymic}.";
    }

    /**
     * @return bool
     */
    public function isOnline(): bool
    {
        $lastLog = PersonalLog::find()->where(['personal_id' => $this->id])->orderBy('created_at DESC')->limit(1)->one();
        if ($lastLog && date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . "-15 mins")) < $lastLog->created_at) {
            return true;
        }
        return false;
    }

    /**
     * @return string
     */
    public function lastOnline()
    {
        $lastLog = PersonalLog::find()->where(['personal_id' => $this->id])->orderBy('created_at DESC')->limit(1)->one();
        if ($lastLog) {
            return $lastLog->logged_at;
        }
        return 'Никогда';
    }

    /**
     * Gets query for [[AuthAssignments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['personal_id' => 'id']);
    }

    /**
     * Gets query for [[ItemNames]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getItemNames()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'item_name'])->viaTable('auth_assignment', ['personal_id' => 'id']);
    }

    /**
     * Gets query for [[Carwashes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarwashes()
    {
        return $this->hasMany(Carwash::className(), ['owner_id' => 'id']);
    }

    /**
     * Gets query for [[ChatMessages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChatMessages()
    {
        return $this->hasMany(ChatMessages::className(), ['personal_id' => 'id']);
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::className(), ['personal_id' => 'id']);
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * Gets query for [[PersonalNotifications]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonalNotifications()
    {
        return $this->hasMany(PersonalNotification::className(), ['personal_id' => 'id']);
    }

    /**
     * Gets query for [[TicketMessages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicketMessages()
    {
        return $this->hasMany(TicketMessages::className(), ['personal_id' => 'id']);
    }

    /**
     * Gets query for [[Tickets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Tickets::className(), ['personal_id' => 'id']);
    }
}
