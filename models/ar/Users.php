<?php

namespace app\models\ar;

use app\commons\yii\User;
use app\models\ar\personal\Personal;
use Yii;
use yii\base\NotSupportedException;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $guid
 * @property string|null $auth_token
 * @property int $status
 * @property string|null $firstname
 * @property string|null $lastname
 * @property string|null $patronymic
 * @property string|null $avatar
 * @property string|null $phone
 * @property int|null $phone_verified
 * @property string|null $email
 * @property string|null $password_hash
 * @property int|null $lang_id
 * @property string|null $updated_at
 * @property string|null $created_at
 *
 * @property Personal[] $personals
 */
class Users extends \yii\db\ActiveRecord implements IdentityInterface
{
    public const PDATA_COOKIE_PATTERN = 'asV3^alp2!&{cwID}&{pID}';
    public const INVITED_GUID = 'first-invited_'; // с рандомным окончанием после _
    public const INVITED_DEFAULT_PASSWORD = 'lasv23qiaXz';

    public const STATUS_REGISTER = 0; // Пользователь начал процесс регистрации
    public const STATUS_FIRST_INVITED = 1; // Пользователь впервые приглашен в сервис
    public const STATUS_ACTIVE = 2; // Пользователь активен
    public const STATUS_BANNED = 3; // Пользователь заблокирован
    public const STATUS_DELETED = 4; // Пользователь удален


    public const LANG_DEFAULT = 1; // Язык по-умолчанию, русский


    private $personalId = null;
    private $carwashId = null;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['guid'], 'required'],
            [['status', 'lang_id'], 'integer'],
            [['updated_at', 'created_at', 'phone_verified'], 'safe'],
            [['guid', 'email'], 'string', 'max' => 64],
            [['auth_token'], 'string', 'max' => 48],
            [['password_hash'], 'string', 'max' => 255],
            [['firstname', 'lastname', 'patronymic'], 'string', 'max' => 128],
            [['avatar'], 'string', 'max' => 300],
            [['firstname'], 'string', 'min' => 3],
            [['phone'], 'string', 'max' => 32],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'guid' => 'Guid',
            'auth_token' => 'Токен доступа',
            'status' => 'Статус',
            'firstname' => 'Имя',
            'lastname' => 'Фамилия',
            'patronymic' => 'Отчество',
            'avatar' => 'Аватар',
            'phone' => 'Телефон',
            'phone_verified' => 'Подтвержден телефон',
            'email' => 'Email',
            'lang_id' => 'ID выбранного языка',
            'updated_at' => 'Дата обновления',
            'created_at' => 'Дата добавления',
        ];
    }

    /**
     * Gets query for [[Personals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonals()
    {
        return $this->hasMany(Personal::className(), ['user_id' => 'id']);
    }

    public static function setUserLoginCookie($personalId)
    {
        $personal = Personal::findOne($personalId);
        if (!$personal) {
            return false;
        }
        $pdata = strtr(self::PDATA_COOKIE_PATTERN, [
            '{cwID}' => $personal->carwash_id,
            '{pID}' => $personal->id,
        ]);
        setcookie('__pdata', $pdata, time() + (60 * 60 * 24 * 365), '/');
        return true;
    }

    /**
     * @return mixed|string
     */
    public function getPId()
    {
        if (!$this->personalId) {
            $authData = explode('&', $_COOKIE['__pdata']);
            $this->carwashId = $authData[1] ?? null;
            $this->personalId = $authData[2] ?? null;
        }
        return $this->personalId;
    }

    /**
     * @return mixed|string
     */
    public function getCWId()
    {
        if (!$this->carwashId) {
            $authData = explode('&', $_COOKIE['__pdata']);
            $this->carwashId = $authData[1] ?? null;
            $this->personalId = $authData[2] ?? null;
        }
        return $this->carwashId;
    }

    /**
     * @param int|string $id
     * @return Users|IdentityInterface|null
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @param mixed $token
     * @param null $type
     * @return void|IdentityInterface|null
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }
        return static::findOne(['password_reset_token' => $token]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }
        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @return int|mixed|string
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @return mixed|string
     */
    public function getAuthKey()
    {
        return $this->auth_token;
    }

    /**
     * @param string $authKey
     * @return bool
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        if (empty($this->password_hash)) {
            return false;
        }

        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * @param $password
     * @throws \yii\base\Exception
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_token = Yii::$app->security->generateRandomString(48);
    }

    public function generatePasswordResetToken()
    {
        $this->reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->reset_token = null;
    }
}
