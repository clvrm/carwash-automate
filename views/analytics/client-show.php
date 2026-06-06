<?php


use app\commons\helpers\CarTypeHelper;
use app\models\ar\order\Orders;
use yii\helpers\Html;

/**
 * @var Orders[] $orders
 * @var int $totalProfit
 * @var bool $isSubscriber
 * @var Orders $lastVisitOrder
 */

$this->title = 'Статистика клиента';
$this->params['header_button'] = '<div class="d-block mr-12">
     <a href="/analytics/clients" target="_blank"><button class="btn btn-outline-primary px-6">Назад</button></a>
    </div>';
?>

<div class="row">
    <div class="col-12">
        <div class="card card-custom  gutter-b">
            <div class="card-header h-auto border-0">
                <!--begin::Title-->
                <div class="card-title py-5">
                    <h3 class="card-label d-flex align-items-center">
                        <span class="d-flex mr-6 font-weight-bold"><?= $lastVisitOrder->car_number ?? '' ?> | <?= $lastVisitOrder->car_region ?? '' ?></span>
                        <span class="d-flex font-weight-normal font-size-h6 border-label
                        <?= $isSubscriber ? 'subscriber-color' : 'client-color' ?>"><?= $isSubscriber ? 'Подписчик' : 'Клиент' ?></span>
                    </h3>
                </div>
                <!--end::Title-->
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <div class="text-muted font-size-h6">
                            ВСЕГО ПОСЕЩЕНИЙ
                        </div>
                        <div class="text-primary font-size-h2 font-weight-bolder">
                            <?= count($orders) ?? 0 ?>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted font-size-h6">
                            ПРИБЫЛЬ С КЛИЕНТА
                        </div>
                        <div class="text-primary font-size-h2 font-weight-bolder">
                            <?= $totalProfit ?? 0 ?> Р.
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted font-size-h6">
                            ПОСЛЕДНЕЕ ПОСЕЩЕНИЕ
                        </div>
                        <div class="text-primary font-size-h2 font-weight-bolder">
                            <?= $lastVisitOrder->date ?? 'Не указано' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-custom mt-6 gutter-b">
            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <div class="d-block text-muted font-size-h5 font-weight-bold mb-4">О КЛИЕНТЕ</div>
                        <div class="form-group">
                            <label>ФИО</label>
                            <input type="text" class="form-control" disabled
                                   value="<?= $lastVisitOrder->client_fullname ?? '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Номер телефона</label>
                            <input type="text" class="form-control" disabled
                                   value="<?= $lastVisitOrder->client_phone ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-block text-muted font-size-h5 font-weight-bold mb-4">ТС КЛИЕНТА</div>
                        <div class="form-group">
                            <label>Тип ТС</label>
                            <input type="text" class="form-control" disabled
                                   value="<?= $lastVisitOrder->car_type ? CarTypeHelper::getLabelType($lastVisitOrder->car_type) : 'Не указан' ?>">
                        </div>
                        <div class="d-flex w-100">
                            <div class="form-group mr-2 w-50">
                                <label>Марка</label>
                                <input type="text" class="form-control" disabled
                                       value="<?= $lastVisitOrder->carBrand->title ?? '-' ?>">
                            </div>
                            <div class="form-group w-50">
                                <label>Модель</label>
                                <input type="text" class="form-control" disabled
                                       value="<?= $lastVisitOrder->carModel->title ?? '-' ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Цвет</label>
                            <input type="text" class="form-control" disabled
                                   value="<?= $lastVisitOrder->color ?? '' ?>">
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="d-block text-muted font-size-h5 font-weight-bold mb-4">НОМЕР ТС</div>
                        <div class="d-flex">
                            <div class="form-group mr-2">
                                <label>Номер</label>
                                <input type="text" class="form-control" disabled
                                       value="<?= $lastVisitOrder->car_number ?? '' ?>">
                            </div>
                            <div class="form-group">
                                <label>Регион</label>
                                <input type="text" class="form-control" disabled
                                       value="<?= $lastVisitOrder->car_region ?? '' ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-custom mt-6 gutter-b">
            <div class="card-header h-auto border-0">
                <!--begin::Title-->
                <div class="card-title py-5">
                    <h3 class="card-label">
                        <span class="d-block text-muted font-weight-bolder">ПОСЕЩЕНИЯ</span>
                    </h3>
                </div>
                <!--end::Title-->
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($orders as $order) : ?>
                        <div class="col-4">
                            <div class="d-flex justify-content-between w-100 px-2 py-3"
                                 style="border-bottom: 1px solid #e2e8f1;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-circle color-status <?= Orders::getCssStatusClass($order->status) ?> mr-2"></i>
                                    <?= date('d-m-Y', strtotime($order->date)) ?? '---' ?>
                                </div>
                                <div class="text-right text-primary">
                                    <a href="/orders/show?id=<?= $order->id ?? '' ?>"
                                       style="text-decoration: underline;">К заказу</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>
</div>