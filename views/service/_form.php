<?php

use app\commons\helpers\CarTypeHelper;
use app\models\ar\Materials;
use app\models\ar\service\ServiceMaterials;
use app\models\ar\service\Services;
use yii\helpers\Html;

/**
 * @var Services $model
 * @var ServiceMaterials[] $selectedMaterials
 */
?>
<div class="card-body">
    <div class="row">
        <div class="col-lg-4">
            <div class="mt-4 form-group">
                <label>Название услуги</label>
                <?= Html::activeInput('text', $model, 'name', ['class' => 'form-control',
                    'placeholder' => 'Введите название услуги', 'required' => true]) ?>
            </div>
        </div>
    </div>

    <div class="row service-time--row mt-6">
        <div class="col-lg-12">
            <h6 class="font-weight-normal"><i class="fas fa-clock mr-4 text-primary"></i>Длительность
                услуги для каждого типа ТС</h6>
            <div class="form-group row mt-3">
                <?php for ($carTypeId = 1; $carTypeId <= 5; $carTypeId++): ?>
                    <div class="col-lg-2">
                        <div class="service-form--icon d-block text-center">
                            <div class="symbol symbol-30  symbol-fixed mr-2 mt-2">
                                <div class="symbol-label bg-transparent"
                                     style="background-image: url('<?= CarTypeHelper::getIcon($carTypeId) ?? '' ?>')"></div>
                            </div>
                            <div class="mt-n2 mb-2"><?= CarTypeHelper::getLabelType($carTypeId) ?? ' - ' ?></div>
                        </div>
                        <div class="input-group">
                            <?= Html::activeInput('number', $model, 'type_' . $carTypeId . '_time', ['class' => 'form-control',
                                'placeholder' => '30 мин', 'required' => false, 'min' => 0]) ?>
                            <div class="input-group-append">
                                        <span class="input-group-text bg-white">
                                            <i class="fas fa-stopwatch"></i>
                                        </span>
                            </div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <div class="row mt-6 service-price--row">
        <div class="col-lg-12">
            <h6 class="font-weight-normal"><i class="fas fa-tag mr-4 text-primary"></i>Цена услуги для
                каждого типа ТС</h6>
            <div class="form-group row mt-3">
                <?php for ($carTypeId = 1; $carTypeId <= 5; $carTypeId++): ?>
                    <div class="col-lg-2">
                        <div class="service-form--icon d-block text-center">
                            <div class="symbol symbol-30  symbol-fixed mr-2 mt-2">
                                <div class="symbol-label bg-transparent"
                                     style="background-image: url('<?= CarTypeHelper::getIcon($carTypeId) ?? '' ?>')"></div>
                            </div>
                            <div class="mt-n2 mb-2"><?= CarTypeHelper::getLabelType($carTypeId) ?? ' - ' ?></div>
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
    <div class="row mt-6">
        <div class="col-lg-6">
            <div class="d-flex align-items-center align-content-center justify-content-between">
                <h6 class="font-weight-normal"><i class="fas fa-wrench mr-4 text-primary"></i>Материалы</h6>
                <div>
                    <button type="button" class="btn btn-outline-primary btn-lg" data-toggle="modal"
                            data-target="#serviceMaterialModal"><i
                                class="font-size-h6 fas fa-plus mr-4 text-primary"></i> Добавить материал
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
                <a href="<?= Yii::$app->request->referrer ?? '/material/' ?>">
                    <button type="reset" class="btn btn-lg btn-outline-primary mr-2">Назад</button>
                </a>
                <button type="submit" class="btn btn-lg btn-primary">Создать</button>
            </div>
        </div>
    </div>
</div>