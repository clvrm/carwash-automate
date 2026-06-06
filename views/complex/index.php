<?php

use app\commons\helpers\CarTypeHelper;
use app\models\ar\complex\Complexes;

$this->title = 'Прайс-лист';
$this->params['without_header'] = true;
$isDetailing = in_array($this->context->route, ['complex/detail']) ? 1 : 0;
if (!$isDetailing) {
    $createLink = '/complex/create-wash';
} else {
    $createLink = '/complex/create-detail';
}
$this->params['header_button'] = '<div class="d-block">
     <a href="' . $createLink . '"><button class="btn btn-primary px-6">+ Добавить комплекс</button></a>
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
if (in_array($this->context->route, ['complex/wash'])) {
    $this->params['mobileFixedMenuActiveId'] = 40;
} elseif (in_array($this->context->route, ['complex/detail'])) {
    $this->params['mobileFixedMenuActiveId'] = 900;
}
/**
 * @var Complexes[] $complexes
 */
?>
<?= $this->render('/layouts/elements/page-header', [
    'is_detailing' => in_array($this->context->route, ['complex/detail']),
    'menu' => 'complex',
    'questionTitle' => 'Прайс-лист',
    'question' => 'Перечень услуг автомоечного комплекса с ценами на соответствующие позиции. <br>
Важно! Перечень, который видит клиент в своем приложении. Последовательность отображения в списке, соответствует выставленному порядку в этом разделе'
])  ?>

<div class="row draggable-zone">
    <?php foreach ($complexes as $key => $complex) : ?>
        <div class="col-lg-6 col-xl-4 draggable complex-block-draggable" data-id="<?= $complex->id ?>">
            <div class="card complex-card card-custom card-stretch gutter-b">
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between align-content-stretch w-100 mt-1">
                        <div class="card-title">
                            <div class="label label-primary mr-2 p-3  font-size-h6"><?= $key < 5 ? $key + 1 : '' ?></div>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex">
                                <div class="complex-card-switch mr-4 draggable-handle">
                                    <i class="fas fa-grip-vertical text-primary"></i>
                                </div>
                                <a <?php if ($complex->is_detailing == 0): ?>
                                    href="/complex/edit-wash?id=<?= $complex->id ?>"
                                <?php else: ?>
                                    href="/complex/edit-detail?id=<?= $complex->id ?>"
                                <?php endif; ?>
                                >
                                    <div class="complex-card-edit mr-4">
                                        <i class="fas fa-pen text-primary"></i>
                                    </div>
                                </a>
                                <div class="complex-card-delete" data-id="<?= $complex->id ?>">
                                    <i class="fas fa-trash text-light-danger text-hover-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 d-flex flex-wrap justify-content-between align-content-stretch w-100">
                        <div class="complex-title-block d-block">
                            <div class="text-muted mr-4">Комплекс</div>
                            <h5 class="complex-title">
                                <?= $complex->name ?? '-' ?>
                            </h5>
                        </div>
                        <div class="complex-tab-switcher font-size-sm d-flex align-items-center justify-content-between">
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
                    <div class="complex-card-info card-switch-tab" data-tab="1">
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
                                        <?php if (isset($complex->{'type_' . $carTypeId . '_time'}) && isset($complex->{'type_' . $carTypeId . '_price'})): ?>
                                            <tr>
                                                <td>
                                                    <div class="symbol symbol-25  symbol-fixed mr-2 mt-0"
                                                         data-toggle="popover" title="<?= CarTypeHelper::getLabelType($carTypeId) ?? '' ?>">
                                                        <div class="symbol-label bg-transparent"
                                                             style="background-image: url('<?= CarTypeHelper::getIcon($carTypeId) ?? '' ?>')"></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if ($isDetailing): ?>
                                                        - - - - - -
                                                    <?php else: ?>
                                                        <?= $complex->{'type_' . $carTypeId . '_time'} ?> мин
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $complex->{'type_' . $carTypeId . '_price'} ?> ₽</td>
                                            </tr>
                                        <?php endif; ?>
                                    <?php endfor; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="complex-card-info card-switch-tab" style="display: none" data-tab="2">
                        <div class="d-flex flex-wrap justify-content-between align-content-stretch mb-2">
                            <div class="complex-info--item complex-info--post d-block max-w-50">
                                <?php if (!empty($complex->complexServices)): ?>
                                    <div class="text-muted mr-2">Услуги:</div>
                                    <div class="material-desc-list">
                                        <ul class="pt-2 pl-5">
                                            <?php foreach ($complex->complexServices as $complexService) : ?>
                                                <li class="mb-2 text-muted">
                                                    <span><?= $complexService->service->name ?? '' ?></span></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php else: ?>
                                    <div class="text-muted mr-2">Услуги не указаны</div>
                                <?php endif; ?>
                            </div>
                            <div class="complex-info--item complex-info--post d-block max-w-50">
                                <?php if (!empty($complex->complexMaterials)): ?>
                                    <div class="text-muted mr-2">Материалы:</div>
                                    <div class="material-desc-list">
                                        <ul class="pt-2 pl-5">
                                            <?php foreach ($complex->complexMaterials as $complexMaterial) : ?>
                                                <li class="mb-2 text-muted">
                                                    <span><?= $complexMaterial->material->name ?? '' ?></span></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php elseif($isDetailing): ?>

                                <?php else: ?>
                                    <div class="text-muted mr-2">Материалы не указаны</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="col-lg-6 col-xl-4">
        <a href="<?= $createLink ?? '' ?>">
            <div class="card card-custom complex-card card-create-entity card-stretch gutter-b">
                <div class="h-100 d-flex d-flex align-items-center justify-content-center font-size-lg">
                    <i class="fas fa-plus mr-4 text-primary"></i> Новый комплекс
                </div>
            </div>
        </a>
    </div>
</div>
<script src="/plugins/custom/draggable/draggable.bundle.js"></script>
<script>
    $(document).ready(function () {

        let cwId = $('#mainInfoBlock').data('cw-id');

        $(".complex-card-delete").click(function (e) {
            let id = $(this).data('id');
            let serviceBlock = $(this).closest('.complex-block-draggable');

            Swal.fire({
                title: "Вы действительно хотите удалить данный комплекс",
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
                        url: '/ajax/carwash/delete-complex',
                        data: {
                            'id': id,
                            'cwId': cwId,
                        },
                        success: function (data) {
                            serviceBlock.hide(400);
                            toastr.success("Комплекс удален");
                        }
                    });
                }
            });
        });


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
                $('.complex-block-draggable').each(function (index, item) {
                    arrayIds.push($(item).data('id'));
                });
                $.ajax({
                    type: 'POST',
                    url: '/ajax/carwash/update-complex-position',
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
