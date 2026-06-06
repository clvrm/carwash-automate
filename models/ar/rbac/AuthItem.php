<?php

namespace app\models\ar\rbac;

use Yii;

/**
 * This is the model class for table "auth_item".
 *
 * @property string $name
 * @property int $type
 * @property string|null $description
 * @property string|null $rule_name
 * @property resource|null $data
 * @property int|null $created_at
 * @property int|null $updated_at
 *
 * @property AuthAssignment[] $authAssignments
 * @property Personal[] $personals
 * @property AuthRule $ruleName
 * @property AuthItemChild[] $authItemChildren
 * @property AuthItemChild[] $authItemChildren0
 * @property AuthItem[] $children
 * @property AuthItem[] $parents
 */
class AuthItem extends \yii\db\ActiveRecord
{
    public const ROLE_OWNER = 'owner'; // Самая верхняя роль
    public const ROLE_MANAGER = 'manager'; // Управляющий
    public const ROLE_ADMIN = 'admin'; // Администратор автомойки (ресепшн и т.д.)
    public const ROLE_WASHER = 'washer'; // Мойщик

    public const PERM_RESPOND_REVIEWS = 'perm_respond_reviews'; // Разрешение на ответ к отзывам
    public const PERM_EDIT_PRICELIST = 'perm_edit_pricelist'; // Разрешение на изменение прайс-листов
    public const PERM_CHANGE_RECORD_SETTING = 'perm_change_record_setting'; // Разрешение на изменение настроек записи
    public const PERM_CREATE_MAILING = 'perm_create_mailing'; // Разрешение на создание рассылок
    public const PERM_CREATE_EDIT_SALES = 'perm_create_edit_sales'; // Разрешение на создание / изменение скидок
    public const PERM_CREATE_EDIT_ORDERS = 'perm_create_edit_orders'; // Разрешение на создание / изменение заказов
    public const PERM_CLOSE_ORDERS = 'perm_close_orders'; // Разрешение на закрытие заказов
    public const PERM_EDIT_PERSONAL = 'perm_edit_personal'; // Разрешение на редактирование персонала
    public const PERM_VIEW_ANALYTICS = 'perm_view_analytics'; // Разрешение на просмотр аналитики
    public const PERM_EDIT_CARWASH_INFO = 'perm_edit_carwash_info'; // Разрешение на изменение информации об автомойке

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auth_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'type'], 'required'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64],
            [['name'], 'unique'],
            [['rule_name'], 'exist', 'skipOnError' => true, 'targetClass' => AuthRule::className(), 'targetAttribute' => ['rule_name' => 'name']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'type' => 'Type',
            'description' => 'Description',
            'rule_name' => 'Rule Name',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[AuthAssignments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthAssignments()
    {
        return $this->hasMany(AuthAssignment::className(), ['item_name' => 'name']);
    }

    /**
     * Gets query for [[Personals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonals()
    {
        return $this->hasMany(Personal::className(), ['id' => 'personal_id'])->viaTable('auth_assignment', ['item_name' => 'name']);
    }

    /**
     * Gets query for [[RuleName]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRuleName()
    {
        return $this->hasOne(AuthRule::className(), ['name' => 'rule_name']);
    }

    /**
     * Gets query for [[AuthItemChildren]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren()
    {
        return $this->hasMany(AuthItemChild::className(), ['parent' => 'name']);
    }

    /**
     * Gets query for [[AuthItemChildren0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuthItemChildren0()
    {
        return $this->hasMany(AuthItemChild::className(), ['child' => 'name']);
    }

    /**
     * Gets query for [[Children]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'child'])->viaTable('auth_item_child', ['parent' => 'name']);
    }

    /**
     * Gets query for [[Parents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParents()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'parent'])->viaTable('auth_item_child', ['child' => 'name']);
    }
}
