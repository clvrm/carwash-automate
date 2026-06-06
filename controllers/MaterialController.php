<?php

namespace app\controllers;


use app\models\ar\Materials;
use yii\db\Exception;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Class MaterialController
 * @package app\controllers
 */
class MaterialController extends Controller
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

        $materials = Materials::find()->where(['is_detailing' => true, 'carwash_id' => $carwashId])->all();

        return $this->render('index', [
            'materials' => $materials
        ]);
    }

    /**
     * @return string
     */
    public function actionWash()
    {
        $carwashId = \Yii::$app->user->identity->getCWid();

        $materials = Materials::find()->where(['is_detailing' => false, 'carwash_id' => $carwashId])->all();

        return $this->render('index', [
            'materials' => $materials
        ]);
    }

    /**
     * @param int $isDetailing
     * @return string|\yii\web\Response
     */
    public function actionCreate($isDetailing = 0)
    {
        $material = new Materials();
        $material->is_detailing = $isDetailing;
        $material->carwash_id = \Yii::$app->user->identity->getCWid();

        if ($material->load(\Yii::$app->request->post()) && $material->validate()) {
            $material->save();

            if ($isDetailing) {
                return $this->redirect('/material/detail');
            } else {
                return $this->redirect('/material/wash');
            }
        }
        return $this->render('create', [
            'model' => $material
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws Exception
     */
    public function actionEdit($id)
    {
        $material = Materials::findOne($id);
        if (empty($material) or $material->carwash_id != \Yii::$app->user->identity->getCWid()) {
            throw new Exception('Не найден данный материал');
        }

        if ($material->load(\Yii::$app->request->post()) && $material->validate()) {
            $material->save();

            if ($material->is_detailing) {
                return $this->redirect('/material/detail');
            } else {
                return $this->redirect('/material/wash');
            }
        }
        return $this->render('edit', [
            'model' => $material
        ]);
    }
}