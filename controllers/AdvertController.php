<?php


namespace app\controllers;


use app\commons\helpers\ClientHelper;
use app\commons\helpers\FileHelper;
use app\models\ar\Advertising;
use app\models\ar\personal\PersonalLog;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\UploadedFile;


/**
 * Class AdvertController
 * @package app\controllers
 */
class AdvertController extends Controller
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
                        'roles' => ['perm_create_mailing'],
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ]
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $cwId = \Yii::$app->user->identity->getCWid() ?? 0;
        $model = Advertising::findOne(['carwash_id' => $cwId]);
        if (!$model) {
            $model = new Advertising();
            $model->carwash_id = $cwId;
            $model->status = Advertising::STATUS_NOT_APPROVED;
            $model->save();
        }

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $model->status = Advertising::STATUS_ON_REVIEW;
            if ($model->save()) {
                try {
                    $log = new PersonalLog();
                    $log->createLog(\Yii::$app->user->identity->getPId() ?? 0, '', PersonalLog::EDIT_ADVERT);
                } catch (\Exception $exception) {
                }
                \Yii::$app->session->setFlash('success', 'Рекламные настройки сохранены');
            } else {
                \Yii::$app->session->setFlash('danger', 'Ошибка при валидации данных рекламной кампании');

            }
        }
        $subscribersCount = ClientHelper::countSubscribers($cwId);

        return $this->render('index', [
            'model' => $model,
            'subscribersCount' => $subscribersCount,
        ]);
    }
}