<?php

namespace app\models\forms\profile;

use app\commons\exceptions\LogicException;
use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalNotification;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;


/**
 * Class ProfileNotificationsForm
 * @package app\models\forms\profile
 */
class ProfileNotificationsForm extends Model
{
    public $systemEmail;
    public $systemTelegram;
    public $systemWhatsapp;
    public $systemPush;

    public $firebaseUid;

    public $eventNewOrder;
    public $eventNewReview;
    public $eventCancelOrder;
    public $eventNewSalesReport;

    public $eventEditSchedule;
    public $eventEditRecordSettings;
    public $eventEditPriceList;
    public $eventEditPersonalSalary;
    public $eventNewPersonal;
    public $eventChangeOnlineRecordStatus;

    /**
     * @var Personal|null
     */
    private $_personal = null;

    /**
     * ProfileNotificationsForm constructor.
     * @param $personalId
     * @param array $config
     * @throws LogicException
     */
    public function __construct($personalId, $config = [])
    {
        $this->_personal = Personal::findOne($personalId);
        if (!$this->_personal) {
            throw new LogicException('Не найден персонал для создания формы контактов');
        }
        $this->initLoad();
        parent::__construct($config);
    }

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [
                [
                    'systemEmail', 'systemTelegram', 'systemWhatsapp', 'systemPush',
                    'firebaseUid',
                    PersonalNotification::EVENT_NEW_ORDER, PersonalNotification::EVENT_NEW_REVIEW,
                    PersonalNotification::EVENT_CANCEL_ORDER, PersonalNotification::EVENT_NEW_SALES_REPORT,

                    PersonalNotification::EVENT_EDIT_SCHEDULE, PersonalNotification::EVENT_EDIT_RECORD_SETTINGS,
                    PersonalNotification::EVENT_EDIT_PRICE_LIST, PersonalNotification::EVENT_EDIT_PERSONAL_SALARY,
                    PersonalNotification::EVENT_NEW_PERSONAL, PersonalNotification::EVENT_CHANGE_ONLINE_RECORD_STATUS,
                ], 'safe'],
        ];
    }


    public function initLoad()
    {
        $items = PersonalNotification::find()->where(['type' => [PersonalNotification::TYPE_NOTIFY_SYSTEM, PersonalNotification::TYPE_NOTIFY_EVENTS]])
            ->andWhere(['personal_id' => $this->_personal->id])->all();
        $valuesMap = ArrayHelper::map($items, 'key', 'value');
        foreach ($this->attributes as $key => $attribute) {
            $value = $valuesMap[$key] ?? null;
            $this->{$key} = $value;
        }
    }


    public function save()
    {
        foreach ($this->attributes as $key => $value) {
            $type = PersonalNotification::TYPE_NOTIFY_EVENTS;
            if (in_array($key, ['systemEmail', 'systemTelegram', 'systemWhatsapp', 'systemPush'])) {
                $type = PersonalNotification::TYPE_NOTIFY_SYSTEM;
            }

            $model = PersonalNotification::find()->where(['type' => $type])
                ->andWhere(['personal_id' => $this->_personal->id, 'key' => $key])->one();
            if (!$model) {
                $model = new PersonalNotification();
                $model->type = $type;
                $model->personal_id = $this->_personal->id;
                $model->key = $key;
            }
            $model->value = $value;
            $model->save();
        }
    }

}
