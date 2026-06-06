<?php

namespace app\models\ar\ticket;

use app\models\ar\carwash\Carwash;
use app\models\ar\personal\Personal;
use Yii;

/**
 * This is the model class for table "tickets".
 *
 * @property int $id
 * @property int $carwash_id
 * @property int|null $personal_id
 * @property string $text
 * @property bool $is_closed
 * @property string|null $created_at
 *
 * @property TicketMessages[] $ticketMessages
 * @property Carwash $carwash
 * @property Personal $personal
 */
class Tickets extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tickets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carwash_id', 'text'], 'required'],
            [['carwash_id', 'personal_id'], 'integer'],
            [['is_closed'], 'boolean'],
            [['created_at'], 'safe'],
            [['text'], 'string', 'max' => 8000],
            [['carwash_id'], 'exist', 'skipOnError' => true, 'targetClass' => Carwash::className(), 'targetAttribute' => ['carwash_id' => 'id']],
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
            'carwash_id' => 'ID автомойки',
            'personal_id' => 'ID персонала',
            'text' => 'Текст',
            'is_closed' => 'Закрыт',
            'created_at' => 'Дата добавления',
        ];
    }

    /**
     * Gets query for [[TicketMessages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicketMessages()
    {
        return $this->hasMany(TicketMessages::className(), ['ticket_id' => 'id']);
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
     * Gets query for [[Personal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonal()
    {
        return $this->hasOne(Personal::className(), ['id' => 'personal_id']);
    }
}
