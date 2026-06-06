<?php


namespace app\controllers\ajax;


use app\models\ar\car\CarModels;
use app\models\ar\personal\Personal;
use app\models\ar\service\Services;
use yii\web\Controller;
use Yii;

/**
 * Class CommonController
 * @package app\controllers\ajax
 */
class CommonController extends Controller
{
    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    /**
     * @param $cwId
     * @param $pId
     * @param string $query
     * @param bool $isDetailing
     * @return array
     */
    public function actionGetServicesList($cwId, $pId, $query = '', bool $isDetailing = false): array
    {
        $personal = Personal::findOne(['id' => $pId, 'carwash_id' => $cwId]);
        if ($personal === null) {
            Yii::error('Попытка получить данные об услугах автомойки, к которой не прикреплен', 'ajax');
            return ['result' => false, 'items' => []];
        }
        if (empty($query)) {
            $services = Services::find()->where(['carwash_id' => $cwId, 'is_detailing' => $isDetailing])
                ->orderBy('name ASC')->all();
        } else {
            $services = Services::find()->where(['like', 'name', $query])
                ->andWhere(['carwash_id' => $cwId, 'is_detailing' => $isDetailing])->orderBy('name ASC')->all();
        }

        $resultArray = [];
        foreach ($services as $service) {
            $resultArray[] = ['id' => $service->id, 'name' => $service->name];
        }
        return ['result' => true, 'items' => $resultArray];
    }

    public function actionGetCarModelsByBrand($brandId, $carwashId = false)
    {
        $query = CarModels::find()->where(['car_brand_id' => $brandId, 'carwash_id' => null]);
        if ($carwashId) {
            $query->orWhere(['car_brand_id' => $brandId, 'carwash_id' => $carwashId]);
        }
        $models = $query->all();

        $resultArray = [];
        foreach ($models as $model) {
            $resultArray[] = ['id' => $model->id, 'text' => $model->title];
        }

        return ['result' => true, 'items' => $resultArray];
    }


}