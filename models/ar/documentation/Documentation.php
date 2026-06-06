<?php

namespace app\models\ar\documentation;

use Yii;

/**
 * This is the model class for table "documentation".
 *
 * @property int $id
 * @property int $category_id
 * @property string|null $html_id
 * @property string $category_name
 * @property string $title
 * @property string|null $text
 * @property string|null $video
 * @property int|null $position
 * @property string|null $updated_at
 * @property string|null $created_at
 *
 * @property DocumentationCategory $category
 */
class Documentation extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documentation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'category_name', 'title'], 'required'],
            [['category_id', 'position'], 'integer'],
            [['text'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['html_id'], 'string', 'max' => 50],
            [['category_name'], 'string', 'max' => 64],
            [['title'], 'string', 'max' => 128],
            [['video'], 'string', 'max' => 2048],
            [['html_id'], 'unique'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentationCategory::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'ID категории',
            'html_id' => 'HtmlID Название',
            'category_name' => 'Название категории',
            'title' => 'Заголовок',
            'text' => 'Текст',
            'video' => 'Видео',
            'position' => 'Позиция',
            'updated_at' => 'Дата обновления',
            'created_at' => 'Дата добавления',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(DocumentationCategory::className(), ['id' => 'category_id']);
    }
}
