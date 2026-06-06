<?php

namespace app\models\ar;

use app\models\ar\personal\Personal;
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
            'user_id' => 'User ID',
            'personal_id' => 'Personal ID',
            'event' => 'Event',
            'type' => 'Type',
            'data' => 'Data',
            'created_at' => 'Created At',
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
