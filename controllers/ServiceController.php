<?php


namespace app\controllers;


use app\commons\exceptions\CarwashNotAccess;
use app\commons\notification\NotifyHelper;
use app\models\ar\Materials;
use app\models\ar\personal\PersonalNotification;
use app\models\ar\service\ServiceMaterials;
use app\models\ar\service\Services;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Class ServiceController
 * @package app\controllers
 */
class ServiceController extends Controller
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

    /**
     * @return string
     */
    public function actionDetail()
    {
        $carwashId = \Yii::$app->user->identity->getCWid();

        $services = Services::find()->where(['carwash_id' => $carwashId, 'is_detailing' => true])->
        orderBy('position ASC')->all();

        return $this->render('index', [
            'services' => $services
        ]);
    }

    /**
     * @return string
     */
    public function actionWash()
    {
        $carwashId = \Yii::$app->user->identity->getCWid();

        $services = Services::find()->where(['carwash_id' => $carwashId, 'is_detailing' => false])->
        orderBy('position ASC')->all();

        return $this->render('index', [
            'services' => $services
        ]);
    }

    /**
     * @param $isDetailing
     * @return string|\yii\web\Response
     */
    public function actionCreate($isDetailing)
    {
        $materialsList = Materials::findAll(['carwash_id' => \Yii::$app->user->identity->getCWid(),
            'is_detailing' => $isDetailing]);
        $selectedMaterials = [];
        $model = new Services();
        $model->is_detailing = $isDetailing;
        $model->carwash_id = \Yii::$app->user->identity->getCWid();

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            $materials = \Yii::$app->request->post('material') ?? [];
            foreach ($materials as $materialId => $materialItem) {
                $materialModel = new ServiceMaterials();
                $materialModel->material_id = $materialId;
                $materialModel->service_id = $model->id;
                $materialModel->price = (int)$materialItem['price'] ?? 0;
                $materialModel->save();
            }

            if ($isDetailing) {
                return $this->redirect('/service/detail');
            } else {
                return $this->redirect('/service/wash');
            }
        }

        return $this->render('create', [
            'model' => $model,
            'isDetailing' => $isDetailing,
            'materials' => $materialsList,
            'selectedMaterials' => $selectedMaterials,
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws CarwashNotAccess
     */
    public function actionEdit($id)
    {
        $model = Services::findOne(['carwash_id' => \Yii::$app->user->identity->getCWid(), 'id' => $id]);
        if (!$model) {
            throw new CarwashNotAccess('Нет доступа к данной странице');
        }

        $isDetailing = $model->is_detailing;
        $materialsList = Materials::findAll(['carwash_id' => \Yii::$app->user->identity->getCWid(),
            'is_detailing' => $isDetailing]);
        $selectedMaterials = ServiceMaterials::findAll(['service_id' => $id]);

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $model->save();

            ServiceMaterials::deleteAll(['service_id' => $id]);

            $materials = \Yii::$app->request->post('material') ?? [];
            foreach ($materials as $materialId => $materialItem) {
                $materialModel = new ServiceMaterials();
                $materialModel->material_id = $materialId;
                $materialModel->service_id = $model->id;
                if (!empty($materialItem['price'])) {
                    $price = (int)$materialItem['price'];
                } else {
                    $price = 0;
                }
                $materialModel->price = $price ?? 0;
                $materialModel->save();
            }
            // Уведомление об изменении прайслиста
            $notify = new NotifyHelper();
            $notify->trigger(PersonalNotification::EVENT_EDIT_PRICE_LIST, ['initiatorPersonalId' => \Yii::$app->user->identity->getPId()]);

            if ($isDetailing) {
                return $this->redirect('/service/detail');
            } else {
                return $this->redirect('/service/wash');
            }
        }

        return $this->render('edit', [
            'model' => $model,
            'isDetailing' => $isDetailing,
            'materials' => $materialsList,
            'selectedMaterials' => $selectedMaterials,
        ]);
    }
}