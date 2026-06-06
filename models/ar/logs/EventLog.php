<?php

namespace app\models\ar\logs;

use app\models\ar\personal\Personal;
use app\models\ar\Users;
use Yii;

/**
 * This is the model class for table "event_log".
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $personal_id
 * @property string|null $event
 * @property string|null $type error / info / debug / system
 * @property string|null $data
 * @property string|null $created_at
 *
 * @property Personal $personal
 * @property Users $user
 */
class EventLog extends \yii\db\ActiveRecord
{
    public const TYPE_ERROR = 'error';
    public const TYPE_INFO = 'info';
    public const TYPE_DEBUG = 'debug';
    public const TYPE_SYSTEM = 'system';

    public const SYS_USER_REGISTER_EMAIL = 'user_first_register';
    public const SYS_USER_RESET_EMAIL = 'user_reset_password';
    public const SYS_USER_INVITE_EMAIL = 'carwash_invite_user';


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'personal_id'], 'integer'],
            [['data'], 'string'],
            [['created_at'], 'safe'],
            [['event'], 'string', 'max' => 255],
            [['type'], 'string', 'max' => 12],
            [['personal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Personal::className(), 'targetAttribute' => ['personal_id' => 'id']],
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
            'personal_id' => 'ID персонала',
            'event' => 'Событие',
            'type' => 'Тип',
            'data' => 'Данные',
            'created_at' => 'Дата создания',
        ];
    }

    /**
     * Gets query for [[Personal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonal()
    {
        return $this->hasOne(Personal::className(), ['id' => 'personal_id']);
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
}
