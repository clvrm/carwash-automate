<?php

namespace app\modules\api\controllers\v1;

use yii\web\Controller;


/**
 * Контроллер для реализации web-view
 */
class ViewsController extends Controller
{
    public $layout = false;
    public function actionPolicy()
    {
        return $this->render('policy');
    }

    public function actionAbout()
    {
        return $this->render('about');
    }
}