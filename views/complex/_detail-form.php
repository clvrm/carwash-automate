<?php

use app\commons\helpers\CarTypeHelper;
use app\models\ar\complex\Complexes;
use app\models\ar\complex\ComplexServices;
use app\models\ar\service\Services;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @var Services[] $services
 * @var Complexes $model
 * @var ComplexServices[] $selectedServices
 * @var array $arrayMapSelectedServicesIds
 */
$arrayMapSelectedServicesIds = ArrayHelper::map($selectedServices, 'id', 'service_id');
if ($model->type_1_price > 0 && [$model->type_1_price, $model->type_1_price, $model->type_1_price, $model->type_1_price, $model->type_1_price] == [$model->type_1_price, $model->type_2_price, $model->type_3_price, $model->type_4_price, $model->type_5_price]) {
    $priceTypeForAll = 1;
} else {
    $priceTypeForAll = 0;
}
?>

<div id="complexWashFirst" class="row">
    <div class="col-lg-12">
        <div id="personalCreate" class="card card-custom gutter-b">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h6 class="mb-4">
                            На данный вид услуг запись возможна только по телефону или при посещении автомойки
                        </h6>
                    </div>
                    <div class="col-lg-5">
                        <h5 class="text-muted mb-8">Какие услуги входят в комплекс?</h5>
                        <div class="mt-4 form-group">
                            <label>Услуга</label>
                            <input id="serviceAutocompleteInput" type="text" class="form-control"
                                   placeholder="Введите название услуги"/>
                        </div>
                        <div class="create-complex-list custom--scrollable">
                            <?php foreach ($services as $service) : ?>
                                <div class="services-list--item d-flex justify-content-between
                                <?= in_array($service->id, $arrayMapSelectedServicesIds) ? 'd-none-i service-already-selected' : '' ?>"
                                     data-id="<?= $service->id ?? '' ?>">
                                    <div class="service-list--title"><?= $service->name ?? '' ?></div>
                                    <div class="service-list--toolbar">
                                        <div class="service-list--add">
                                            <i class="fas fa-plus mr-4 text-primary"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="offset-lg-1 col-lg-6">
                        <h5 class="text-muted mb-8">Комплекс</h5>

                        <div class="mt-4 form-group">
                            <label>Название комплекса</label>
                            <?= Html::activeInput('text', $model, 'name', ['class' => 'form-control',
                                'placeholder' => 'Введите название комплекса', 'required' => true]) ?>
                        </div>
                        <div class="create-complex-selected-list custom--scrollable">
                            <div class="services-selected-list--item text-muted d-flex justify-content-between">
                                <div class="service-list--title">Въезд - выезд</div>
                            </div>
                            <?php foreach ($selectedServices as $selectedService): ?>
                                <div class="services-selected-list--item d-flex justify-content-between">
                                    <input hidden class="selected-service--input"
                                           name="service[<?= $selectedService->service_id ?? '' ?>]"
                                           value="<?= $selectedService->service_id ?? '' ?>">
                                    <div class="service-list--title"><?= $selectedService->service->name ?? '' ?></div>
                                    <div class="service-list--toolbar">
                                        <div class="complex-selected-service-card-delete"
                                             data-id="<?= $selectedService->service_id ?? '' ?>">
                                            <i class="fas fa-trash text-light-danger text-hover-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="services-selected-list--item default-defined-block d-flex justify-content-between"
                                 style="display: none !important;">
                                <input hidden class="selected-service--input" value="">
                                <div class="service-list--title"></div>
                                <div class="service-list--toolbar">
                                    <div class="complex-selected-service-card-delete" data-id="">
                                        <i class="fas fa-trash text-light-danger text-hover-danger"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-6">
                    <div class="col-12">
                        <div class="text-right">
                            <a href="<?= Yii::$app->request->referrer ?? '/complex/wash' ?>">
                                <button type="button" class="btn btn-lg btn-outline-primary mr-2">Назад</button>
                            </a>
                            <button id="goToSecondStep" type="button" class="btn btn-lg btn-primary">Далее</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<div id="complexWashSecond" class="row" style="display: none">
    <div class="col-lg-12">
        <div id="personalCreate" class="card card-custom gutter-b">
            <div class="card-body">
                <div class="row mt-6">
                    <div class="col-lg-12">

                        <div id="complexDetailingPriceSwitcherBlock">
                            <ul id="complexDetailingPriceSwitcher" class="nav nav-pills nav-fill">
                                <li class="nav-item">
                                    <a class="nav-link  <?= $priceTypeForAll == 1 ? 'active' : '' ?>"
                                       data-toggle="tab"
                                       href="#complexDetailingPriceByAll">
                                        <span class="nav-text">Для всех</span>
                                    </a>
                                </li>
                                <li id="complexDetailingPriceByTypeNav" class="nav-item">
                                    <a class="nav-link <?= $priceTypeForAll == 0 ? 'active' : '' ?>"
                                       data-toggle="tab"
                                       href="#complexDetailingPriceByType" aria-controls="profile">
                                        <span class="nav-text">По типу ТС</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content mt-5" id="myTabContent4">
                                <div class="tab-pane fade <?= $priceTypeForAll == 1 ? 'active show' : '' ?>"
                                     id="complexDetailingPriceByAll" role="tabpanel">
                                    <div class="d-flex align-items-center flex-wrap justify-content-between">
                                        <div class="mt-4 form-group">
                                            <label>Введите сумму ОТ </label>
                                            <?= Html::input('text', 'price_for_all',
                                                $priceTypeForAll == 1 ? $model->type_1_price : null,
                                                ['id' => 'priceForAllInput', 'class' => 'form-control',
                                                    'placeholder' => '2000 Р', 'required' => false]) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade <?= $priceTypeForAll == 0 ? 'active show' : '' ?>"
                                     id="complexDetailingPriceByType" role="tabpanel">
                                    <div class="d-flex align-items-center flex-wrap justify-content-between">
                                        <div class="complex-price--row form-group row mt-3">
                                            <?php for ($carTypeId = 1; $carTypeId <= 5; $carTypeId++): ?>
                                                <div class="col-lg-2">
                                                    <div class="service-form--icon d-block text-center">
                                                        <div class="symbol symbol-30  symbol-fixed mr-2 mt-2">
                                                            <div class="symbol-label bg-transparent"
                                                                 style="background-image: url('<?= CarTypeHelper::getIcon($carTypeId) ?? '' ?>')"></div>
                                                        </div>
                                                        <div class="mt-n2 mb-2"><?= CarTypeHelper::getLabelType($carTypeId) ?? '' ?></div>
                                                    </div>
                                                    <div class="input-group">
                                                        <?= Html::activeInput('number', $model, 'type_' . $carTypeId . '_price', ['class' => 'form-control',
                                                            'placeholder' => 'руб', 'required' => false, 'min' => 0]) ?>
                                                    </div>
                                                </div>
                                            <?php endfor; ?>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><div class="row mt-6">
                    <div class="col-lg-6">
                        <div class="d-flex align-items-center align-content-center justify-content-between">
                            <h6 class="font-weight-normal"><i class="fas fa-wrench mr-4 text-primary"></i>Материалы
                            </h6>
                            <div>
                                <button type="button" class="btn btn-outline-primary btn-lg" data-toggle="modal"
                                        data-target="#serviceMaterialModal"><i
                                            class="font-size-h6 fas fa-plus mr-4 text-primary"></i> Добавить
                                    материал
                                </button>
                            </div>
                        </div>
                        <div class="service-materials--block mt-8">
                            <?php foreach ($selectedMaterials as $selectedMaterial): ?>
                                <div class="d-flex service-material--item mb-2">

                                    <div class="py-2 service-material--info w-75 d-flex justify-content-between">
                                        <div class="service-material--title"><?= $selectedMaterial->material->name ?? '' ?></div>
                                        <div class="service-material--price"><?= $selectedMaterial->price ?? '-' ?>₽
                                        </div>
                                        <input class="service-material--price-input form-control form-control-sm max-w-100px mt-n4"
                                               hidden
                                               name="material[<?= $selectedMaterial->material_id ?>][price]"
                                               value="<?= $selectedMaterial->price ?>">
                                    </div>
                                    <div class="ml-8 min-w-50px d-flex justify-content-between">
                                        <div class="service-card-edit--material mr-4"
                                             data-id="<?= $selectedMaterial->id ?>">
                                            <i class="fas fa-pen text-primary"></i>
                                        </div>
                                        <div class="service-card-delete--material"
                                             data-id="<?= $selectedMaterial->material_id ?>">
                                            <i class="fas fa-trash text-danger"></i>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="d-flex service-material--item default-defined-block mb-2"
                                 style="display: none !important;">

                                <div class="py-2 service-material--info w-75 d-flex justify-content-between">
                                    <div class="service-material--title"></div>
                                    <div class="service-material--price">₽</div>
                                    <input class="service-material--price-input form-control form-control-sm max-w-100px mt-n4"
                                           hidden name=""
                                           value="0">
                                </div>
                                <div class="ml-8 min-w-50px d-flex justify-content-between">
                                    <div class="service-card-edit--material mr-4"
                                         data-id="">
                                        <i class="fas fa-pen text-primary"></i>
                                    </div>
                                    <div class="service-card-delete--material"
                                         data-id="">
                                        <i class="fas fa-trash text-danger"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="text-right">
                            <button id="goToFirstStep" type="button" class="btn btn-lg btn-outline-primary mr-2">
                                Назад
                            </button>
                            <?php if (isset($model->id)): ?>
                                <button type="submit" class="btn btn-lg btn-primary">Обновить</button>
                            <?php else: ?>
                                <button type="submit" class="btn btn-lg btn-primary">Создать</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
