<?php

use app\commons\helpers\CarTypeHelper;

$this->title = 'Создание комплекса';
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
            'active' => true,
        ],
        [
            'subtitle' => 'Шаг два',
            'title' => 'Параметры комплекса',
            'link' => '///',
        ],
    ]
]) ?>
<div class="row">
    <div class="col-lg-12">
        <div id="personalCreate" class="card card-custom gutter-b">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-5">
                        <h5 class="text-muted mb-8">Какие услуги входят в комплекс?</h5>
                        <div class="mt-4 form-group">
                            <label>Услуга</label>
                            <input type="email" class="form-control" placeholder="Enter contact number"/>
                        </div>
                        <div class="create-complex-list custom--scrollable">
                            <div class="services-list--item d-flex justify-content-between">
                                <div class="service-list--title">Чернение шин</div>
                                <div class="service-list--toolbar">
                                    <div class="sales-card-delete">
                                        <i class="fas fa-plus mr-4 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="services-list--item d-flex justify-content-between">
                                <div class="service-list--title">Чернение шин 2</div>
                                <div class="service-list--toolbar">
                                    <div class="sales-card-delete">
                                        <i class="fas fa-plus mr-4 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="services-list--item d-flex justify-content-between">
                                <div class="service-list--title">Чернение шин 3</div>
                                <div class="service-list--toolbar">
                                    <div class="sales-card-add">
                                        <i class="fas fa-plus mr-4 text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="offset-lg-1 col-lg-6">
                        <h5 class="text-muted mb-8">Комплекс</h5>

                        <div class="mt-4 form-group">
                            <label>Название комплекса</label>
                            <input type="email" class="form-control" placeholder="Enter contact number"/>
                        </div>
                        <div class="create-complex-selected-list custom--scrollable">
                            <div class="services-selected-list--item text-muted d-flex justify-content-between">
                                <div class="service-list--title">Въезд - выезд</div>
                            </div>
                            <div class="services-selected-list--item d-flex justify-content-between">
                                <div class="service-list--title">Полировка</div>
                                <div class="service-list--toolbar">
                                    <div class="sales-card-delete">
                                        <i class="fas fa-trash text-light-danger text-hover-danger"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="services-selected-list--item d-flex justify-content-between">
                                <div class="service-list--title">Химчистка салона</div>
                                <div class="service-list--toolbar">
                                    <div class="sales-card-delete">
                                        <i class="fas fa-trash text-light-danger text-hover-danger"></i>
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
                            <button type="submit" class="btn btn-lg btn-primary">Далее</button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>