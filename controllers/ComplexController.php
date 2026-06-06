<?php


namespace app\controllers;


use app\commons\exceptions\CarwashNotAccess;
use app\commons\notification\NotifyHelper;
use app\models\ar\complex\Complexes;
use app\models\ar\complex\ComplexMaterials;
use app\models\ar\complex\ComplexServices;
use app\models\ar\Materials;
use app\models\ar\personal\PersonalNotification;
use app\models\ar\service\ServiceMaterials;
use app\models\ar\service\Services;
use yii\base\BaseObject;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * Class ComplexController
 * @package app\controllers
 */
class ComplexController extends Controller
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

        $complexes = Complexes::find()->where(['carwash_id' => $carwashId, 'is_detailing' => true])->
        orderBy('position ASC')->all();

        return $this->render('index', [
            'complexes' => $complexes
        ]);
    }

    /**
     * @return string
     */
    public function actionWash()
    {
        $carwashId = \Yii::$app->user->identity->getCWid();

        $complexes = Complexes::find()->where(['carwash_id' => $carwashId, 'is_detailing' => false])->
        orderBy('position ASC')->all();

        return $this->render('index', [
            'complexes' => $complexes
        ]);
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionCreateWash()
    {
        $materialsList = Materials::findAll(['carwash_id' => \Yii::$app->user->identity->getCWid(),
            'is_detailing' => false]);
        $selectedMaterials = [];

        $services = Services::find()->where(['carwash_id' => \Yii::$app->user->identity->getCWid(), 'is_detailing' => false])
            ->orderBy('name ASC')->all();
        $model = new Complexes();
        $model->is_detailing = false;
        $model->carwash_id = \Yii::$app->user->identity->getCWid();
        $model->position = 100;

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $model->save();

            $newSelectedMaterials = \Yii::$app->request->post('material') ?? [];
            foreach ($newSelectedMaterials as $materialId => $materialItem) {
                $materialModel = new ComplexMaterials();
                $materialModel->material_id = $materialId;
                $materialModel->complex_id = $model->id;
                $materialModel->price = (int)$materialItem['price'] ?? 0;
                $materialModel->save();
            }
            $newSelectedServices = \Yii::$app->request->post('service') ?? [];
            foreach ($newSelectedServices as $serviceId => $serviceItem) {
                $complexServiceModel = new ComplexServices();
                $complexServiceModel->complex_id = $model->id;
                $complexServiceModel->service_id = $serviceId;
                $complexServiceModel->save();
            }

            return $this->redirect('/complex/wash');
        }

        return $this->render('create-wash', [
            'model' => $model,
            'services' => $services,
            'selectedServices' => [],
            'selectedMaterials' => $selectedMaterials,
            'materials' => $materialsList,
        ]);
    }

    /**
     * @param $id
     * @return string|\yii\web\Response
     * @throws CarwashNotAccess
     */
    public function actionEditWash($id)
    {
        $model = Complexes::findOne(['id' => $id, 'carwash_id' => \Yii::$app->user->identity->getCWid()]);
        if (empty($model)) {
            throw new CarwashNotAccess('Нет доступа к данной странице');
        }
        $materialsList = Materials::findAll(['carwash_id' => \Yii::$app->user->identity->getCWid(),
            'is_detailing' => false]);
        $selectedMaterials = ComplexMaterials::findAll(['complex_id' => $id]);
        $selectedServices = ComplexServices::findAll(['complex_id' => $id]);

        $services = Services::find()->where(['carwash_id' => \Yii::$app->user->identity->getCWid(), 'is_detailing' => false])
            ->orderBy('name ASC')->all();

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            // Предварительно очищаем все привязанные материалы
            ComplexMaterials::deleteAll(['complex_id' => $id]);
            $newSelectedMaterials = \Yii::$app->request->post('material') ?? [];
            foreach ($newSelectedMaterials as $materialId => $materialItem) {
                $materialModel = new ComplexMaterials();
                $materialModel->material_id = $materialId;
                $materialModel->complex_id = $model->id;
                $materialModel->price = (int)$materialItem['price'] ?? 0;
                $materialModel->save();
            }
            // Предварительно очищаем все привязанные услуги
            ComplexServices::deleteAll(['complex_id' => $id]);
            $newSelectedService = \Yii::$app->request->post('service') ?? [];
            foreach ($newSelectedService as $serviceId => $serviceItem) {
                $materialModel = new ComplexServices();
                $materialModel->complex_id = $model->id;
                $materialModel->service_id = $serviceId;
                $materialModel->save();
            }
            // Уведомление об изменении прайслиста
            $notify = new NotifyHelper();
            $notify->trigger(PersonalNotification::EVENT_EDIT_PRICE_LIST, ['initiatorPersonalId' => \Yii::$app->user->identity->getPId()]);

            return $this->redirect('/complex/wash');
        }

        return $this->render('edit-wash', [
            'model' => $model,
            'services' => $services,
            'selectedServices' => $selectedServices,
            'selectedMaterials' => $selectedMaterials,
            'materials' => $materialsList,
        ]);
    }


    /**
     * Создание комплекса дитейлинга
     * @return string|\yii\web\Response
     */
    public function actionCreateDetail()
    {
        $services = Services::find()->where(['carwash_id' => \Yii::$app->user->identity->getCWid(), 'is_detailing' => true])
            ->orderBy('name ASC')->all();

        $materialsList = Materials::findAll(['carwash_id' => \Yii::$app->user->identity->getCWid(),
            'is_detailing' => true]);
        $selectedMaterials = [];

        $model = new Complexes();
        $model->is_detailing = true;
        $model->carwash_id = \Yii::$app->user->identity->getCWid();
        $model->position = 100;

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $model->type_1_time = 0;
            $model->type_2_time = 0;
            $model->type_3_time = 0;
            $model->type_4_time = 0;
            $priceForAll = \Yii::$app->request->post('price_for_all');
            if (isset($priceForAll) and !empty($priceForAll)) {
                $model->type_1_price = $priceForAll;
                $model->type_2_price = $priceForAll;
                $model->type_3_price = $priceForAll;
                $model->type_4_price = $priceForAll;
            }
            $model->save();

            $newSelectedMaterials = \Yii::$app->request->post('material') ?? [];
            foreach ($newSelectedMaterials as $materialId => $materialItem) {
                $materialModel = new ComplexMaterials();
                $materialModel->material_id = $materialId;
                $materialModel->complex_id = $model->id;
                $materialModel->price = (int)$materialItem['price'] ?? 0;
                $materialModel->save();
            }

            $newSelectedServices = \Yii::$app->request->post('service') ?? [];
            foreach ($newSelectedServices as $serviceId => $serviceItem) {
                $materialModel = new ComplexServices();
                $materialModel->complex_id = $model->id;
                $materialModel->service_id = $serviceId;
                $materialModel->save();
            }

            return $this->redirect('/complex/detail');
        }

        return $this->render('create-detail', [
            'model' => $model,
            'services' => $services,
            'selectedServices' => [],
            'selectedMaterials' => $selectedMaterials,
            'materials' => $materialsList,
        ]);
    }

    public function actionEditDetail($id)
    {
        $model = Complexes::findOne(['id' => $id, 'carwash_id' => \Yii::$app->user->identity->getCWid()]);
        if (empty($model)) {
            throw new CarwashNotAccess('Нет доступа к данной странице');
        }
        $services = Services::find()->where(['carwash_id' => \Yii::$app->user->identity->getCWid(), 'is_detailing' => true])
            ->orderBy('name ASC')->all();
        $selectedServices = ComplexServices::findAll(['complex_id' => $id]);
        $materialsList = Materials::findAll(['carwash_id' => \Yii::$app->user->identity->getCWid(),
            'is_detailing' => false]);
        $selectedMaterials = ComplexMaterials::findAll(['complex_id' => $id]);

        if ($model->load(\Yii::$app->request->post()) && $model->validate()) {
            $model->type_1_time = 0;
            $model->type_2_time = 0;
            $model->type_3_time = 0;
            $model->type_4_time = 0;
            $priceForAll = \Yii::$app->request->post('price_for_all');
            if (isset($priceForAll) and !empty($priceForAll)) {
                $model->type_1_price = $priceForAll;
                $model->type_2_price = $priceForAll;
                $model->type_3_price = $priceForAll;
                $model->type_4_price = $priceForAll;
            }
            $model->save();

            // Предварительно очищаем все привязанные материалы
            ComplexMaterials::deleteAll(['complex_id' => $id]);
            $newSelectedMaterials = \Yii::$app->request->post('material') ?? [];
            foreach ($newSelectedMaterials as $materialId => $materialItem) {
                $materialModel = new ComplexMaterials();
                $materialModel->material_id = $materialId;
                $materialModel->complex_id = $model->id;
                $materialModel->price = (int)$materialItem['price'] ?? 0;
                $materialModel->save();
            }


            // Предварительно очищаем все услуги привязанные к комплексу
            ComplexServices::deleteAll(['complex_id' => $id]);
            $newSelectedServices = \Yii::$app->request->post('service') ?? [];
            foreach ($newSelectedServices as $serviceId => $serviceItem) {
                $materialModel = new ComplexServices();
                $materialModel->complex_id = $model->id;
                $materialModel->service_id = $serviceId;
                $materialModel->save();
            }

            return $this->redirect('/complex/detail');
        }

        return $this->render('edit-detail', [
            'model' => $model,
            'services' => $services,
            'selectedServices' => $selectedServices,
            'selectedMaterials' => $selectedMaterials,
            'materials' => $materialsList,
        ]);
    }

}