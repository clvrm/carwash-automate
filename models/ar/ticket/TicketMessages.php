<?php

namespace app\models\ar\ticket;

use app\models\ar\personal\Personal;
use app\modules\admin\models\Admins;
use Yii;

/**
 * This is the model class for table "ticket_messages".
 *
 * @property int $id
 * @property int $ticket_id
 * @property int|null $personal_id
 * @property int|null $admin_id
 * @property string $text
 * @property string|null $created_at
 *
 * @property Admins $admin
 * @property Personal $personal
 * @property Tickets $ticket
 */
class TicketMessages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ticket_messages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_id'], 'required'],
            [['ticket_id', 'personal_id', 'admin_id'], 'integer'],
            [['text'], 'string'],
            [['created_at'], 'safe'],
            [['admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => Admins::className(), 'targetAttribute' => ['admin_id' => 'id']],
            [['personal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Personal::className(), 'targetAttribute' => ['personal_id' => 'id']],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tickets::className(), 'targetAttribute' => ['ticket_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ticket_id' => 'ID тикета',
            'personal_id' => 'ID персонала',
            'admin_id' => 'ID администратора',
            'text' => 'Сообщение',
            'created_at' => 'Дата добавления',
        ];
    }

    /**
     * Gets query for [[Admin]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdmin()
    {
        return $this->hasOne(Admins::className(), ['id' => 'admin_id']);
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
     * Gets query for [[Ticket]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Tickets::className(), ['id' => 'ticket_id']);
    }
}
