<?php


namespace app\controllers;


use app\models\ar\documentation\Documentation;
use app\models\ar\documentation\DocumentationCategory;
use yii\filters\AccessControl;
use yii\web\Controller;

class DocumentationController extends Controller
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

    public function actionIndex($category = false)
    {
        $allCategories = DocumentationCategory::find()->where(['parent_id' => null])->orderBy('position ASC')->all();
        if ($category) {
            $subCategories = DocumentationCategory::find()->where(['parent_id' => $category])->orderBy('position ASC')->all();
        }

        return $this->render('index',[
            'currentCategory' => $category,
            'categories' => $allCategories,
            'subCategories' => $subCategories ?? [],
        ]);
    }
}