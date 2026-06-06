<?php

use app\commons\helpers\CarTypeHelper;
use app\commons\helpers\TimeHelper;
use app\models\ar\car\CarBrands;
use app\models\ar\car\CarModels;
use app\models\ar\complex\Complexes;
use app\models\ar\order\Orders;
use app\models\ar\personal\Personal;
use app\models\ar\service\Services;
use PhpOffice\PhpSpreadsheet\Calculation\DateTimeExcel\Time;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var int $carwashId
 * @var int $postCounts
 * @var Orders $model
 * @var CarBrands $carBrand
 * @var CarModels $carModel
 * @var Personal[] $personalList
 *
 * @var Complexes[] $selectedComplexes
 * @var Services[] $selectedServices
 */

$this->title = 'Просмотр записи';

$this->params['main-data'] = [
    'data-o-id' => $model->id ?? 0,
    'data-log-data' => 'Редактирование заказа: ' . $model->id ?? '--',
];
$this->params['header_button'] = '<div class="d-flex justify-content-between">
<div class="d-block mr-8">
     <div>СТОИМОСТЬ</div>
        <div class="text-primary font-weight-bold font-size-h4 d-flex"><div class="headTotalOrderPrice">' . ($model->total_price ?? 0) . '</div> ₽</div>
    </div>
    <div class="d-block mr-12">
        <div>ВРЕМЯ РАБОТ</div>
        <div class="text-primary font-weight-bold font-size-h4 d-flex"><div class="headTotalWorkTime">' . ($model->work_time ?? 0) . ' </div>&nbsp;мин</div>
    </div>
</div>';

//$model = new DynamicModel(['id' => 1]);
?>

<?php $form = \yii\widgets\ActiveForm::begin([
    'id' => 'createOrderForm',
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>

<div class="row">
    <div class="col-lg-4">
        <div id="" class="card card-custom gutter-b">
            <div class="card-body">
                <h6><i class="fas fa-calendar-day text-primary mr-2"></i>Дата и время записи</h6>
                <div class="row mt-6">
                    <div class="col-6 col-lg-12 col-xl-6">
                        <label>Дата записи</label>
                        <div class="input-group">
                            <input type="text" class="form-control text-center" disabled
                                   placeholder="гггг-мм-дд" value="<?= $model->date ?? '' ?>"/>
                        </div>
                    </div>
                    <div class="col-6 col-lg-12 col-xl-6">
                        <label>Номер поста</label>
                        <div class="input-group">
                            <input type="text" class="form-control text-center" disabled
                                   value="<?= $model->post ?? '' ?>"/>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-6 col-lg-12 col-xl-6">
                        <label>Начало</label>
                        <div class="input-group">
                            <input type="text"
                                   class="form-control text-center" disabled
                                   value="<?= TimeHelper::convertMinToTime($model->start_time) ?? '' ?>"/>
                        </div>
                    </div>
                    <div class="col-6 col-lg-12 col-xl-6">
                        <div class="form-group mb-4">
                            <label>Окончание</label>
                            <div class="input-group">
                                <input type="text" class="form-control text-center" disabled
                                       value="<?= TimeHelper::convertMinToTime($model->end_time) ?? '' ?>"/>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div id="" class="card card-custom gutter-b">
            <div class="card-body">
                <h6><i class="fas fa-car text-primary mr-2"></i>Автомобиль</h6>
                <div class="row mt-4">
                    <div class="col-12">
                        <label>Тип ТС</label>
                        <div class="form-group mb-4">
                            <input type="text" class="form-control text-center" disabled
                                   value="<?= CarTypeHelper::getLabelType($model->car_type) ?? '' ?>"/>
                        </div>
                    </div>

                </div>
                <div class="row mt-4">
                    <div class="col-4 col-lg-6 col-xl-4">
                        <div class="form-group mb-4">
                            <label>Номер ТС</label>
                            <input type="text" class="form-control text-center" disabled
                                   value="<?= $model->car_number ?? '' ?>"/>
                        </div>
                    </div>
                    <div class="col-3 col-lg-6 col-xl-3">
                        <div class="form-group mb-4">
                            <label>Регион</label>
                            <input type="text" class="form-control text-center" disabled
                                   value="<?= $model->car_region ?? '' ?>"/>
                        </div>
                    </div>
                    <div class="col-5 col-lg-12 col-xl-5">
                        <div class="form-group mb-4">
                            <label>Цвет</label>
                            <input type="text" class="form-control text-center" disabled
                                   value="<?= $model->color ?? '' ?>"/>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-6">
                        <label>Марка</label>
                        <div class="form-group mb-4">
                            <input type="text" class="form-control text-center" disabled
                                   value="<?= $carBrand->title ?? '' ?>"/>
                        </div>
                    </div>
                    <div class="col-6">
                        <label>Модель</label>
                        <div class="form-group mb-4">
                            <input type="text" class="form-control text-center" disabled
                                   value="<?= $carModel->title ?? '' ?>"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div id="" class="card card-custom gutter-b">
            <div class="card-body" style="overflow-x: hidden;">
                <h6><i class="fas fa-tools text-primary mr-2"></i>Список работ</h6>
                <div class="row" style="margin: 0 -29px;">
                    <div class="col-12 px-0">
                        <div class="accordion accordion-toggle-arrow" id="accordionWash">
                            <div class="card" style="border: none">
                                <div class="card-header">
                                    <div class="card-title" data-toggle="collapse"
                                         data-target="#accordionWashCollapse">
                                        Мойка
                                    </div>
                                </div>
                                <div id="accordionWashCollapse" class="collapse show">
                                    <div class="card-body">
                                        <div class="wash-complex-selector-block">
                                            <label>Комплекс</label>
                                            <div class="form-group mb-4">
                                                <select id="washComplexSelect" disabled
                                                        class="form-control default-select2 order-complex-select"
                                                        multiple>
                                                    <?php foreach ($selectedComplexes as $complex) : ?>
                                                        <?php if ($complex->is_detailing == false) : ?>
                                                            <option selected
                                                                    value="<?= $complex->entity_id ?>"><?= $complex->name ?? '' ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="wash-service-selector-block">
                                            <label>Услуги</label>
                                            <div class="form-group mb-4">
                                                <select id="washServiceSelect" disabled
                                                        class="form-control default-select2 order-service-select"
                                                        multiple
                                                        name="ServicesList[]">
                                                    <?php foreach ($selectedServices as $service) : ?>
                                                        <?php if ($service->is_detailing == false) : ?>
                                                            <option selected
                                                                    value="<?= $service->entity_id ?>"><?= $service->name ?? '' ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin: 0 -29px">
                    <div class="col-12 px-0">
                        <div class=" accordion accordion-toggle-arrow" id="accordionDitailing">
                            <div class="card" style="border: none">
                                <div class="card-header">
                                    <div class="card-title" data-toggle="collapse"
                                         data-target="#accordionDitailingCollapse">
                                        Дитейлинг
                                    </div>
                                </div>
                                <div id="accordionDitailingCollapse" class="collapse show">
                                    <div class="card-body">
                                        <div class="detail-complex-selector-block">
                                            <label>Комплекс</label>
                                            <div class="form-group mb-4">
                                                <select id="detailingComplexSelect" disabled
                                                        class="form-control default-select2 order-complex-select"
                                                        multiple
                                                        name="ComplexesList[]">
                                                    <?php foreach ($selectedComplexes as $complex) : ?>
                                                        <?php if ($complex->is_detailing == true) : ?>
                                                            <option selected
                                                                    value="<?= $complex->entity_id ?>"><?= $complex->name ?? '' ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="detail-service-selector-block">
                                            <label>Услуги</label>
                                            <div class="form-group mb-4">
                                                <select id="detailingServiceSelect" disabled
                                                        class="form-control default-select2 order-service-select"
                                                        multiple
                                                        name="ServicesList[]">
                                                    <?php foreach ($selectedServices as $service) : ?>
                                                        <?php if ($service->is_detailing == true) : ?>
                                                            <option selected
                                                                    value="<?= $service->entity_id ?>"><?= $service->name ?? '' ?></option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="" class="card card-custom gutter-b">
            <div class="card-body">
                <h6><i class="fas fa-comment-alt text-primary mr-2"></i>Комментарий клиента</h6>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group mb-0">
                                <textarea class="form-control text-center" disabled
                                          rows="2"><?= $model->client_comment ?? '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="" class="card card-custom gutter-b">
            <div class="card-body">
                <h6><i class="fas fa-comment-alt text-primary mr-2"></i>Комментарий администратора</h6>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group mb-0">
                                <textarea class="form-control text-center" disabled
                                          rows="2"><?= $model->admin_comment ?? '' ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <div class="col-lg-4">
        <div id="" class="card card-custom gutter-b">
            <div class="card-body">
                <h6><i class="fas fa-wallet text-primary mr-2"></i>Расчет стоимости</h6>
                <div class="row">
                    <div class="col-12">
                        <div class="selected-order-service-list mt-4">
                        </div>
                    </div>
                </div>
                <div class="row mt-6">
                    <div class="col-12">
                        <label>Исполнитель</label>
                        <div class="form-group mb-4">
                            <?php if (!empty($model->personal_fullname)): ?>
                                <input type="text" class="form-control text-center" disabled
                                       value="<?= $model->personal_fullname ?? '' ?>"/>
                            <?php else : ?>
                                <select id="personalSelect" class="form-control default-select2">
                                    <option value="">Выберите исполнителя</option>
                                    <?php foreach ($personalList as $personal): ?>
                                        <?php if (!$personal->is_approved) {
                                            continue;
                                        } ?>
                                        <option value="<?= $personal->id ?>"><?= $personal->getShortUsername() ?? '' ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between order-total-price">
                            <div class="font-size-h5">Всего</div>
                            <div class="font-size-h5">
                                    <span id="calculatedTotalOrderPrice">
                                    <?php if (isset($model->total_price, $model->sale)): ?>
                                        <?= ($model->total_price + $model->sale) ?? 0 ?>
                                    <?php elseif (isset($model->total_price)): ?>
                                        <?= $model->total_price ?? 0 ?>
                                    <?php else : ?>
                                        0
                                    <?php endif; ?>
                                    </span>₽
                            </div>
                        </div>
                        <div class="d-flex justify-content-between order-sale-price mt-2">
                            <div class="font-size-h6">Скидка</div>
                            <div class="font-size-h6"><span id="calculatedOrderSale"><?= $model->sale ?? 0 ?></span>₽
                            </div>
                        </div>
                        <div class="mt-4 row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-content-center align-items-center">
                                    <div class="mr-4 d-flex">
                                        <i class="fas fa-receipt text-primary mr-2"></i>
                                        Итого
                                    </div>
                                    <input type="text" class="form-control text-center h4" disabled
                                           value="<?= $model->total_price ? $model->total_price . '₽' : '0₽' ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="" class="card card-custom gutter-b">
            <div class="card-body">
                <h6><i class="fas fa-user-cog text-primary mr-2"></i>Клиент</h6>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group mb-4">
                            <label>ФИО</label>
                            <input type="text" class="form-control text-center" disabled
                                   value="<?= $model->client_fullname ?? '' ?>"/>
                        </div>
                        <div class="form-group mb-4">
                            <label>Телефон</label>
                            <input type="text" class="form-control text-center" disabled
                                   value="<?= $model->client_phone ?? '' ?>"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>


<script>
    $(document).ready(function () {
        let cwId = $('#mainInfoBlock').data('cw-id');
        let pId = $('#mainInfoBlock').data('p-id');
        let oId = $('#mainInfoBlock').data('o-id');

        $('#personalSelect').on('change', function () {
            let selectedPersonalId = $(this).val();
            $.ajax({
                type: 'POST',
                url: '/ajax/orders/change-order-personal',
                data: {orderId: oId, pId: pId, selectedPersonal: selectedPersonalId},
                success: function (data) {
                    if (!data.result) {
                        toastr.warning("Не удалось изменить исполнителя для заказа");
                    } else {
                        toastr.success("Исполнитель для данного заказа успешно изменен");
                    }
                }
            });
        });

    });


</script>

<style>
    .select2-container--default.select2-container--disabled .select2-selection--multiple, .select2-container--default.select2-container--disabled .select2-selection--single {
        cursor: not-allowed;
        background-color: #f3f6f9 !important;
        opacity: 1 !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__rendered .select2-selection__choice {
        background: #D5F0DC !important;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove {
        display: none !important;
    }
</style>