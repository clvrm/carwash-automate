<?php

namespace app\models\ar\chat;

use app\models\ar\Clients;
use app\models\ar\personal\Personal;
use Yii;

/**
 * This is the model class for table "chat_messages".
 *
 * @property int $id
 * @property int $chat_id
 * @property int|null $client_id
 * @property int|null $personal_id
 * @property bool $is_viewed
 * @property string $text
 * @property int|null $created_at
 *
 * @property Chat $chat
 * @property Clients $client
 * @property Personal $personal
 */
class ChatMessages extends \yii\db\ActiveRecord
{
    public const IS_VIEWED = 1;
    public const NOT_VIEWED = 0;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chat_messages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['chat_id', 'text'], 'required'],
            [['chat_id', 'client_id', 'personal_id'], 'integer'],
            [['is_viewed'], 'boolean'],
            [['text'], 'string'],
            [['created_at'], 'safe'],
            [['chat_id'], 'exist', 'skipOnError' => true, 'targetClass' => Chat::className(), 'targetAttribute' => ['chat_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clients::className(), 'targetAttribute' => ['client_id' => 'id']],
            [['personal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Personal::className(), 'targetAttribute' => ['personal_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_id' => 'ID чата',
            'client_id' => 'ID клиента',
            'personal_id' => 'ID персонала',
            'is_viewed' => 'Просмотрено?',
            'text' => 'Сообщение',
            'created_at' => 'Дата добавления',
        ];
    }

    /**
     * Gets query for [[Chat]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChat()
    {
        return $this->hasOne(Chat::className(), ['id' => 'chat_id']);
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
     * Gets query for [[Personal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonal()
    {
        return $this->hasOne(Personal::className(), ['id' => 'personal_id']);
    }
}
