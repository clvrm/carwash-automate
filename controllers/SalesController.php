<?php

namespace app\controllers;

use app\commons\exceptions\CarwashNotAccess;
use app\commons\exceptions\LogicException;
use app\models\ar\carwash\Carwash;
use app\models\ar\carwash\CarwashSales;
use app\models\ar\carwash\CarwashSalesItem;
use app\models\ar\complex\Complexes;
use app\models\ar\service\Services;
use Symfony\Component\OptionsResolver\Exception\AccessException;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Class SalesController
 * @package app\controllers
 */
class SalesController extends Controller
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
                        'roles' => ['perm_create_edit_sales']
                    ],
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ]
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $sales = CarwashSales::find()->where(['carwash_id' => \Yii::$app->user->identity->getCwId()])->andWhere(['>=', 'end_at', date('Y-m-d')])->orderBy('position ASC')->all();
        $saleOld = CarwashSales::find()->where(['carwash_id' => \Yii::$app->user->identity->getCwId()])->andWhere(['<', 'end_at', date('Y-m-d')])->orderBy('end_at ASC')->all();

        return $this->render('index', [
            'sales' => $sales,
            'oldSales' => $saleOld
        ]);
    }

    /**
     * @return string
     */
    public function actionCreate()
    {
        $cwId = \Yii::$app->user->identity->getCWid();

        $complexList = Complexes::find()->where(['carwash_id' => $cwId])->all();
        $serviceList = Services::find()->where(['carwash_id' => $cwId])->all();

        $sale = new CarwashSales();
        $sale->carwash_id = $cwId;
        $sale->sum_up_discount = CarwashSales::NOT_SUM_UP_DISCOUNT;
        $sale->apply_greater = CarwashSales::APPLY_GREATER;

        if ($sale->load(\Yii::$app->request->post()) && $sale->validate()) {
            $errors = false;
            if ($sale->start_at > $sale->end_at) {
                \Yii::$app->session->setFlash('warning', 'Дата начала не может быть позже даты окончания');
                $errors = true;
            }
            if (empty(\Yii::$app->request->post('complexesList')) && empty(\Yii::$app->request->post('servicesList'))) {
                \Yii::$app->session->setFlash('warning', 'Скидка не содержит позиций. Пожалуйста, выберите элементы, на которые распространяется скидка');
                $errors = true;
            }
            if (!$errors && $sale->save()) {
                if ($sale->for_service_type == CarwashSales::SERVICE_TYPE_COMPLEX) {
                    $newComplexList = \Yii::$app->request->post('complexesList');
                    foreach ($newComplexList as $complexId) {
                        $item = new CarwashSalesItem();
                        $item->sale_id = $sale->id;
                        $item->complex_id = $complexId;
                        $item->save();
                    }
                } elseif ($sale->for_service_type == CarwashSales::SERVICE_TYPE_SERVICE) {
                    $newServiceList = \Yii::$app->request->post('servicesList');
                    foreach ($newServiceList as $serviceId) {
                        $item = new CarwashSalesItem();
                        $item->sale_id = $sale->id;
                        $item->service_id = $serviceId;
                        $item->save();
                    }
                }
                \Yii::$app->session->setFlash('success', 'Скидка добавлена');
                return $this->redirect('/sales/');
            }
        }

        return $this->render('create', [
            'sale' => $sale,
            'complexList' => $complexList,
            'serviceList' => $serviceList,
            'selectedServices' => [],
            'selectedComplexes' => [],
        ]);
    }

    public function actionEdit($id)
    {
        $cwId = \Yii::$app->user->identity->getCWid();

        $sale = CarwashSales::findOne($id);
        if (!$sale) {
            throw new LogicException('Не найдена редактируемая скидка');
        }
        if ($sale->carwash_id != $cwId) {
            throw new CarwashNotAccess('Нет доступа к данной странице');
        }

        if ($sale->load(\Yii::$app->request->post()) && $sale->validate()) {
            $errors = false;
            if ($sale->start_at > $sale->end_at) {
                \Yii::$app->session->setFlash('warning', 'Дата начала не может быть позже даты окончания');
                $errors = true;
            }
            if (empty(\Yii::$app->request->post('complexesList')) && empty(\Yii::$app->request->post('servicesList'))) {
                \Yii::$app->session->setFlash('warning', 'Скидка не содержит позиций. Пожалуйста, выберите элементы, на которые распространяется скидка');
                $errors = true;
            }
            if (!$errors && $sale->save()) {
                // Осторожно. Предварительно очищаем уже указанные позиции со скидкой
                CarwashSalesItem::deleteAll(['sale_id' => $sale->id]);

                $newComplexList = \Yii::$app->request->post('complexesList');
                $newServiceList = \Yii::$app->request->post('servicesList');

                if ($sale->for_service_type == CarwashSales::SERVICE_TYPE_COMPLEX && !empty($newComplexList)) {
                    foreach ($newComplexList as $complexId) {
                        $item = new CarwashSalesItem();
                        $item->sale_id = $sale->id;
                        $item->complex_id = $complexId;
                        $item->save();
                    }
                } elseif ($sale->for_service_type == CarwashSales::SERVICE_TYPE_SERVICE && !empty($newServiceList)) {
                    foreach ($newServiceList as $serviceId) {
                        $item = new CarwashSalesItem();
                        $item->sale_id = $sale->id;
                        $item->service_id = $serviceId;
                        $item->save();
                    }
                }
                \Yii::$app->session->setFlash('success', 'Скидка обновлена');
                return $this->redirect('/sales/');
            }
        }


        $complexList = Complexes::find()->where(['carwash_id' => $cwId])->all();
        $serviceList = Services::find()->where(['carwash_id' => $cwId])->all();
        $selectedComplexesList = [];
        $selectedServicesList = [];

        $salesItems = CarwashSalesItem::findAll(['sale_id' => $sale->id]);
        foreach ($salesItems as $salesItem) {
            if (!empty($salesItem->service_id)) {
                $selectedServicesList[] = $salesItem->service_id;
            }
            if (!empty($salesItem->complex_id)) {
                $selectedComplexesList[] = $salesItem->complex_id;
            }
        }

        return $this->render('create', [
            'sale' => $sale,
            'complexList' => $complexList,
            'serviceList' => $serviceList,
            'selectedServices' => $selectedServicesList ?? [],
            'selectedComplexes' => $selectedComplexesList ?? [],
        ]);
    }
}