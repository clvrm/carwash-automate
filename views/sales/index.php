<?php

use app\models\ar\carwash\CarwashSales;

$this->title = 'Скидки';

$this->params['header_button'] = '<div class="d-block mr-12">
     <a href="/sales/create"><button class="btn btn-primary px-6">+ Создать скидку</button></a>
    </div>';


/**
 * @var CarwashSales $sales
 * @var CarwashSales $oldSales
 */
?>
<div class="row draggable-zone">
    <?php foreach ($sales as $sale) : ?>
        <div class="col-lg-6 col-xl-4 draggable sale-block-draggable" data-id="<?= $sale->id ?>">
            <div class="card sales-card card-custom card-stretch gutter-b">
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between align-content-stretch w-100">
                        <div class="card-title">
                            <?php if ($sale->end_at >= date('Y-m-d') && $sale->start_at <= date('Y-m-d')): ?>
                                <div class="d-flex align-items-center sales-time bg-danger-o-30">
                                    <i class="fas fa-clock mr-2"></i>до <?= $sale->end_at ?>
                                </div>
                            <?php else: ?>
                                <div class="d-flex align-items-center sales-time bg-primary-o-30">
                                    <i class="fas fa-clock mr-2"></i>с <?= $sale->start_at ?>
                                    по <?= $sale->end_at ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex">
                                <div class="sales-card-switch mr-4 draggable-handle">
                                    <i class="fas fa-grip-vertical text-primary"></i>
                                </div>
                                <a href="/sales/edit?id=<?= $sale->id ?>">
                                    <div class="sales-card-edit mr-4">
                                        <i class="fas fa-pen text-primary"></i>
                                    </div>
                                </a>
                                <div class="sales-card-delete" data-id="<?= $sale->id ?>">
                                    <i class="fas fa-trash text-light-danger text-hover-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 d-flex flex-wrap justify-content-between align-content-stretch w-100">
                        <div class="sales-title-block d-block">
                            <div class="text-muted mr-4">Скидка</div>
                            <h5 class="sales-title">
                                <?= $sale->name ?? 'Без названия' ?>
                            </h5>
                        </div>
                        <div class="sales-tab-switcher font-size-sm d-flex align-items-center justify-content-between">
                            <div class="mr-3 card-tab-ballon card-tab-active" data-switch-tab="1">
                                <i class="fas fa-circle text-muted"></i>
                            </div>
                            <div class="card-tab-ballon" data-switch-tab="2">
                                <i class="fas fa-circle text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="sales-card-info card-switch-tab" data-tab="1">
                        <div class="d-flex flex-wrap justify-content-between align-content-stretch mb-2">
                            <div class="sales-info--item sales-info--post d-flex">
                                <div class="text-muted mr-2">Для кого:</div>
                                <?= $sale->saleForLabel() ?>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap justify-content-between align-content-stretch mb-2">
                            <div class="person-info--item person-info--post d-flex">
                                <div class="text-muted mr-2 min-w-50px">На что:</div>
                                <?= $sale->saleItemsList() ?>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap justify-content-between align-content-stretch mb-2">
                            <div class="person-info--item person-info--post d-flex">
                                <div class="text-muted mr-2">Вид скидки:</div>
                                <?= $sale->saleWithTypeLabel() ?>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap justify-content-between align-content-stretch">
                            <div class="person-info--item person-info--post d-flex">
                                <div class="text-muted mr-2">Суммирование:</div>
                                <?php if ($sale->sum_up_discount): ?>
                                    Да
                                <?php else : ?>
                                    Нет
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="sales-card-info card-switch-tab" style="display: none" data-tab="2">
                        <div class="d-flex flex-wrap justify-content-between align-content-stretch mb-2">
                            <div class="sales-info--item sales-info--post d-flex">
                                <div class="text-muted mr-2">Описание:</div>
                                <?= $sale->description ?? '' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <?php foreach ($oldSales as $sale) : ?>
        <div class="col-lg-6 col-xl-4 sale-block-draggable" data-id="<?= $sale->id ?>">
            <div class="card sales-card card-custom card-stretch gutter-b">
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between align-content-stretch w-100">
                        <div class="card-title">
                            <div class="d-flex align-items-center sales-time bg-gray-200">
                                <i class="fas fa-clock mr-2"></i>Закончилась
                            </div>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex">
                                <a href="/sales/edit?id=<?= $sale->id ?>">
                                    <div class="sales-card-edit mr-4">
                                        <i class="fas fa-pen text-primary"></i>
                                    </div>
                                </a>
                                <div class="sales-card-delete" data-id="<?= $sale->id ?>">
                                    <i class="fas fa-trash text-light-danger text-hover-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 d-flex flex-wrap justify-content-between align-content-stretch w-100">
                        <div class="sales-title-block d-block">
                            <div class="text-muted mr-4">Скидка</div>
                            <h5 class="sales-title">
                                <?= $sale->name ?? 'Без названия' ?>
                            </h5>
                        </div>
                        <div class="sales-tab-switcher font-size-sm d-flex align-items-center justify-content-between">
                            <div class="mr-3 card-tab-ballon card-tab-active" data-switch-tab="1">
                                <i class="fas fa-circle text-muted"></i>
                            </div>
                            <div class="card-tab-ballon" data-switch-tab="2">
                                <i class="fas fa-circle text-muted"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="sales-card-info card-switch-tab" data-tab="1">
                        <div class="d-flex flex-wrap justify-content-between align-content-stretch mb-2">
                            <div class="sales-info--item sales-info--post d-flex">
                                <div class="text-muted mr-2">Для кого:</div>
                                <?= $sale->saleForLabel() ?>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap justify-content-between align-content-stretch mb-2">
                            <div class="person-info--item person-info--post d-flex">
                                <div class="text-muted mr-2 min-w-50px">На что:</div>
                                <?= $sale->saleItemsList() ?>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap justify-content-between align-content-stretch mb-2">
                            <div class="person-info--item person-info--post d-flex">
                                <div class="text-muted mr-2">Вид скидки:</div>
                                <?= $sale->saleWithTypeLabel() ?>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap justify-content-between align-content-stretch">
                            <div class="person-info--item person-info--post d-flex">
                                <div class="text-muted mr-2">Суммирование:</div>
                                <?php if ($sale->sum_up_discount): ?>
                                    Да
                                <?php else : ?>
                                    Нет
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="sales-card-info card-switch-tab" style="display: none" data-tab="2">
                        <div class="d-flex flex-wrap justify-content-between align-content-stretch mb-2">
                            <div class="sales-info--item sales-info--post d-flex">
                                <div class="text-muted mr-2">Описание:</div>
                                <?= $sale->description ?? '' ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <div class="col-lg-6 col-xl-4">
        <a href="/sales/create">
            <div class="card card-custom sales-card card-create-entity card-stretch gutter-b">
                <div class="h-100 d-flex d-flex align-items-center justify-content-center font-size-lg">
                    <i class="fas fa-plus mr-4 text-primary"></i> Добавить скидку
                </div>
            </div>
        </a>
    </div>
</div>
<script src="/plugins/custom/draggable/draggable.bundle.js"></script>
<script>
    $(document).ready(function () {

        let cwId = $('#mainInfoBlock').data('cw-id');

        $('.sales-card-delete').on('click', function () {
            let id = $(this).data('id');
            let saleBlock = $(this).closest('.sale-block-draggable');
            $.ajax({
                type: 'POST',
                url: '/ajax/carwash/delete-sale',
                data: {
                    'id': id,
                    'cwId': cwId,
                },
                success: function (data) {
                    saleBlock.hide(400);
                    toastr.success("Скидка удалена");
                }
            });
        })


        var containers = document.querySelectorAll('.draggable-zone');

        var swappable = new Sortable.default(containers, {
            draggable: '.draggable',
            handle: '.draggable .draggable-handle',
            mirror: {
                appendTo: 'body',
                constrainDimensions: true
            }
        });

        swappable.on('sortable:stop', function (i, e) {
            setTimeout(function () {
                let arrayIds = [];
                $('.sale-block-draggable').each(function (index, item) {
                    arrayIds.push($(item).data('id'));
                });
                $.ajax({
                    type: 'POST',
                    url: '/ajax/carwash/update-sales-position',
                    data: {
                        'ids': arrayIds,
                        'cwId': cwId,
                    },
                    success: function (data) {
                        toastr.success("Порядок обновлен");
                    }
                });
            }, 200);
        });


    })
</script>
