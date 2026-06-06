<?php

use app\commons\helpers\CarTypeHelper;

$this->title = 'Параметры комплекса';
$this->params['mobileFixedMenuBackUrl'] = '/sales/';
$this->params['without_header'] = true;

?>
<?= $this->render('/layouts/elements/page-header', [
    'is_detailing' => true,
    'menu' => 'complex',
    'tabs' => [
        [
            'subtitle' => 'Шаг один',
            'title' => 'Выбор услуг',
            'link' => '///',
        ],
        [
            'subtitle' => 'Шаг два',
            'title' => 'Параметры комплекса',
            'link' => '///',
            'active' => true,
        ],
    ]
]) ?>
<div class="row">
    <div class="col-lg-12">
        <div id="personalCreate" class="card card-custom gutter-b">
            <div class="card-body">
                <div class="row mt-6">
                    <div class="col-lg-12">
                        <h6 class="font-weight-normal"><i class="fas fa-clock mr-4 text-primary"></i>Длительность
                            комплекса для каждого типа ТС</h6>
                        <div class="form-group row mt-3">
                            <div class="col-lg-2">
                                <div class="service-form--icon d-block text-center">
                                    <div class="symbol symbol-30  symbol-fixed mr-2 mt-2">
                                        <div class="symbol-label bg-transparent"
                                             style="background-image: url('<?= CarTypeHelper::getIcon(1) ?>')"></div>
                                    </div>
                                    <div class="mt-n2 mb-2">Легковые</div>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="30 мин"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-white">
                                            <i class="fas fa-stopwatch"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="service-form--icon d-block text-center">
                                    <div class="symbol symbol-30  symbol-fixed mr-2 mt-2">
                                        <div class="symbol-label bg-transparent"
                                             style="background-image: url('<?= CarTypeHelper::getIcon(2) ?>')"></div>
                                    </div>
                                    <div class="mt-n2 mb-2">Легковые</div>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="30 мин"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-white">
                                            <i class="fas fa-stopwatch"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="service-form--icon d-block text-center">
                                    <div class="symbol symbol-30  symbol-fixed mr-2 mt-2">
                                        <div class="symbol-label bg-transparent"
                                             style="background-image: url('<?= CarTypeHelper::getIcon(3) ?>')"></div>
                                    </div>
                                    <div class="mt-n2 mb-2">Легковые</div>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="30 мин"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text bg-white">
                                            <i class="fas fa-stopwatch"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-6">
                    <div class="col-lg-12">
                        <h6 class="font-weight-normal"><i class="fas fa-tag mr-4 text-primary"></i>Цена комплекса для
                            каждого типа ТС</h6>
                        <div class="form-group row mt-3">
                            <div class="col-lg-2">
                                <div class="service-form--icon d-block text-center">
                                    <div class="symbol symbol-30  symbol-fixed mr-2 mt-2">
                                        <div class="symbol-label bg-transparent"
                                             style="background-image: url('<?= CarTypeHelper::getIcon(1) ?>')"></div>
                                    </div>
                                    <div class="mt-n2 mb-2">Легковые</div>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="30 мин"/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="service-form--icon d-block text-center">
                                    <div class="symbol symbol-30  symbol-fixed mr-2 mt-2">
                                        <div class="symbol-label bg-transparent"
                                             style="background-image: url('<?= CarTypeHelper::getIcon(2) ?>')"></div>
                                    </div>
                                    <div class="mt-n2 mb-2">Легковые</div>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="30 мин"/>
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <div class="service-form--icon d-block text-center">
                                    <div class="symbol symbol-30  symbol-fixed mr-2 mt-2">
                                        <div class="symbol-label bg-transparent"
                                             style="background-image: url('<?= CarTypeHelper::getIcon(3) ?>')"></div>
                                    </div>
                                    <div class="mt-n2 mb-2">Легковые</div>
                                </div>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="30 мин"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-6">
                    <div class="col-lg-6">
                        <div class="d-flex align-items-center align-content-center justify-content-between">
                            <h6 class="font-weight-normal"><i class="fas fa-wrench mr-4 text-primary"></i>Материалы</h6>
                            <div>
                                <button class="btn btn-outline-primary btn-lg"><i
                                            class="font-size-h6 fas fa-plus mr-4 text-primary"></i> Добавить материал
                                </button>
                            </div>
                        </div>
                        <div class="service-materials--block mt-8">
                            <div class="d-flex service-material--item mb-2">
                                <div class="py-2 service-material--info w-75 d-flex justify-content-between">
                                    <div>Шампунь</div>
                                    <div>200 ₽</div>
                                </div>
                                <div class="ml-8 min-w-50px d-flex justify-content-between">
                                    <div class="sales-card-edit mr-4">
                                        <i class="fas fa-pen text-primary"></i>
                                    </div>
                                    <div class="sales-card-delete">
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
                            <button type="reset" class="btn btn-lg btn-outline-primary mr-2">Назад</button>
                            <button type="submit" class="btn btn-lg btn-primary">Создать</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>