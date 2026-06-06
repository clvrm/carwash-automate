<?php

namespace app\models\ar\carwash;

use Yii;

/**
 * This is the model class for table "carwash_contacts".
 *
 * @property int $id
 * @property int $carwash_id
 * @property string|null $phone_1
 * @property string|null $phone_2
 * @property string|null $phone_3
 * @property string|null $site
 * @property string|null $email
 * @property string|null $vk
 * @property string|null $facebook
 * @property string|null $instagram
 * @property string|null $telegram
 * @property string|null $whatsapp
 * @property string|null $viber
 * @property string|null $updated_at
 * @property string|null $created_at
 *
 * @property Carwash $carwash
 */
class CarwashContacts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carwash_contacts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carwash_id'], 'required'],
            [['carwash_id'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['phone_1', 'phone_2', 'phone_3'], 'string', 'max' => 32],
            [['site', 'email'], 'string', 'max' => 128],
            [['vk', 'facebook', 'instagram', 'telegram', 'whatsapp', 'viber'], 'string', 'max' => 64],
            [['carwash_id'], 'exist', 'skipOnError' => true, 'targetClass' => Carwash::className(), 'targetAttribute' => ['carwash_id' => 'id']],
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
            'phone_1' => 'Телефон 1',
            'phone_2' => 'Телефон 2',
            'phone_3' => 'Телефон 3',
            'site' => 'Сайт',
            'email' => 'Email',
            'vk' => 'Vk',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'telegram' => 'Telegram',
            'whatsapp' => 'Whatsapp',
            'viber' => 'Viber',
            'updated_at' => 'Дата обновления',
            'created_at' => 'Дата добавления',
        ];
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
}
