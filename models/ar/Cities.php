<?php

namespace app\models\ar;

use app\models\ar\carwash\Carwash;
use Yii;

/**
 * This is the model class for table "cities".
 *
 * @property int $id
 * @property string $name
 * @property string|null $created_at
 *
 * @property Carwash[] $carwashes
 */
class Cities extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cities';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Город',
            'created_at' => 'Дата добавления',
        ];
    }

    /**
     * Gets query for [[Carwashes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarwashes()
    {
        return $this->hasMany(Carwash::className(), ['city_id' => 'id']);
    }
}
