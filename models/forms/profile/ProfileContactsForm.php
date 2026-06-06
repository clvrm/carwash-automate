<?php

namespace app\models\forms\profile;

use app\commons\exceptions\LogicException;
use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalNotification;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;


/**
 * Class ProfileContactsForm
 * @package app\models\forms\profile
 */
class ProfileContactsForm extends Model
{
    public $email;
    public $phone;
    public $telegram;
    public $whatsapp;

    private $_personal = null;

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
            [['email', 'phone', 'telegram', 'whatsapp'], 'safe'],
        ];
    }

    public function initLoad()
    {
        $items = PersonalNotification::findAll(['type' => PersonalNotification::TYPE_CONTACTS, 'personal_id' => $this->_personal->id]);
        $valuesMap = ArrayHelper::map($items, 'key', 'value');
        foreach ($this->attributes as $key => $attribute) {
            $value = $valuesMap[$key] ?? null;
            $this->{$key} = $value;
        }
    }

    public function save()
    {
        foreach ($this->attributes as $key => $value) {
            $model = PersonalNotification::findOne(['type' => PersonalNotification::TYPE_CONTACTS, 'personal_id' => $this->_personal->id, 'key' => $key]);
            if (!$model) {
                $model = new PersonalNotification();
                $model->type = PersonalNotification::TYPE_CONTACTS;
                $model->personal_id = $this->_personal->id;
                $model->key = $key;
            }
            $model->value = $value;
            $model->save();
        }
    }

}
