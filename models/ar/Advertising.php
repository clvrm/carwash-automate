<?php

namespace app\models\ar;

use app\models\ar\carwash\Carwash;
use Yii;

/**
 * This is the model class for table "advertising".
 *
 * @property int $id
 * @property int $carwash_id
 * @property string $type
 * @property string|null $site
 * @property string|null $phone
 * @property string|null $title
 * @property string|null $text
 * @property string|null $banner
 * @property int $status
 * @property int $views
 * @property string|null $updated_at
 * @property string|null $created_at
 *
 * @property Carwash $carwash
 */
class Advertising extends \yii\db\ActiveRecord
{
    public const STATUS_APPROVED = 1;
    public const STATUS_ON_REVIEW = 3;
    public const STATUS_NOT_APPROVED = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'advertising';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carwash_id'], 'required'],
            [['carwash_id', 'status', 'views'], 'integer'],
            [['text'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['type'], 'string', 'max' => 16],
            [['site'], 'string', 'max' => 128],
            [['phone'], 'string', 'max' => 32],
            [['title', 'banner'], 'string', 'max' => 255],
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
            'type' => 'Тип',
            'site' => 'Сайт',
            'phone' => 'Телефон',
            'title' => 'Заголовок',
            'text' => 'Текст',
            'banner' => 'Баннер',
            'status' => 'Статус',
            'views' => 'Просмотры',
            'updated_at' => 'Дата обновления',
            'created_at' => 'Дата создания',
        ];
    }

    /**
     * @param int $statusId
     * @return string|string[]
     */
    public static function labelStatus(int $statusId = 0)
    {
        $labels = [
            self::STATUS_NOT_APPROVED => 'Не подтверждено',
            self::STATUS_APPROVED => 'Подтверждено',
            self::STATUS_ON_REVIEW => 'На модерации',
        ];
        if ($statusId) {
            return $labels[$statusId] ?? '';
        }
        return $labels;
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
