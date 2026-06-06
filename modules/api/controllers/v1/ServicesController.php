<?php

namespace app\modules\api\controllers\v1;

use app\models\ar\carwash\CarwashSales;
use app\models\ar\carwash\CarwashSalesItem;
use app\models\ar\complex\Complexes;
use app\models\ar\complex\ComplexServices;
use app\models\ar\order\OrderService;
use app\models\ar\service\Services;
use app\modules\api\commons\v1\ReturnCodes;
use yii\web\Controller;
use Yii;

class ServicesController extends Controller
{

    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }


    public function actionList()
    {
        $cwId = Yii::$app->request->post('cwId');
        $pId = Yii::$app->request->post('pId');
        $carType = Yii::$app->request->post('carType');
        $serviceType = Yii::$app->request->post('serviceType');
        $isDetailing = filter_var(Yii::$app->request->post('isDetailing'), FILTER_VALIDATE_BOOLEAN);
        $searchQuery = Yii::$app->request->post('query');

        if (!$carType || !$cwId || !$pId) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ATTRIBUTES_ERROR);
            return [
                'items' => [],
                'message' => 'Не все обязательные параметры переданы'
            ];
        }

        $resultArray = [];

        $services = Services::find()->where(['carwash_id' => $cwId]);
        $complexes = Complexes::find()->where(['carwash_id' => $cwId]);

        $services->andWhere(['is_detailing' => $isDetailing]);
        $complexes->andWhere(['is_detailing' => $isDetailing]);

        if (isset($searchQuery)) {
            $services->andWhere(['like', 'name', $searchQuery]);
            $complexes->andWhere(['like', 'name', $searchQuery]);
        }

        if ($serviceType) {
            if ($serviceType == OrderService::TYPE_SERVICE) {
                $services = $services->all();
                $complexes = [];
            }
            if ($serviceType == OrderService::TYPE_COMPLEX) {
                $services = [];
                $complexes = $complexes->all();
            }
        } else {
            $services = $services->all() ?? [];
            $complexes = $complexes->all() ?? [];
        }

        foreach ($services as $service) {
            $price = $service->{"type_{$carType}_price"} ?? 0;
            $time = $service->{"type_{$carType}_time"} ?? 0;
            $resultArray[] = [
                'id' => $service->id,
                'type' => 'service',
                'name' => $service->name ?? 'не указано',
                'isDetailing' => $service->is_detailing,
                'price' => $price,
                'time' => $time,
            ];
        }

        foreach ($complexes as $complex) {
            $price = $complex->{"type_{$carType}_price"} ?? 0;
            $time = $complex->{"type_{$carType}_time"} ?? 0;
            $includedServices = ComplexServices::findAll(['complex_id' => $complex->id]);
            $servicesText = '';
            $servicesArray = [];
            foreach ($includedServices as $includedService) {
                $service = $includedService->getService()->one();
                $servicesArray[] = ['id' => $service->id, 'name' => $service->name];
            }
            $resultArray[] = [
                'id' => $complex->id,
                'type' => 'complex',
                'name' => $complex->name ?? 'не указано',
                'isDetailing' => $complex->is_detailing,
                'price' => $price,
                'time' => $time,
                'services' => $servicesArray,
            ];
        }

        return ['items' => $resultArray];
    }

    public function actionInfoServices()
    {
        $cwId = Yii::$app->request->post('cwId');
        $pId = Yii::$app->request->post('pId');
        $carType = Yii::$app->request->post('carType');
        $serviceType = Yii::$app->request->post('serviceType');
        $isDetailing = filter_var(Yii::$app->request->post('isDetailing'), FILTER_VALIDATE_BOOLEAN);
        $searchQuery = Yii::$app->request->post('query');

        if (!$carType || !$cwId || !$pId) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ATTRIBUTES_ERROR);
            return [
                'items' => [],
                'message' => 'Не все обязательные параметры переданы'
            ];
        }

        $resultArray = [];

        $services = Services::find()->where(['carwash_id' => $cwId]);
        $complexes = Complexes::find()->where(['carwash_id' => $cwId]);

        if (isset($isDetailing)) {
            $services->andWhere(['is_detailing' => $isDetailing]);
            $complexes->andWhere(['is_detailing' => $isDetailing]);
        }
        if (isset($searchQuery)) {
            $services->andWhere(['like', 'name', $searchQuery]);
            $complexes->andWhere(['like', 'name', $searchQuery]);
        }

        if ($serviceType) {
            if ($serviceType == OrderService::TYPE_SERVICE) {
                $services = $services->all();
                $complexes = [];
            }
            if ($serviceType == OrderService::TYPE_COMPLEX) {
                $services = [];
                $complexes = $complexes->all();
            }
        } else {
            $services = $services->all() ?? [];
            $complexes = $complexes->all() ?? [];
        }

        foreach ($services as $service) {
            $price = $service->{"type_{$carType}_price"} ?? 0;
            $time = $service->{"type_{$carType}_time"} ?? 0;
            $resultArray[] = [
                'id' => $service->id,
                'type' => 'service',
                'name' => $service->name ?? 'не указано',
                'isDetailing' => $service->is_detailing,
                'price' => $price,
                'time' => $time,
            ];
        }

        foreach ($complexes as $complex) {
            $price = $complex->{"type_{$carType}_price"} ?? 0;
            $time = $complex->{"type_{$carType}_time"} ?? 0;
            $includedServices = ComplexServices::findAll(['complex_id' => $complex->id]);
            $servicesText = '';
            $servicesArray = [];
            foreach ($includedServices as $includedService) {
                $service = $includedService->getService()->one();
                $servicesArray[] = ['id' => $service->id, 'name' => $service->name];
                $servicesText .= $service->name . ';';
            }

            $resultArray[] = [
                'id' => $complex->id,
                'type' => 'complex',
                'name' => $complex->name ?? 'не указано',
                'isDetailing' => $complex->is_detailing,
                'price' => $price,
                'time' => $time,
                'services' => $servicesArray
            ];
        }

        return ['items' => $resultArray];
    }

    public function actionInfoSales()
    {
        $cwId = Yii::$app->request->post('cwId');
        $pId = Yii::$app->request->post('pId');
        $carType = Yii::$app->request->post('carType');

        if (!$carType || !$cwId || !$pId) {
            \Yii::$app->response->setStatusCode(ReturnCodes::ATTRIBUTES_ERROR);
            return [
                'items' => [],
                'message' => 'Не все обязательные параметры переданы'
            ];
        }

        $sales = CarwashSales::find()->where(['carwash_id' => $cwId])
            ->andWhere(['>=', 'end_at', date('Y-m-d', strtotime(date('Y-m-d') . '-1 day"'))])
            ->orderBy('position ASC')->all();
        $salesArray = [];
        foreach ($sales as $sale) {
            $saleText = '';
            if ($sale->sale_type == CarwashSales::SALE_TYPE_PERCENT) {
                $saleText .= $sale->sale . '%';
            } elseif ($sale->sale_type == CarwashSales::SALE_TYPE_PRICE) {
                $saleText .= $sale->sale . ' руб.';
            } else {
                $saleText .= $sale->sale;
            }
            $forServiceTypeText = 'услуги';
            if ($sale->for_service_type == CarwashSales::SERVICE_TYPE_COMPLEX) {
                $forServiceTypeText = 'комплексы';
            }
            $saleItemsText = $sale->saleItemsList();

            $salesArray[] = [
                'id' => $sale->id,
                'name' => $sale->name,
                'description' => $sale->description,
                'onlySubscribers' => (bool)$sale->only_subscribers,
                'forServiceTypeText' => $forServiceTypeText,
                'saleText' => $saleText,
                'startAt' => $sale->start_at,
                'endAt' => $sale->end_at,
                'itemsText' => $saleItemsText,
            ];
        }

        return ['sales' => $salesArray];
    }

}
