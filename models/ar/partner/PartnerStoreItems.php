<?php

namespace app\models\ar\partner;

use Yii;

/**
 * This is the model class for table "partner_store_items".
 *
 * @property int $id
 * @property int $partner_store_id
 * @property string $type Материал / Оборудование
 * @property string $title
 * @property int|null $price
 * @property string|null $link
 * @property int|null $position
 * @property string|null $created_at
 *
 * @property PartnerStore $partnerStore
 */
class PartnerStoreItems extends \yii\db\ActiveRecord
{
    public const TYPE_MATERIALS = 'materials';
    public const TYPE_EQUIPMENT = 'equipment';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'partner_store_items';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['partner_store_id', 'type', 'title'], 'required'],
            [['partner_store_id', 'price', 'position'], 'integer'],
            [['created_at'], 'safe'],
            [['type'], 'string', 'max' => 32],
            [['title', 'link'], 'string', 'max' => 50],
            [['partner_store_id'], 'exist', 'skipOnError' => true, 'targetClass' => PartnerStore::className(), 'targetAttribute' => ['partner_store_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'partner_store_id' => 'ID магазина-партнера',
            'type' => 'Тип',
            'title' => 'Название',
            'price' => 'Цена',
            'link' => 'Ссылка',
            'position' => 'Порядок',
            'created_at' => 'Дата добавления',
        ];
    }

    /**
     * Gets query for [[PartnerStore]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPartnerStore()
    {
        return $this->hasOne(PartnerStore::className(), ['id' => 'partner_store_id']);
    }
}
