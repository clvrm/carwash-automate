<?php

namespace app\modules\api\controllers\v1;

use yii\web\Controller;

/**
 * Default controller for the `api` module
 */
class DefaultController extends Controller
{

    /**
     * @param $token
     * @return false|string
     */
    public function actionIndex($token)
    {
        $this->layout = false;
        if ($token !== 'F593902kcssO') {
            return false;
        }

        $url = '/swagger/specifications/app_v1.json';
        return $this->render('/swagger', [
            'url' => $url
        ]);
    }
}
