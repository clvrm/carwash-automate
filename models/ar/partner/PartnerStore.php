<?php

namespace app\models\ar\partner;

use Yii;

/**
 * This is the model class for table "partner_store".
 *
 * @property int $id
 * @property string $title
 * @property string|null $short_text
 * @property string $type Оборудование / Материалы / Всё
 * @property string $link
 * @property string|null $logo
 * @property int|null $position
 * @property string $created_at
 *
 * @property PartnerStoreItems[] $partnerStoreItems
 */
class PartnerStore extends \yii\db\ActiveRecord
{
    public const TYPE_MATERIALS = 'materials';
    public const TYPE_EQUIPMENT = 'equipment';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner_store';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'type', 'link'], 'required'],
            [['position'], 'integer'],
            [['created_at'], 'safe'],
            [['title'], 'string', 'max' => 128],
            [['short_text'], 'string', 'max' => 512],
            [['type'], 'string', 'max' => 32],
            [['link', 'logo'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Название',
            'short_text' => 'Короткое описание',
            'type' => 'Тип',
            'link' => 'Ссылка',
            'logo' => 'Логотип',
            'position' => 'Позиция',
            'created_at' => 'Дата добавления',
        ];
    }

    /**
     * Gets query for [[PartnerStoreItems]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerStoreItems()
    {
        return $this->hasMany(PartnerStoreItems::className(), ['partner_store_id' => 'id']);
    }
}
