<?php

namespace app\models\ar\documentation;

use Yii;

/**
 * This is the model class for table "documentation_category".
 *
 * @property int $id
 * @property int $parent_id
 * @property string|null $title
 * @property int|null $position
 *
 * @property Documentation[] $documentations
 */
class DocumentationCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documentation_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['position', 'parent_id'], 'integer'],
            [['title'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parent_id' => 'Родительская категория',
            'title' => 'Название',
            'position' => 'Позиция',
        ];
    }

    /**
     * Gets query for [[Documentations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentations()
    {
        return $this->hasMany(Documentation::className(), ['category_id' => 'id']);
    }
}
