<?php


namespace app\controllers;


use app\models\ar\chat\Chat;
use app\models\ar\ticket\Tickets;
use yii\filters\AccessControl;
use yii\web\Controller;

class ChatController extends Controller
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
                        'roles' => ['@'],
                    ]
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $cwId = \Yii::$app->user->identity->getCWid();

        $chats = Chat::find()->where(['carwash_id' => $cwId])->orderBy('created_at DESC')->all();

        return $this->render('index', [
            'cwId' => $cwId,
            'chats' => $chats,
        ]);
    }

    public function actionSupport()
    {
        $cwId = \Yii::$app->user->identity->getCWid();

        $tickets = Tickets::find()->where(['carwash_id' => $cwId])->orderBy('created_at DESC')->all();

        return $this->render('support', [
            'cwId' => $cwId,
            'tickets' => $tickets,
        ]);
    }
}