<?php


namespace app\controllers;


use app\commons\exceptions\CarwashNotAccess;
use app\commons\exceptions\LogicException;
use app\commons\helpers\DaDataHelper;
use app\commons\helpers\FileHelper;
use app\commons\notification\NotifyHelper;
use app\models\ar\carwash\Carwash;
use app\models\ar\carwash\CarwashBlacklist;
use app\models\ar\carwash\CarwashComfort;
use app\models\ar\carwash\CarwashContacts;
use app\models\ar\carwash\CarwashImages;
use app\models\ar\carwash\CarwashSchedule;
use app\models\ar\carwash\CarwashSettings;
use app\models\ar\Cities;
use app\models\ar\order\Orders;
use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalLog;
use app\models\ar\personal\PersonalNotification;
use app\models\ar\Users;
use app\models\searchModels\CarwashBlacklistSearch;
use PHPUnit\Exception;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\UploadedFile;
use Yii;

/**
 * Class CarwashController
 * @package app\controllers
 */
class CarwashController extends Controller
{
    public $layout = 'app/main';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['settings'],
                        'roles' => ['perm_change_record_setting']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['client'],
                        'roles' => ['perm_edit_carwash_info']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['base-setting'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionSettings()
    {
        $carwash = Carwash::findOne([\Yii::$app->user->identity->getCwId()]);
        if (!$carwash) {
            throw new LogicException('Не найдена базовая модель автомойки. Свяжитесь с администратором');
        }
        $carwashSettings = CarwashSettings::findOne(['carwash_id' => $carwash->id]);
        if (!$carwashSettings) {
            $carwashSettings = new CarwashSettings();
            $carwashSettings->carwash_id = $carwash->id;
        }
        if (empty($carwashSettings->subscriber_code)) {
            $carwashSettings->generateSubscriberCode();
        }

        // Работа с черным списком
        $searchModel = new CarwashBlacklistSearch();
        $searchModel->carwash_id = $carwash->id;
        $blackListDataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $blackListDataProvider->pagination->pageSize = 10;

        $oldPostCount = $carwashSettings->post_count;
        if (\Yii::$app->request->isPost) {
            $carwashSettings->load(\Yii::$app->request->post());

            // Проверяем, есть ли у мойки заказы на сокращаемых постах с датами впереди текущей
            if ($oldPostCount > 0 && $carwashSettings->post_count < $oldPostCount) {
                $orders = Orders::find()->where(['carwash_id' => $carwash->id])->andWhere(['>=', 'date', date('Y-m-d')])
                    ->andWhere(['in', 'status', [Orders::STATUS_NEW_FROM_CLIENT, Orders::STATUS_NEW_FROM_WASH]])
                    ->andWhere(['>', 'post', $carwashSettings->post_count])->all();
                if (!empty($orders)) {
                    $carwashSettings->post_count = $oldPostCount;
                    Yii::$app->session->setFlash('danger', 'Есть записи на скрываемых постах. Сначала уберите записи со скрываемых постов, и попробуйте снова');
                }
            }
            $carwashSettings->validate();
            if ($carwashSettings->save()) {
                try {
                    // Уведомление об изменении настроек
                    $notify = new NotifyHelper();
                    $notify->trigger(PersonalNotification::EVENT_EDIT_RECORD_SETTINGS, ['initiatorPersonalId' => Yii::$app->user->identity->getPId()]);

                    $log = new PersonalLog();
                    $log->createLog(\Yii::$app->user->identity->getPId() ?? 0, '', PersonalLog::EDIT_SETTINGS);
                } catch (\Exception $exception) {
                }
            }
        }

        return $this->render('settings', [
            'settings' => $carwashSettings,
            'blacklistDataProvider' => $blackListDataProvider
        ]);
    }

    /**
     * @return string
     * @throws LogicException
     */
    public function actionClient()
    {
        $carwash = Carwash::findOne([\Yii::$app->user->identity->getCwId()]);
        if (!$carwash) {
            throw new LogicException('Не найдена автомойка для вашего сеанса');
        }
        $carwashComfort = CarwashComfort::findOne(['carwash_id' => $carwash->id]);
        if (!$carwashComfort) {
            $carwashComfort = new CarwashComfort();
            $carwashComfort->carwash_id = $carwash->id;
        }
        $carwashContacts = CarwashContacts::findOne(['carwash_id' => $carwash->id]);
        if (!$carwashContacts) {
            $carwashContacts = new CarwashContacts();
            $carwashContacts->carwash_id = $carwash->id;
        }
        $carwashSchedule = CarwashSchedule::findOne(['carwash_id' => $carwash->id]);
        if (!$carwashSchedule) {
            $carwashSchedule = new CarwashSchedule();
            $carwashSchedule->carwash_id = $carwash->id;
        }
        $carwashImages = CarwashImages::find()->where(['carwash_id' => $carwash->id])->orderBy('position ASC')->all();
        $cities = Cities::find()->orderBy('name ASC')->all();

        if (\Yii::$app->request->isPost) {
            $currentAvatar = $carwash->avatar;

            $carwash->load(\Yii::$app->request->post());
            $carwash->validate();
            $carwash->save();

            // Уведомление об изменении настроек
            $notify = new NotifyHelper();
            $notify->trigger(PersonalNotification::EVENT_EDIT_SCHEDULE, ['initiatorPersonalId' => Yii::$app->user->identity->getPId()]);

            $uploadedFile = UploadedFile::getInstanceByName('avatar');
            if ($uploadedFile) {
                $avatarLink = FileHelper::saveUploaded($uploadedFile, Yii::getAlias('@carwash') . $carwash->id . '/', true);
                $carwash->avatar = $avatarLink;
            } else {
                $carwash->avatar = $currentAvatar;
            }
            $carwash->save();

            $carwashContacts->load(\Yii::$app->request->post());
            $carwashContacts->validate();
            $carwashContacts->save();

            $carwashComfort->load(\Yii::$app->request->post());
            $carwashComfort->validate();
            $carwashComfort->save();

            $carwashSchedule->load(\Yii::$app->request->post());
            $carwashSchedule->validate();
            $carwashSchedule->save();

            try {
                $log = new PersonalLog();
                $log->createLog(\Yii::$app->user->identity->getPId() ?? 0, '', PersonalLog::EDIT_FOR_CLIENT);
            } catch (\Exception $exception) {
            }
        }


        return $this->render('client', [
            'comfort' => $carwashComfort,
            'contacts' => $carwashContacts,
            'schedule' => $carwashSchedule,
            'carwash' => $carwash,
            'carwashImages' => $carwashImages,
            'cities' => $cities,
        ]);
    }


    /**
     * Базовые настройки пользователя
     */
    public function actionBaseSetting()
    {
        $user = Users::findOne([\Yii::$app->user->getId()]);
        $personal = Personal::findOne([\Yii::$app->user->identity->getPId()]);
        $carwash = Carwash::findOne([\Yii::$app->user->identity->getCwId()]);
        if (!$carwash or !$personal or !$user) {
            throw new CarwashNotAccess('У вас нет доступа к данной странице');
        }

        $currentAvatar = $user->avatar;
        if (\Yii::$app->request->isPost) {
            $post = \Yii::$app->request->post();
            $uploadedFile = UploadedFile::getInstanceByName('avatar');

            if (!empty($uploadedFile)) {
                $randName = md5(rand(1, 999999));
                $uploadedFile->saveAs(\Yii::getAlias('@user-avatars') . $randName . '.' . $uploadedFile->getExtension());
                $user->avatar = '/uploads/user-avatars/' . $randName . '.' . $uploadedFile->getExtension();
            } else {
                $user->avatar = $currentAvatar;
            }

            $user->load($post);
            $user->phone = $post['phone'] ?? null;
            if ($user->validate()) {
                $user->save();
            }
            if ($personal->id === $carwash->owner_id) {
                $carwash->name = $post['carwash_name'] ?? '';
                $carwash->city_id = (int)$post['city_id'] ?? 0;
                $carwash->address = $post['carwash_address'] ?? '';
                if ($carwash->validate() && $carwash->save()) {
                    return $this->redirect('/');
                }
            } else {
                return $this->redirect('/profile/my');
            }
        }

        if ($carwash->owner_id !== $personal->id) {
            $carwash = null;
        }
        // Определяем предварительно город по IP пользователя
        $preDefinedCity = DaDataHelper::getCityByIp();
        $citiesList = Cities::find()->orderBy('name ASC')->all();

        return $this->render('base-setting', [
            'cities' => $citiesList,
            'carwash' => $carwash,
            'user' => $user,
            'preDefinedCity' => $preDefinedCity,
        ]);
    }
}