<?php


namespace app\controllers\ajax;

use app\commons\exceptions\LogicException;
use app\commons\helpers\FileHelper;
use app\models\ar\carwash\Carwash;
use app\models\ar\carwash\CarwashBlacklist;
use app\models\ar\carwash\CarwashImages;
use app\models\ar\carwash\CarwashSales;
use app\models\ar\carwash\CarwashSchedule;
use app\models\ar\complex\Complexes;
use app\models\ar\Materials;
use app\models\ar\service\Services;
use Yii;
use yii\web\Controller;
use yii\web\NotAcceptableHttpException;
use yii\web\UploadedFile;

/**
 * Class CarwashController
 * @package app\controllers\ajax
 */
class CarwashController extends Controller
{
    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    /**
     * @param $carwashId
     * @return bool
     */
    public function actionUploadImages($carwashId)
    {
        $currentImagesCount = CarwashImages::find()->where(['carwash_id' => $carwashId])->count();
        if ($currentImagesCount >= Yii::$app->params['restrictions']['carwash-max-images'])
            return ['error' => 'max-images'];

        $uploadedFile = UploadedFile::getInstanceByName('file');
        if (!$uploadedFile) {
            return false;
        }
        $link = FileHelper::saveUploaded($uploadedFile, Yii::getAlias('@carwash') . $carwashId . '/', true);
        $model = new CarwashImages();
        $model->carwash_id = $carwashId;
        $model->alt = $uploadedFile->getBaseName();
        $model->image = $link;
        $model->save();

        return true;
    }

    /**
     * @param $imageId
     * @param $cwId
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteImage($imageId, $cwId)
    {
        $image = CarwashImages::findOne(['id' => $imageId, 'carwash_id' => $cwId]);
        if ($image)
            $image->delete();

        return true;
    }

    /**
     * @return bool[]|false[]
     * @throws NotAcceptableHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionRemoveBlacklist()
    {
        if (!Yii::$app->request->isPost) {
            throw new NotAcceptableHttpException('Запрещен доступ');
        }
        $id = Yii::$app->request->post('id');
        // Для дополнительной проверки, является ли связанным с данной мойкой
        $clientId = Yii::$app->request->post('clientId');
        $blacklistItem = CarwashBlacklist::findOne(['id' => $id, 'client_id' => $clientId]);
        if ($blacklistItem) {
            $blacklistItem->delete();
            return ['result' => true];
        }
        return ['result' => false];
    }

    /**
     * @return bool[]
     * @throws NotAcceptableHttpException
     */
    public function actionUpdateSalesPosition()
    {
        if (!Yii::$app->request->isPost) {
            throw new NotAcceptableHttpException('Запрещен доступ');
        }
        $saleIds = Yii::$app->request->post('ids');
        $cwId = Yii::$app->request->post('cwId');

        $sales = CarwashSales::find()->where(['carwash_id' => $cwId])->andWhere(['in', 'id', $saleIds])->all();
        foreach ($sales as $sale) {
            $sale->position = array_search($sale->id, $saleIds) ?? 0;
            $sale->save();
        }

        return ['result' => true];
    }

    /**
     * @return bool[]|false[]
     * @throws NotAcceptableHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteSale()
    {
        if (!Yii::$app->request->isPost) {
            throw new NotAcceptableHttpException('Запрещен доступ');
        }
        $id = Yii::$app->request->post('id');
        // Для дополнительной проверки, является ли связанным с данной мойкой
        $cwId = Yii::$app->request->post('cwId');

        $sale = CarwashSales::findOne(['id' => $id, 'carwash_id' => $cwId]);
        if ($sale) {
            $sale->delete();
            return ['result' => true];
        }
        return ['result' => false];
    }

    /**
     * @return bool[]|false[]
     * @throws NotAcceptableHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteMaterial()
    {
        if (!Yii::$app->request->isPost) {
            throw new NotAcceptableHttpException('Запрещен доступ');
        }
        $id = Yii::$app->request->post('id');
        // Для дополнительной проверки, является ли связанным с данной мойкой
        $cwId = Yii::$app->request->post('cwId');

        $material = Materials::findOne(['id' => $id, 'carwash_id' => $cwId]);
        if ($material) {
            $material->delete();
            return ['result' => true];
        }
        return ['result' => false];
    }

    /**
     * @return bool[]
     * @throws NotAcceptableHttpException
     */
    public function actionUpdateServicePosition()
    {
        if (!Yii::$app->request->isPost) {
            throw new NotAcceptableHttpException('Запрещен доступ');
        }
        $serviceIds = Yii::$app->request->post('ids');
        $cwId = Yii::$app->request->post('cwId');

        $services = Services::find()->where(['carwash_id' => $cwId])->andWhere(['in', 'id', $serviceIds])->all();
        foreach ($services as $service) {
            $service->position = array_search($service->id, $serviceIds) ?? 0;
            $service->save();
        }

        return ['result' => true];
    }

    /**
     * @return bool[]|false[]
     * @throws NotAcceptableHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteService()
    {
        if (!Yii::$app->request->isPost) {
            throw new NotAcceptableHttpException('Запрещен доступ');
        }
        $id = Yii::$app->request->post('id');
        // Для дополнительной проверки, является ли связанным с данной мойкой
        $cwId = Yii::$app->request->post('cwId');

        $service = Services::findOne(['id' => $id, 'carwash_id' => $cwId]);
        if ($service) {
            $service->delete();
            return ['result' => true];
        }
        return ['result' => false];
    }


    public function actionUpdateComplexPosition()
    {
        if (!Yii::$app->request->isPost) {
            throw new NotAcceptableHttpException('Запрещен доступ');
        }
        $complexIds = Yii::$app->request->post('ids');
        $cwId = Yii::$app->request->post('cwId');

        $complexes = Complexes::find()->where(['carwash_id' => $cwId])->andWhere(['in', 'id', $complexIds])->all();
        foreach ($complexes as $complex) {
            $complex->position = array_search($complex->id, $complexIds) ?? 0;
            $complex->save();
        }

        return ['result' => true];
    }

    /**
     * @return bool[]|false[]
     * @throws NotAcceptableHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeleteComplex()
    {
        if (!Yii::$app->request->isPost) {
            throw new NotAcceptableHttpException('Запрещен доступ');
        }
        $id = Yii::$app->request->post('id');
        // Для дополнительной проверки, является ли связанным с данной мойкой
        $cwId = Yii::$app->request->post('cwId');

        $complex = Complexes::findOne(['id' => $id, 'carwash_id' => $cwId]);
        if ($complex) {
            $complex->delete();
            return ['result' => true];
        }
        return ['result' => false];
    }

    public function actionIsWorkingDay()
    {
        $cwId = Yii::$app->request->post('cwId');
        $date = Yii::$app->request->post('date');
        $weekDay = date('w', strtotime($date));

        $carwash = Carwash::findOne($cwId);
        if (!$carwash) {
            throw new LogicException('Не найдены базовые модели');
        }
        $carwashSchedule = CarwashSchedule::findOne(['carwash_id' => $carwash->id]);
        if (!$carwashSchedule) {
            throw new LogicException('Не найдена базовая модель расписания');
        }
        if ($carwashSchedule->isWorkDay($weekDay)) {
            return ['result' => true];
        }
        return ['result' => false];
    }
}