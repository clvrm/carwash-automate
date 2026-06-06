<?php

namespace app\models\ar\carwash;

use Yii;

/**
 * This is the model class for table "carwash_images".
 *
 * @property int $id
 * @property int $carwash_id
 * @property string $image
 * @property string|null $alt
 * @property int|null $position
 * @property string|null $created_at
 *
 * @property Carwash $carwash
 */
class CarwashImages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carwash_images';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carwash_id', 'image'], 'required'],
            [['carwash_id', 'position'], 'integer'],
            [['created_at'], 'safe'],
            [['image'], 'string', 'max' => 512],
            [['alt'], 'string', 'max' => 255],
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
            'image' => 'Изображение',
            'alt' => 'Подпись',
            'position' => 'Позиция',
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
