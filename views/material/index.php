<?php

use app\models\ar\Materials;

$this->title = 'Материалы';
$this->params['without_header'] = true;
$isDetailing = in_array($this->context->route, ['material/detail']) ? 1 : 0;
$this->params['header_button'] = '<a href="/material/create?isDetailing=' . $isDetailing . '">
<div class="d-block">
     <button class="btn btn-primary px-6">+ Добавить материал</button>
    </div></a>';

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
if (in_array($this->context->route, ['material/wash'])) {
    $this->params['mobileFixedMenuActiveId'] = 60;
} elseif (in_array($this->context->route, ['material/detail'])) {
    $this->params['mobileFixedMenuActiveId'] = 900;
}


/**
 * @var Materials $materials
 */
?>
<?= $this->render('/layouts/elements/page-header', [
    'is_detailing' => $isDetailing,
    'menu' => 'material',
    'questionTitle' => 'Материалы',
    'question' => 'Список расходных материалов, используемых персоналом'
]) ?>

<div class="row">
    <?php foreach ($materials as $material): ?>
        <div class="col-lg-6 col-xl-4 material-col-block">
            <div class="card material-card card-custom card-stretch gutter-b">
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between align-content-stretch w-100">
                        <div class="card-title">
                            <div class="d-block material-title">
                                <div class="text-muted mr-4"><small>Материал</small></div>
                                <h5 class="sales-title">
                                    <?= $material->name ?? '--' ?>
                                </h5>
                            </div>

                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex">
                                <a href="/material/edit?id=<?= $material->id ?>">
                                    <div class="material-card-edit mr-4">
                                        <i class="fas fa-pen text-primary"></i>
                                    </div>
                                </a>
                                <div class="material-card-delete" data-id="<?= $material->id ?>">
                                    <i class="fas fa-trash text-light-danger text-hover-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="material-card-info card-switch-tab" data-tab="1">
                        <div class="d-flex flex-wrap justify-content-between align-content-stretch mb-2">
                            <div class="material-info--item d-block">
                                <div class="text-muted mr-2">Цена</div>
                                <div class="material-price font-size-lg mt-2">
                                    <?= $material->price ?? 'не указано' ?> ₽
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    <div class="col-lg-6 col-xl-4">
        <a href="/material/create?isDetailing=<?= $isDetailing ?>">
            <div class="card card-custom material-card card-create-entity card-stretch gutter-b">
                <div class="h-100 d-flex d-flex align-items-center justify-content-center font-size-lg">
                    <i class="fas fa-plus mr-4 text-primary"></i> Добавить материал
                </div>
            </div>
        </a>
    </div>
</div>
<script>
    $(document).ready(function () {

        let cwId = $('#mainInfoBlock').data('cw-id');

        $('.material-card-delete').on('click', function () {
            let id = $(this).data('id');
            let materialBlock = $(this).closest('.material-col-block');
            $.ajax({
                type: 'POST',
                url: '/ajax/carwash/delete-material',
                data: {
                    'id': id,
                    'cwId': cwId,
                },
                success: function (data) {
                    materialBlock.hide(400);
                    toastr.success("Материал удален");
                }
            });
        });
    })
</script>