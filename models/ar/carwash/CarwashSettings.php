<?php

namespace app\models\ar\carwash;

use Yii;

/**
 * This is the model class for table "carwash_settings".
 *
 * @property int $id
 * @property int $carwash_id
 * @property int|null $post_count Количество постов
 * @property int|null $online_record Онлайн запись
 * @property int|null $only_subscribers Только для подписчиков
 * @property string|null $subscriber_code Код для подписчиков
 * @property int|null $can_record_blacklist Запись клиентов из черного списка
 * @property int|null $checkout_time Время въезда-выезда
 * @property int|null $dense_record Плотная запись
 * @property int|null $max_recording_range Максимальное количество дней для записи наперед
 * @property int|null $average_duration Средняя длительность
 * @property int|null $until_last_client До последнего клиента
 * @property int|null $staff_delay_time Время задержки персонала
 * @property int|null $service_time_multiplier Множитель времени услуг
 * @property string|null $updated_at
 * @property string|null $created_at
 *
 * @property Carwash $carwash
 */
class CarwashSettings extends \yii\db\ActiveRecord
{
    public const MAX_POST_COUNT = 20;
    public const SUBSCRIBER_CODE_PREFIX = 'AV';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carwash_settings';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carwash_id'], 'required'],
            [['carwash_id', 'max_recording_range', 'post_count', 'online_record', 'only_subscribers', 'can_record_blacklist', 'checkout_time', 'dense_record', 'average_duration', 'until_last_client', 'staff_delay_time', 'service_time_multiplier'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['subscriber_code'], 'string', 'max' => 32],
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
            'post_count' => 'Количество постов',
            'online_record' => 'Онлайн запись',
            'only_subscribers' => 'Только для подписчиков',
            'subscriber_code' => 'Код автомойки',
            'can_record_blacklist' => 'Запись из черного списка?',
            'checkout_time' => 'Время заезда',
            'max_recording_range' => 'Максимальное количество дней для записи',
            'dense_record' => 'Плотная запись',
            'average_duration' => 'Средняя длительность',
            'until_last_client' => 'До последнего клиента',
            'staff_delay_time' => 'Время задержки персонала',
            'service_time_multiplier' => 'Множитель времени',
            'updated_at' => 'Дата обновления',
            'created_at' => 'Дата добавления',
        ];
    }


    public function generateSubscriberCode() : void
    {
        $maxLengthCode = 7;
        $length = strlen($this->id);
        $this->subscriber_code = self::SUBSCRIBER_CODE_PREFIX . str_repeat('0', ($maxLengthCode - $length)) . $this->id;
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
