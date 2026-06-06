<?php


namespace app\controllers;


use app\models\ar\partner\PartnerStore;
use yii\filters\AccessControl;
use yii\web\Controller;

class PartnerShopsController extends Controller
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

    public function actionMaterials()
    {
        $stores = PartnerStore::find()->where(['type' => PartnerStore::TYPE_MATERIALS])->orderBy('position ASC')->all();

        return $this->render('index', [
            'type' => 'materials',
            'stores' => $stores,
        ]);
    }

    public function actionEquipment()
    {
        $stores = PartnerStore::find()->where(['type' => PartnerStore::TYPE_EQUIPMENT])->orderBy('position ASC')->all();

        return $this->render('index', [
            'type' => 'equipment',
            'stores' => $stores,
        ]);
    }
}