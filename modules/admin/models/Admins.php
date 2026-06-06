<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "admins".
 *
 * @property int $id
 * @property string|null $auth_token
 * @property string $email
 * @property string $password_hash
 * @property int $status
 * @property string|null $name
 * @property string|null $avatar
 * @property string|null $updated_at
 * @property string|null $created_at
 *
 * @property TicketMessages[] $ticketMessages
 */
class Admins extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'admins';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'password_hash'], 'required'],
            [['status'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['auth_token'], 'string', 'max' => 48],
            [['email', 'password_hash'], 'string', 'max' => 64],
            [['name'], 'string', 'max' => 128],
            [['avatar'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'auth_token' => 'Auth Token',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'status' => 'Status',
            'name' => 'Name',
            'avatar' => 'Avatar',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[TicketMessages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicketMessages()
    {
        return $this->hasMany(TicketMessages::className(), ['admin_id' => 'id']);
    }
}
