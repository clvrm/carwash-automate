<?php

namespace app\models\ar\personal;

use Yii;

/**
 * This is the model class for table "personal_notification".
 *
 * @property int $id
 * @property int $personal_id
 * @property string $type
 * @property string $key
 * @property string|null $value
 * @property string|null $created_at
 *
 * @property Personal $personal
 */
class PersonalNotification extends \yii\db\ActiveRecord
{
    public const TYPE_CONTACTS = 'contacts';
    public const TYPE_NOTIFY_EVENTS = 'notify_event';
    public const TYPE_NOTIFY_SYSTEM = 'notify_system';

    public const SYSTEM_PUSH = 'systemPush';
    public const SYSTEM_EMAIL = 'systemEmail';
    public const SYSTEM_TELEGRAM = 'systemTelegram';
    public const SYSTEM_WHATSAPP = 'systemWhatsapp';

    public const EVENT_NEW_ORDER = 'eventNewOrder';
    public const EVENT_NEW_REVIEW = 'eventNewReview';
    public const EVENT_CANCEL_ORDER = 'eventCancelOrder';
    public const EVENT_NEW_SALES_REPORT = 'eventNewSalesReport';

    public const EVENT_EDIT_SCHEDULE = 'eventEditSchedule';
    public const EVENT_EDIT_RECORD_SETTINGS = 'eventEditRecordSettings';
    public const EVENT_EDIT_PRICE_LIST = 'eventEditPriceList';
    public const EVENT_EDIT_PERSONAL_SALARY = 'eventEditPersonalSalary';
    public const EVENT_NEW_PERSONAL = 'eventNewPersonal';
    public const EVENT_CHANGE_ONLINE_RECORD_STATUS = 'eventChangeOnlineRecordStatus';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'personal_notification';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['personal_id', 'type', 'key'], 'required'],
            [['personal_id'], 'integer'],
            [['created_at'], 'safe'],
            [['type'], 'string', 'max' => 32],
            [['key'], 'string', 'max' => 128],
            [['value'], 'string', 'max' => 4096],
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
            'personal_id' => 'ID персонала',
            'type' => 'Тип',
            'key' => 'Ключ',
            'value' => 'Значение',
            'created_at' => 'Дата добавления',
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
}
