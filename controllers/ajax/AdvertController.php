<?php

namespace app\controllers\ajax;

use app\commons\helpers\FileHelper;
use app\models\ar\Advertising;
use yii\web\Controller;
use Yii;
use yii\web\UploadedFile;

/**
 * Class AdvertController
 * @package app\controllers\ajax
 */
class AdvertController extends Controller
{
    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    public function actionUploadBanner($carwashId)
    {
        $advert = Advertising::findOne(['carwash_id' => $carwashId]);

        $uploadedFile = UploadedFile::getInstanceByName('file');
        if (!$advert && !$uploadedFile) {
            return false;
        }

        $link = FileHelper::saveUploaded($uploadedFile, Yii::getAlias('@advert') . $carwashId . '/', true);
        $advert->banner = $link;
        $advert->save();

        return true;
    }

    /**
     * @param $carwashId
     * @return array
     */
    public function actionRemoveBanner($carwashId)
    {
        $advert = Advertising::findOne(['carwash_id' => $carwashId]);
        if (!$advert) {
            return ['result' => false];
        }
        $advert->banner = null;

        return ['result' => $advert->save()];
    }

}