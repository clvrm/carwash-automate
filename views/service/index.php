<?php

use app\commons\helpers\CarTypeHelper;
use app\models\ar\service\Services;

$this->title = 'Прайс-лист';
$this->params['without_header'] = true;
$isDetailing = in_array($this->context->route, ['service/detail']) ? 1 : 0;

$this->params['header_button'] = '<div class="d-block">
     <a href="/service/create?isDetailing=' . $isDetailing . '">
     <button class="btn btn-primary px-6">+ Добавить услугу</button>
     </a>
    </div>';

$this->params['mobileFixedMenu'] = [
    '1' => ['title' => 'Мойка', 'parent_id' => null, 'url' => ''],
    '2' => ['title' => 'Дитейлинг', 'parent_id' => null, 'url' => ''],
    '40' => ['title' => 'Комплексы', 'parent_id' => 1, 'url' => '/complex/wash'],
    '50' => ['title' => 'Услуги', 'parent_id' => 1, 'url' => '/service/wash'],
    '60' => ['title' => 'Материалы', 'parent_id' => 1, 'url' => '/material/wash'],
    '700' => ['title' => 'Комплексы', 'parent_id' => 2, 'url' => '/complex/detail'],
    '800' => ['title' => 'Услуги', 'parent_id' => 2, 'url' => '/service/detail'],
    '900' => ['title' => 'Материалы', 'parent_id' => 2, 'url' => '/material/detail'],
];

if (in_array($this->context->route, ['service/wash'])) {
    $this->params['mobileFixedMenuActiveId'] = 50;
} elseif (in_array($this->context->route, ['service/detail'])) {
    $this->params['mobileFixedMenuActiveId'] = 800;
}

/**
 * @var Services $services
 * @var Services $service
 */
?>
<?= $this->render('/layouts/elements/page-header', [
    'is_detailing' => $isDetailing,
    'menu' => 'service',
    'questionTitle' => 'Прайс-лист',
    'question' => 'Перечень услуг автомоечного комплекса с ценами на соответствующие позиции. <br>
Важно! Перечень, который видит клиент в своем приложении. Последовательность отображения в списке, соответствует высталенному порядку в этом разделе'
]) ?>

<div class="row draggable-zone">
    <?php foreach ($services as $key => $service) : ?>
        <div class="col-lg-6 col-xl-4 draggable service-block-draggable" data-id="<?= $service->id ?>">
            <div class="card service-card card-custom card-stretch gutter-b">
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between align-content-stretch w-100 mt-1">
                        <div class="card-title">
                            <div class="label label-primary mr-2 p-3  font-size-h6"><?= $key < 5 ? $key + 1 : '' ?></div>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex">
                                <div class="service-card-switch mr-4 draggable-handle">
                                    <i class="fas fa-grip-vertical text-primary"></i>
                                </div>
                                <a href="/service/edit?id=<?= $service->id ?>">
                                    <div class="service-card-edit mr-4">
                                        <i class="fas fa-pen text-primary"></i>
                                    </div>
                                </a>
                                <div class="service-card-delete" data-id="<?= $service->id ?>">
                                    <i class="fas fa-trash text-light-danger text-hover-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 d-flex flex-wrap justify-content-between align-content-stretch w-100">
                        <div class="service-title-block d-block">
                            <div class="text-muted mr-4">Услуга</div>
                            <h5 class="service-title">
                                <?= $service->name ?? '-' ?>
                            </h5>
                        </div>
                        <div class="service-tab-switcher font-size-sm d-flex align-items-center justify-content-between">
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
                    <div class="service-card-info card-switch-tab" data-tab="1">
                        <div class="d-block flex-wrap justify-content-between align-content-stretch mb-2">
                            <div class="table-responsive">
                                <table class="table price-table table-borderless table-vertical-center">
                                    <thead>
                                    <tr class="text-muted font-weight-normal">
                                        <th class="py-0 w-75px"></th>
                                        <th class="py-0 min-w-100px font-weight-normal">Время</th>
                                        <th class="py-0 min-w-100px font-weight-normal">Цена</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php for ($carTypeId = 1; $carTypeId <= 5; $carTypeId++): ?>
                                        <?php if (isset($service->{'type_' . $carTypeId . '_time'}) && isset($service->{'type_' . $carTypeId . '_price'})): ?>
                                            <tr>
                                                <td>
                                                    <div class="symbol symbol-25  symbol-fixed mr-2 mt-0"
                                                         data-toggle="popover" title="<?= CarTypeHelper::getLabelType($carTypeId) ?? '' ?>">
                                                        <div class="symbol-label bg-transparent"
                                                             style="background-image: url('<?= CarTypeHelper::getIcon($carTypeId) ?? '' ?>')"></div>
                                                    </div>
                                                </td>
                                                <td><?= $service->{'type_' . $carTypeId . '_time'} ?> мин</td>
                                                <td><?= $service->{'type_' . $carTypeId . '_price'} ?> ₽</td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="service-card-info card-switch-tab" style="display: none" data-tab="2">
                        <div class="d-flex flex-wrap justify-content-between align-content-stretch mb-2">
                            <div class="service-info--item service-info--post d-block">
                                <?php if (!empty($service->serviceMaterials)): ?>
                                    <div class="text-muted mr-2">Материалы:</div>
                                    <div class="material-desc-list">
                                        <ul class="pt-2 pl-5">
                                            <?php foreach ($service->serviceMaterials as $serviceMaterial) : ?>
                                                <li class="mb-2 text-muted">
                                                    <span><?= $serviceMaterial->material->name ?? '' ?></span></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted mr-2">Материалы для услуги не указаны</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="col-lg-6 col-xl-4">
        <a href="/service/create?isDetailing=<?= $isDetailing ?>">
            <div class="card card-custom service-card card-create-entity card-stretch gutter-b">
                <div class="h-100 d-flex d-flex align-items-center justify-content-center font-size-lg">
                    <i class="fas fa-plus mr-4 text-primary"></i> Новая услуга
                </div>
            </div>
        </a>
    </div>
</div>
<script src="/plugins/custom/draggable/draggable.bundle.js"></script>
<script>
    $(document).ready(function () {

        let cwId = $('#mainInfoBlock').data('cw-id');

        $('.service-card-delete').on('click', function () {
            let id = $(this).data('id');
            let serviceBlock = $(this).closest('.service-block-draggable');

            Swal.fire({
                title: "Вы действительно хотите удалить данную услугу?",
                text: "Это действие невозможно отменить",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                cancelButtonText: 'Отменить',
                confirmButtonText: "Да, удалить!",
                reverseButtons: true,
                customClass: {
                    confirmButton: "btn btn-danger btn-lg",
                    cancelButton: "btn btn-outline-secondary btn-lg"
                }
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: 'POST',
                        url: '/ajax/carwash/delete-service',
                        data: {
                            'id': id,
                            'cwId': cwId,
                        },
                        success: function (data) {
                            serviceBlock.hide(400);
                            toastr.success("Услуга удалена");
                        }
                    });
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
                $('.service-block-draggable').each(function (index, item) {
                    arrayIds.push($(item).data('id'));
                });
                $.ajax({
                    type: 'POST',
                    url: '/ajax/carwash/update-service-position',
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