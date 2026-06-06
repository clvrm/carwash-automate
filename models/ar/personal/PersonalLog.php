<?php

namespace app\models\ar\personal;

use app\commons\exceptions\LogicException;
use app\commons\helpers\TimeHelper;
use app\models\ar\carwash\Carwash;
use Yii;

/**
 * This is the model class for table "personal_log".
 *
 * @property int $id
 * @property int $personal_id
 * @property int|null $type 1 - общее, 2 - личное, 3 - информационное
 * @property string $event
 * @property string|null $text
 * @property string|null $logged_at
 * @property string|null $created_at
 */
class PersonalLog extends \yii\db\ActiveRecord
{
    public const AUTH_LOGIN = 'auth_login';
    public const AUTH_LOGOUT = 'auth_logout';

    public const VIEW_JOURNAL = 'view_journal';
    public const VIEW_INFO = 'view_info';

    public const VIEW_FOR_CLIENT = 'view_for_client';
    public const EDIT_FOR_CLIENT = 'edit_for_client';

    public const VIEW_SETTINGS = 'view_settings';
    public const EDIT_SETTINGS = 'edit_settings';

    public const VIEW_PRICELIST = 'view_pricelist';
    public const CREATE_PRICELIST = 'create_pricelist';
    public const EDIT_PRICELIST = 'edit_pricelist';
    public const DELETE_PRICELIST = 'delete_pricelist';

    public const VIEW_SALES_ALL = 'view_sales_all';
    public const CREATE_SALES = 'view_sale';
    public const EDIT_SALES = 'edit_sale';
    public const DELETE_SALES = 'delete_sale';

    public const VIEW_PERSONAL_ALL = 'view_personal_all';
    public const EDIT_PERSONAL = 'edit_personal';
    public const DELETE_PERSONAL = 'delete_personal';
    public const CREATE_PERSONAL = 'create_personal';

    public const VIEW_ADVERT = 'view_advert';
    public const EDIT_ADVERT = 'edit_advert';

    public const VIEW_ANAL_ORDERS = 'view_anal_orders';
    public const VIEW_ANAL_CLIENTS = 'view_anal_clients';
    public const VIEW_ANAL_FINANCE = 'view_anal_finance';

    public const VIEW_PROFILE = 'view_profile';
    public const EDIT_PROFILE = 'edit_profile';

    public const VIEW_CHAT = 'view_chat';
    public const SEND_MESSAGE_CHAT = 'send_message_chat';
    public const VIEW_CHAT_SUPPORT = 'view_chat_support';
    public const SEND_MESSAGE_CHAT_SUPPORT = 'send_message_chat_support';

    public const VIEW_PARTNER_SHOPS = 'view_partner_shops';

    public const VIEW_DOCS = 'view_docs';

    public const VIEW_ORDER = 'view_order';
    public const CREATE_ORDER = 'create_order';
    public const AUTO_CREATE_ORDER = 'auto_create_order';
    public const EDIT_ORDER = 'edit_order';
    public const DELETE_ORDER = 'delete_order';

    public const UNKNOWN_EVENT = 'UNKNOWN_event'; // Используется, когда неизвестно переданное событие, но попытка была
    public const ERROR_PAGE = 'error_page';


    public const TYPE_GLOBAL = 1; // Видим для всех
    public const TYPE_PRIVATE = 2; // Видим только для пользователя
    public const TYPE_SYSTEM_INFO = 3; // Невидим для всех

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'personal_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['personal_id', 'event'], 'required'],
            [['personal_id', 'type'], 'integer'],
            [['text'], 'string'],
            [['created_at', 'logged_at'], 'safe'],
            [['event'], 'string', 'max' => 32],
            [['personal_id'], 'exist', 'skipOnError' => true, 'targetClass' => Personal::className(), 'targetAttribute' => ['personal_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'personal_id' => 'Персонал',
            'type' => 'Тип',
            'event' => 'Событие',
            'text' => 'Дата',
            'logged_at' => 'Дата логирования (с учетом временной зоны)',
            'created_at' => 'Дата создания',
        ];
    }

    /**
     * @param $event
     * @return string
     */
    public function eventLabels($event): string
    {
        $labels = [
            self::AUTH_LOGIN => 'Сотрудник авторизовался в системе',
            self::AUTH_LOGOUT => 'Сотрудник вышел из системы',
            self::VIEW_JOURNAL => 'Просмотр журнала записи',
            self::VIEW_INFO => 'Просмотр сводной информации',
            self::VIEW_FOR_CLIENT => 'Просмотр страницы настроек для клиентов',
            self::EDIT_FOR_CLIENT => 'Редактирование настроек для клиентов',
            self::VIEW_SETTINGS => 'Просмотр страницы настроек автомойки',
            self::EDIT_SETTINGS => 'Редактирование страницы настроек автомойки',
            self::VIEW_PRICELIST => 'Просмотр прайс-листа',
            self::CREATE_PRICELIST => 'Создание новой позиции в прайс-листе',
            self::EDIT_PRICELIST => 'Редактирование прайс-листа',
            self::DELETE_PRICELIST => 'Удаление позиции из прайс-листа',
            self::VIEW_SALES_ALL => 'Просмотр всех скидок',
            self::CREATE_SALES => 'Создание скидки',
            self::EDIT_SALES => 'Редактирование скидки',
            self::DELETE_SALES => 'Удаление скидки',
            self::VIEW_PERSONAL_ALL => 'Просмотр всех сотрудников',
            self::EDIT_PERSONAL => 'Редактирование сотрудника',
            self::DELETE_PERSONAL => 'Удаление сотрудника',
            self::CREATE_PERSONAL => 'Создание сотрудника',
            self::VIEW_ADVERT => 'Просмотр страницы рекламы',
            self::EDIT_ADVERT => 'Редактирование рекламы',
            self::VIEW_ANAL_ORDERS => 'Просмотр аналитики по заказам',
            self::VIEW_ANAL_CLIENTS => 'Просмотр аналитики по клиентам',
            self::VIEW_ANAL_FINANCE => 'Просмотр аналитики по финансам',
            self::VIEW_PROFILE => 'Просмотр своего профиля',
            self::EDIT_PROFILE => 'Редактирование своего профиля',
            self::VIEW_CHAT => 'Просмотр чата с клиентами',
            self::SEND_MESSAGE_CHAT => 'Отправка сообщения в чат с клиентом',
            self::VIEW_CHAT_SUPPORT => 'Просмотр чата поддержки',
            self::SEND_MESSAGE_CHAT_SUPPORT => 'Отправка сообщения в чат с поддержкой',
            self::VIEW_PARTNER_SHOPS => 'Просмотр партнерских магазинов (склад)',
            self::VIEW_DOCS => 'Просмотр документации',
            self::VIEW_ORDER => 'Просмотр заказа',
            self::CREATE_ORDER => 'Создание нового заказа',
            self::AUTO_CREATE_ORDER => 'Автоматическое создание заказа',
            self::EDIT_ORDER => 'Редактирование заказа',
            self::DELETE_ORDER => 'Удаление заказа',
            self::UNKNOWN_EVENT => 'Неизвестное событие',
            self::ERROR_PAGE => 'Страница ошибки',
        ];

        return $labels[$event] ?? '';
    }

    /**
     * @param $personalId
     * @param string $url
     * @param string $event
     * @param string $text
     * @return bool
     */
    public function createLog($personalId, string $url = '', string $event = '', string $text = ''): bool
    {
        if (!$this->validatePersonal($personalId)) {
            Yii::error('Не удалось провалидировать переданного пользователя, для сохранения лога');
            return false;
        }
        $this->personal_id = $personalId;

        if (!empty($url) && empty($event)) {
            $this->event = $this->getEventNameByUrl($url);
        }
        if ($event) {
            if ($this->eventLabels($event)) {
                $this->event = $event;
            }
        }

        $this->text = '';
        if (!$this->event) {
            $this->event = self::UNKNOWN_EVENT;
            $this->text .= '{unknown_event_name: ' . ($event ?? 'not-set') . '; event_url: ' . ($url ?? 'not-set') . '} ---';
        }

        if (!empty($text)) {
            $this->text .= $text;
        }
        $this->type = $this->getTypeByEvent($this->event);

        $this->logged_at = $this->getLoggedTimeWithTimezone($personalId);
        $this->save();

        return true;
    }

    /**
     * @param $personalId
     * @return string|null
     */
    private function getLoggedTimeWithTimezone($personalId): ?string
    {
        $carwashTimezone = Carwash::find()->select('timezone')->rightJoin('personal', 'personal.carwash_id = carwash.id')
            ->where(['personal.id' => $personalId])->one();
        if (!$carwashTimezone) {
            $timezone = 0;
        } else {
            $timezone = $carwashTimezone->timezone ?? 0;
        }
        $currentTimeWithTimezone = TimeHelper::getCurrentDateBasedOnTimezone('Y-m-d H:i:s', $timezone);

        return $currentTimeWithTimezone ?? null;
    }

    private function validatePersonal($personalId)
    {
        $personal = Personal::findOne($personalId);
        if ($personal) {
            return true;
        }
        return false;
    }

    private function getEventNameByUrl($url)
    {
        $urlEvents = [
            '/' => self::VIEW_JOURNAL,
            '/site/index' => self::VIEW_JOURNAL,
            '/orders/info' => self::VIEW_INFO,
            '/orders/create' => self::CREATE_ORDER,
            '/orders/edit' => self::EDIT_ORDER,
            '/orders/auto-create' => self::AUTO_CREATE_ORDER,
            '/orders/show' => self::VIEW_ORDER,
            '/carwash/client' => self::VIEW_FOR_CLIENT,
            '/carwash/settings' => self::VIEW_SETTINGS,
            '/service/create' => self::CREATE_PRICELIST,
            '/service/wash' => self::VIEW_PRICELIST,
            '/service/detail' => self::VIEW_PRICELIST,
            '/service/edit' => self::EDIT_PRICELIST,
            '/material/detail' => self::VIEW_PRICELIST,
            '/material/wash' => self::VIEW_PRICELIST,
            '/material/create' => self::CREATE_PRICELIST,
            '/material/edit' => self::EDIT_PRICELIST,
            '/complex/wash' => self::VIEW_PRICELIST,
            '/complex/create-wash' => self::CREATE_PRICELIST,
            '/complex/detail' => self::VIEW_PRICELIST,
            '/complex/create-detail' => self::CREATE_PRICELIST,
            '/complex/edit-detail' => self::EDIT_PRICELIST,
            '/complex/edit-wash' => self::EDIT_PRICELIST,
            '/sales/' => self::VIEW_SALES_ALL,
            '/sales/edit' => self::EDIT_SALES,
            '/sales/create' => self::CREATE_SALES,
            '/personal/index' => self::VIEW_PERSONAL_ALL,
            '/personal/edit' => self::EDIT_PERSONAL,
            '/personal/create' => self::CREATE_PERSONAL,
            '/advert/index' => self::VIEW_ADVERT,
            '/analytics/orders' => self::VIEW_ANAL_ORDERS,
            '/analytics/clients' => self::VIEW_ANAL_CLIENTS,
            '/analytics/finance' => self::VIEW_ANAL_FINANCE,
            '/profile/my' => self::VIEW_PROFILE,
            '/chat/index' => self::VIEW_CHAT,
            '/chat/support' => self::VIEW_CHAT_SUPPORT,
            '/partner-shops/equipment' => self::VIEW_PARTNER_SHOPS,
            '/partner-shops/materials' => self::VIEW_PARTNER_SHOPS,
            '/documentation' => self::VIEW_DOCS,
        ];

        if (isset($urlEvents[$url])) {
            return $urlEvents[$url];
        }
        return false;
    }

    private function getTypeByEvent($event)
    {
        $typeEvents = [
            self::TYPE_GLOBAL => [
                self::VIEW_JOURNAL, self::VIEW_FOR_CLIENT, self::EDIT_FOR_CLIENT, self::VIEW_SETTINGS,
                self::EDIT_SETTINGS, self::VIEW_PRICELIST, self::CREATE_PRICELIST,
                self::EDIT_PRICELIST, self::DELETE_PRICELIST, self::VIEW_SALES_ALL, self::CREATE_SALES,
                self::EDIT_SALES, self::DELETE_SALES, self::VIEW_PERSONAL_ALL, self::EDIT_PERSONAL,
                self::DELETE_PERSONAL, self::CREATE_PERSONAL, self::VIEW_ADVERT, self::EDIT_ADVERT,
                self::VIEW_ANAL_ORDERS, self::VIEW_ANAL_CLIENTS, self::VIEW_ANAL_FINANCE, self::VIEW_PROFILE,
                self::EDIT_PROFILE, self::VIEW_CHAT, self::SEND_MESSAGE_CHAT, self::VIEW_CHAT_SUPPORT,
                self::SEND_MESSAGE_CHAT_SUPPORT, self::VIEW_PARTNER_SHOPS, self::VIEW_DOCS, self::VIEW_ORDER,
                self::CREATE_ORDER, self::AUTO_CREATE_ORDER, self::EDIT_ORDER, self::DELETE_ORDER, self::VIEW_INFO
            ],
            self::TYPE_PRIVATE => [

            ],
            self::TYPE_SYSTEM_INFO => [
                self::AUTH_LOGIN,
                self::AUTH_LOGOUT,
                self::UNKNOWN_EVENT,
            ],
        ];

        if (in_array($event, $typeEvents[self::TYPE_GLOBAL], false)) {
            return self::TYPE_GLOBAL;
        }
        if (in_array($event, $typeEvents[self::TYPE_PRIVATE], false)) {
            return self::TYPE_PRIVATE;
        }
        if (in_array($event, $typeEvents[self::TYPE_SYSTEM_INFO], false)) {
            return self::TYPE_SYSTEM_INFO;
        }
        return self::TYPE_SYSTEM_INFO; // По-умолчанию
    }

    /**
     * Gets query for [[Personal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonal()
    {
        return $this->hasOne(Personal::className(), ['id' => 'personal_id']);
    }
}
