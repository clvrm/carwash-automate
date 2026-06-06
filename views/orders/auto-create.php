<?php

use app\commons\helpers\CarTypeHelper;
use app\models\ar\car\CarBrands;
use app\models\ar\car\CarModels;
use app\models\ar\carwash\CarwashSales;
use app\models\ar\complex\Complexes;
use app\models\ar\complex\ComplexServices;
use app\models\ar\order\Orders;
use app\models\ar\personal\Personal;
use app\models\ar\service\Services;
use app\widgets\helpers\SVG;
use yii\base\DynamicModel;
use yii\base\Model;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/**
 * @var int $carwashId
 * @var int $postCounts
 * @var Orders $model
 * @var CarBrands[] $carBrands
 * @var CarModels[] $carModels
 * @var Personal[] $personalList
 * @var Services $washServices
 * @var Services $detailingServices
 * @var Complexes $detailingComplexes
 * @var Complexes $washComplexes
 *
 * @var Complexes $selectedWashComplexes
 * @var Complexes $selectedDetailingComplexes
 * @var Services $selectedWashServices
 * @var Services $selectedDetailingServices
 */


$this->title = 'Автоматическая запись';
$this->params['header_button'] = '<div class="d-flex justify-content-between">
<div class="d-block mr-8">
     <div>СТОИМОСТЬ</div>
        <div class="text-primary font-weight-bold font-size-h4 d-flex"><div class="headTotalOrderPrice">0</div> ₽</div>
    </div>
    <div class="d-block mr-12">
        <div>ВРЕМЯ РАБОТ</div>
        <div class="text-primary font-weight-bold font-size-h4 d-flex"><div class="headTotalWorkTime">00</div> мин</div>
    </div>
<div class="d-block ">
     <button class="btn btn-primary px-6 mt-2 create-order-button">Создать запись</button>
    </div>

</div>';

?>

<?php $form = \yii\widgets\ActiveForm::begin([
    'id' => 'createOrderForm',
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>

<div class="row">
    <div class="col-8">
        <div class="page-header-switch p-6 pr-0 mb-6 bg-white d-flex justify-content-between">
            <div id="firstStepHeaderTab" class="d-block goToFirstStep">
                <div class="header-tab-subtitle text-primary">
                    Шаг один
                </div>
                <a class="header-tab-title text-dark" href="#">
                    <div class="font-size-h6">Просмотр информации</div>
                </a>
            </div>
            <div class="page-header-switch--divider"></div>
            <div id="secondStepHeaderTab" class="d-block goToSecondStep">
                <div class="header-tab-subtitle text-muted">
                    Шаг два
                </div>
                <a class="header-tab-title text-dark" href="#">
                    <div class="font-size-h6">Создание быстрой записи</div>
                </a>
            </div>
        </div>
    </div>
</div>
<div id="firstStep" class="row">
    <div class="col-lg-4">
        <div id="" class="card card-custom gutter-b">
            <div class="card-body">
                <h6><i class="fas fa-calendar-day text-primary mr-2"></i>Дата и время записи</h6>
                <div class="row mt-6">
                    <div class="col-lg-12">
                        <label>Дата записи</label>
                        <div class="input-group date-picker">
                            <input id="orderStartDateInput" type="text" name="Orders[date]"
                                   class="form-control" readonly
                                   placeholder="гггг-мм-дд" value="<?= $model->date ?? '' ?>"/>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div id="carTypeSelectFormGroup" class="form-group mt-4 mb-4">
                            <label>Тип ТС</label>
                            <select id="carTypeSelect" class="form-control default-select2"
                                    name="Orders[car_type]">
                                <?= Html::renderSelectOptions($model->car_type, CarTypeHelper::getMap()) ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="col-lg-8">
        <div id="" class="card card-custom gutter-b">
            <div class="card-header">
                <div class="d-flex flex-wrap justify-content-between align-content-stretch w-100 mt-1">
                    <div class="card-title">
                        <div><i class="fas fa-tools text-primary mr-2"></i>Список работ</div>
                    </div>
                    <div class="card-toolbar">
                        <div class="d-flex align-items-center align-content-center">
                            <div class="form-group mb-0">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text line-height-0 py-0">
                                            <i class="fa fa-search"></i>
                                        </span>
                                    </div>
                                    <input id="serviceFindInput" type="text" class="form-control"
                                           placeholder="Поиск по услугам"
                                           aria-describedby="basic-addon2"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body" style="overflow-x: hidden;">
                <div id="autoOrderTypeBlock">
                    <ul id="autoOrderTypeSwitcher" class="nav nav-pills nav-fill">
                        <li class="nav-item">
                            <a class="nav-link  active"
                               id="home-tab-4" data-toggle="tab"
                               href="#autoOrderWashService">
                                <span class="nav-text">Мойка</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                               id="profile-tab-4" data-toggle="tab"
                               href="#autoOrderDetailService" aria-controls="profile">
                                <span class="nav-text">Дитейлинг</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content mt-5" id="myTabContent4">
                        <div class="tab-pane fade active show"
                             id="autoOrderWashService" role="tabpanel"
                             aria-labelledby="home-tab-4">
                            <div class="row mt-2 mb-4">
                                <div class="col-6">
                                    <h4>Комплексы</h4>
                                    <div class="complex-list-block wash-complex-list-block">
                                        <?php foreach ($washComplexes as $washComplex) : ?>
                                            <div class="checkbox-service-list--item"
                                                 data-title="<?= $washComplex->name ?? '' ?>" data-type="complex"
                                                 data-id="<?= $washComplex->id ?>">
                                                <div class="checkbox-service-header d-flex justify-content-between">
                                                    <div class="checkbox-service--check">
                                                        <label class="checkbox checkbox-lg checkbox-primary">
                                                            <input type="checkbox"
                                                                   name="ComplexList[<?= $washComplex->id ?>]"/>
                                                            <span class="mr-2"></span>
                                                            <?= $washComplex->name ?? '' ?>
                                                        </label>
                                                    </div>
                                                    <div class="checkbox-service--price d-flex align-items-center">
                                                        <div class="service-old-price text-muted" style="display: none">
                                                            <span>-</span>₽
                                                        </div>
                                                        <div class="service-new-price">
                                                            <span>-</span>₽
                                                        </div>
                                                        <div class="ml-2 toggle-additional-info"
                                                             data-id="<?= $washComplex->id ?>">
                                                            <i class="fas fa-caret-down text-muted"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="checkbox-service-additional-info"
                                                     data-id="<?= $washComplex->id ?>"
                                                     style="display: none;">
                                                    <ul class="mb-1">
                                                        <?php $washComplexServices = ComplexServices::find()
                                                            ->where(['complex_id' => $washComplex->id])
                                                            ->orderBy('position ASC')->all();

                                                        ?>
                                                        <?php foreach ($washComplexServices as $complexService): ?>
                                                            <li><?= $complexService->service->name ?? '' ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4>Услуги</h4>
                                    <div class="services-list-block wash-services-list-block">
                                        <?php foreach ($washServices as $washService) : ?>
                                            <div class="checkbox-service-list--item"
                                                 data-title="<?= $washService->name ?? 'Без названия' ?>"
                                                 data-type="service"
                                                 data-id="<?= $washService->id ?>">
                                                <div class="checkbox-service-header d-flex justify-content-between ">
                                                    <div class="checkbox-service--check">
                                                        <label class="checkbox checkbox-lg checkbox-primary">
                                                            <input type="checkbox"
                                                                   name="ServiceList[<?= $washService->id ?>]"/>
                                                            <span class="mr-2"></span>
                                                            <?= $washService->name ?? 'Без названия' ?>
                                                        </label>
                                                    </div>
                                                    <div class="checkbox-service--price d-flex align-items-center">
                                                        <div class="service-old-price text-muted" style="display: none">
                                                            <span>-</span>₽
                                                        </div>
                                                        <div class="service-new-price">
                                                            <span>-</span>₽
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade"
                             id="autoOrderDetailService" role="tabpanel"
                             aria-labelledby="profile-tab-4">
                            <div class="row mt-2 mb-4">
                                <div class="col-6">
                                    <h4>Комплексы</h4>
                                    <div class="complex-list-block wash-complex-list-block">
                                        <?php foreach ($detailingComplexes as $detailingComplex) : ?>
                                            <div class="checkbox-service-list--item"
                                                 data-title="<?= $detailingComplex->name ?? 'Без названия' ?>"
                                                 data-type="complex"
                                                 data-id="<?= $detailingComplex->id ?>">
                                                <div class="checkbox-service-header d-flex justify-content-between">
                                                    <div class="checkbox-service--check">
                                                        <label class="checkbox checkbox-lg checkbox-primary">
                                                            <input type="checkbox"
                                                                   name="ComplexList[<?= $detailingComplex->id ?>]"/>
                                                            <span class="mr-2"></span>
                                                            <?= $detailingComplex->name ?? 'Без названия' ?>
                                                        </label>
                                                    </div>
                                                    <div class="checkbox-service--price d-flex align-items-center">
                                                        <div class="service-old-price text-muted" style="display: none">
                                                            <span>-</span>₽

                                                        </div>
                                                        <div class="service-new-price">
                                                            <span>-</span>₽
                                                        </div>
                                                        <div class="ml-2 toggle-additional-info"
                                                             data-id="<?= $detailingComplex->id ?>">
                                                            <i class="fas fa-caret-down text-muted"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="checkbox-service-additional-info"
                                                     data-id="<?= $detailingComplex->id ?>"
                                                     style="display: none;">

                                                    <ul class="mb-1">
                                                        <?php $detailingComplexServices = ComplexServices::find()
                                                            ->where(['complex_id' => $detailingComplex->id])
                                                            ->orderBy('position ASC')->all();
                                                        ?>
                                                        <?php foreach ($detailingComplexServices as $complexService): ?>
                                                            <li><?= $complexService->service->name ?? '' ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h4>Услуги</h4>
                                    <div class="services-list-block wash-services-list-block">
                                        <?php foreach ($detailingServices as $detailingService) : ?>
                                            <div class="checkbox-service-list--item"
                                                 data-title="<?= $detailingService->name ?? 'Без названия' ?>"
                                                 data-type="service"
                                                 data-id="<?= $detailingService->id ?>">
                                                <div class="checkbox-service-header d-flex justify-content-between">
                                                    <div class="checkbox-service--check">
                                                        <label class="checkbox checkbox-lg checkbox-primary">
                                                            <input type="checkbox"
                                                                   name="ServiceList[<?= $detailingService->id ?>]"/>
                                                            <span class="mr-2"></span>
                                                            <?= $detailingService->name ?? 'Без названия' ?>
                                                        </label>
                                                    </div>
                                                    <div class="checkbox-service--price d-flex align-items-center">
                                                        <div class="service-old-price text-muted" style="display: none">
                                                            <span>-</span>₽
                                                        </div>
                                                        <div class="service-new-price">
                                                            <span>-</span>₽
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="btn btn-lg btn-primary goToSecondStep">
                            Далее
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<div id="secondStep" class="row" style="display: none">
    <div class="col-lg-4">
        <div id="" class="card card-custom gutter-b">
            <div class="card-body">
                <h6><i class="fas fa-calendar-day text-primary mr-2"></i>Дата и время записи</h6>
                <div class="row">
                    <div class="col-6">
                        <label>Дата записи</label>
                        <div class="input-group">
                            <input id="duplicateDateInput" type="text" class="form-control" disabled
                                   value="<?= $model->date ?? '' ?>"/>
                        </div>
                    </div>
                    <div class="col-6">
                        <label>Тип ТС</label>
                        <div class="input-group date-picker">
                            <input id="duplicateCarTypeInput" type="text" class="form-control" disabled
                                   value="Седан"/>
                        </div>
                    </div>

                </div>
                <div class="row mt-4">
                    <div class="col-6">
                        <label>Начало</label>
                        <div class="form-group mb-4">
                            <select id="startTimeHour" class="form-control default-select2">
                                <option value="">Часы</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <label>⠀⠀⠀</label>
                        <div class="form-group mb-4">
                            <select id="startTimeMin" class="form-control default-select2">
                                <option value="">Минуты</option>
                            </select>
                        </div>
                    </div>
                    <input id="startTimeInput" hidden name="Orders[start_time]" value="">
                </div>
                <div class="row mt-4">
                    <div class="col-6">
                        <div class="form-group mb-4">
                            <label>Номер ТС</label>
                            <?= Html::activeInput('text', $model, 'car_number', ['class' => 'form-control text-center car-number-validator',
                                'placeholder' => '', 'required' => true]) ?>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-4">
                            <label>Регион</label>
                            <?= Html::activeInput('number', $model, 'car_region', ['class' => 'form-control text-center',
                                'placeholder' => '', 'min' => '1', 'max' => '999', 'step' => '1', 'required' => true]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
    <div class="col-lg-4">
        <div id="" class="card card-custom gutter-b">
            <div class="card-body">
                <h6><i class="fas fa-comment-alt text-primary mr-2"></i>Комментарий к заказу</h6>
                <div class="row">
                    <div class="col-12">
                        <?= Html::activeTextarea($model, 'admin_comment', ['class' => 'form-control',
                            'placeholder' => '', 'required' => false]) ?>
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
                <div class="row mt-4">
                    <div class="col-12">
                        <label>Исполнитель</label>
                        <div class="form-group mb-4">
                            <select class="form-control default-select2" name="Orders[personal_id]"
                                    style="max-width: 100%">
                                <option value="">Выберите исполнителя</option>
                                <?php foreach ($personalList as $personal): ?>
                                    <?php if (!$personal->is_approved) {
                                        continue;
                                    } ?>
                                    <option value="<?= $personal->id ?>"><?= $personal->getShortUsername() ?? '' ?></option>

                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between order-total-price">
                            <div class="font-size-h5">Всего</div>
                            <div class="font-size-h5"><span id="calculatedTotalOrderPrice">0</span>Р.</div>
                        </div>
                        <div class="d-flex justify-content-between order-sale-price mt-2">
                            <div class="font-size-h6">Скидка</div>
                            <div class="font-size-h6"><span id="calculatedOrderSale">0</span>Р.</div>
                        </div>
                        <input id="orderSaleInput" hidden name="Orders[sale]" value="0">
                        <input id="orderWorkTimeInput" hidden name="Orders[work_time]" value="0">

                        <div id="complexesInputs">

                        </div>
                        <div id="servicesInputs">

                        </div>

                        <div class="mt-4 row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-content-center align-items-center">
                                    <div class="mr-4 d-flex align-items-center  font-size-h2">
                                        <i class="fas fa-receipt text-primary fa-1x mr-2"></i>
                                        Итого
                                    </div>
                                    <?= Html::activeInput('number', $model, 'total_price', ['id' => 'orderTotalPrice', 'class' => 'form-control text-center',
                                        'placeholder' => '0', 'required' => true, 'value' => '0', 'min' => 0, 'max' => '999999',
                                        'step' => '1', 'data' => ['is-touched' => '0']]) ?>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                            <div class="col-4">
                                <div class="btn btn-lg btn-outline-primary px-2 goToFirstStep">
                                    Назад
                                </div>
                            </div>
                            <div class="col-8">
                                <button type="submit" class="btn btn-lg btn-primary w-100">
                                    Создать
                                </button>
                            </div>
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
        let orderServices = [];
        let orderComplexes = [];
        let isWorkingDate = true;
        getPrices();
        checkIsWorkDate();

        $('#serviceFindInput').on('input', function () {
            let val = ($(this).val() + "");
            if (val.length > 0) {
                val = val.toLowerCase();
                $.each($('.checkbox-service-list--item'), function (index, item) {
                    let name = ($(item).data('title') + "").toLowerCase();
                    if ((name + '').indexOf(val) != -1) {
                        $(item).show();
                    } else {
                        $(item).hide();
                    }
                });
            } else {
                $.each($('.checkbox-service-list--item'), function (index, item) {
                    $(item).show();
                });
            }
        });

        $('.create-order-button').on('click', function () {
            if ($('#createOrderForm')[0].checkValidity()) {
                $('#createOrderForm').submit();
            } else {
                $('#createOrderForm')[0].reportValidity();
            }
        });

        $('.order-start-time-picker').timepicker({
            minuteStep: 5,
            showSeconds: false,
            showMeridian: false,
            snapToStep: true
        });

        $('.checkbox-service--check input').on('change', function () {
            orderServices = [];
            orderComplexes = [];
            $('#complexesInputs').empty();
            $('#servicesInputs').empty();
            let serviceList = $('.checkbox-service--check input:checked');
            $.each(serviceList, function (i, item) {
                let id = $(item).closest('.checkbox-service-list--item').data('id');
                let type = $(item).closest('.checkbox-service-list--item').data('type');
                if (type == 'service') {
                    orderServices.push(id);
                    $('<input multiple hidden name="ServicesList[' + id + ']" value="' + id + '">').appendTo('#servicesInputs');
                } else {
                    orderComplexes.push(id);
                    $('<input multiple hidden name="ComplexesList[' + id + ']" value="' + id + '">').appendTo('#complexesInputs');
                }
            });

            liveCalculatePrice();
        });

        $('.toggle-additional-info').click(function () {
            let additionalInfoId = $(this).data('id');
            $('.checkbox-service-additional-info[data-id="' + additionalInfoId + '"]').toggle(100);
        });

        $('.goToSecondStep').click(function () {
            if (!isWorkingDate){
                toastr.error("Выбран нерабочий день. Пожалуйста, измените дату записи");
                return false;
            }
            if (orderComplexes.length === 0 && orderServices.length === 0){
                toastr.error("Не выбраны услуги для создания записи");
                return false;
            }
            loadStartHours();
            $('#firstStep').hide();
            $('#secondStep').show(100);
            $('#firstStepHeaderTab').find('.header-tab-subtitle').removeClass('text-primary').addClass('text-muted');
            $('#secondStepHeaderTab').find('.header-tab-subtitle').addClass('text-primary').removeClass('text-muted');
        });

        $('.goToFirstStep').click(function () {
            $('#secondStep').hide();
            $('#firstStep').show(100);
            $('#firstStepHeaderTab').find('.header-tab-subtitle').removeClass('text-muted').addClass('text-primary');
            $('#secondStepHeaderTab').find('.header-tab-subtitle').addClass('text-muted').removeClass('text-primary');
        });

        $('#orderStartDateInput').on('change', function () {
            $('#duplicateDateInput').val($(this).val());
            checkIsWorkDate();
        });

        $('#carTypeSelect').on('change', function () {
            let carTypeText = $('#carTypeSelectFormGroup').find('#select2-carTypeSelect-container').text();
            $('#duplicateCarTypeInput').val(carTypeText);
            getPrices();
            liveCalculatePrice();
        });

        $('#startTimeHour').on('change', function (e) {
            let totalWorkTime = $('#orderWorkTimeInput').val();
            let date = $('#orderStartDateInput').val();
            let hourValue = $(this).val();
            $.ajax({
                type: 'POST',
                url: '/ajax/orders/get-free-times',
                data: {cwId: cwId, pId: pId, workTimeMin: totalWorkTime, date: date},
                success: function (data) {
                    console.log(data);
                    if (!data.result) {
                        toastr.success("Не удалось получить информацию о доступном времени записи");
                    } else {
                        $('#startTimeMin').empty();
                        console.log(data);
                        $('#startTimeMin').append(
                            '<option value="">Минуты</option>'
                        );

                        $.each(data.items, function (hour, minutesArray) {
                            if (hourValue == hour) {
                                $.each(minutesArray, function (index, minute) {
                                    $('#startTimeMin').append(
                                        '<option value="' + minute + '">' + minute + '</option>'
                                    );
                                });
                            }
                        });
                    }
                }
            });
        });

        $('#startTimeMin').on('change', function () {
            let hourValue = $('#startTimeHour').val();
            let minuteValue = $('#startTimeMin').val();
            $('#startTimeInput').val(hourValue + ':' + minuteValue);
        });

        function checkIsWorkDate() {
            let date = $('#orderStartDateInput').val();

            $.ajax({
                type: 'POST',
                url: '/ajax/carwash/is-working-day',
                data: {cwId: cwId, date: date},
                success: function (data) {
                    isWorkingDate = data.result;
                    if(!data.result){
                        $('#orderStartDateInput').addClass('border-danger');
                        toastr.error("Дата: " + date + " указана в расписании как нерабочий день. Пожалуйста, измените дату записи");
                    } else {
                        $('#orderStartDateInput').removeClass('border-danger');
                    }
                }
            })
        }

        function loadStartHours() {
            let totalWorkTime = $('#orderWorkTimeInput').val() ?? 0;
            let date = $('#orderStartDateInput').val();

            $.ajax({
                type: 'POST',
                url: '/ajax/orders/get-free-times',
                data: {cwId: cwId, pId: pId, workTimeMin: totalWorkTime, date: date},
                success: function (data) {
                    console.log(data);
                    if (!data.result) {
                        toastr.success("Не удалось получить информацию о доступном времени записи");
                    } else {
                        $('#startTimeHour').empty();
                        console.log(data);
                        $('#startTimeHour').append(
                            '<option value="">Часы</option>'
                        );
                        if (data.items.length == 0) {
                            toastr.error("Нет доступных промежутков записи на выбранный день");
                        } else {
                            let items = [];

                            // Поперли убийственные сортировки времени 00,01, ... 10,11,12, ... 23
                            Object.keys(data.items).sort().forEach(function (hourKey, key) {
                                items[hourKey] = data.items[hourKey];
                            });

                            $(items).each(function (hour) {
                                let newHour = "" + hour;
                                if (newHour.length == 1) {
                                    newHour = 0 + "" + hour;
                                }
                                let minutesArray = items[newHour];
                                if (minutesArray) {
                                    $('#startTimeHour').append(
                                        '<option value="' + newHour + '">' + newHour + '</option>'
                                    );
                                }
                            });
                        }

                    }
                }
            });
        }


        function liveCalculatePrice() {
            let carType = $('#carTypeSelect').val();
            $('.selected-order-service-list').empty();
            console.log(orderServices);
            console.log(orderComplexes);

            $.ajax({
                type: 'POST',
                url: '/ajax/orders/calc-live-order-price',
                data: {cwId: cwId, carType: carType, servicesIds: orderServices, complexesIds: orderComplexes},
                success: function (data) {
                    console.log(data);
                    if (!data.result) {
                        toastr.success("Не удалось рассчитать предварительную стоимость для этого заказа. Заполните поле итоговой цены самостоятельно");
                    } else {
                        console.log(data);
                        $.each(data.items, function (i, item) {
                            let serviceName = item.name;
                            let servicePrice = item.price;
                            let html = '<div class="selected-order-service--item d-flex justify-content-between">' +
                                '<div>' + serviceName + '</div>' +
                                '<div>' + servicePrice + 'Р.</div>' +
                                '</div>';

                            $('.selected-order-service-list').append(html);
                        });
                        let orderPrice = data.totalPrice;
                        let orderWorkTime = data.workTime;
                        let orderWorkTimeWithMultiplier = data.workTimeWithMultiplier;
                        let orderSale = data.sale;
                        let orderSaleName = data.saleName;

                        // if (parseInt(orderSale) > 0){
                        //     toastr.success("Применена скидка: " + orderSaleName);
                        // }
                        $('.headTotalWorkTime').text(orderWorkTimeWithMultiplier);
                        $('#calculatedTotalOrderPrice').text(orderPrice);
                        $('#calculatedOrderSale').text(orderSale);
                        $('#orderSaleInput').val(orderSale);
                        $('#orderWorkTimeInput').val(orderWorkTimeWithMultiplier);

                        let orderPriceTouched = $('#orderTotalPrice').data('is-touched');
                        if (orderPriceTouched == 0) {
                            $('.headTotalOrderPrice').text(orderPrice);
                            $('#orderTotalPrice').val(orderPrice);
                        }
                    }
                }
            });
        };

        $('#orderTotalPrice').on('change', function () {
            $(this).data('is-touched', 1);
            if ($('#orderTotalPrice').val() === '' || $('#orderTotalPrice').val() === undefined) {
                $(this).val(0);
            }
            $('.headTotalOrderPrice').text($(this).val());
        })

        function getPrices() {
            let carType = $('#carTypeSelect').val();
            $('.selected-order-service-list').empty();

            $.ajax({
                type: 'POST',
                url: '/ajax/orders/get-prices',
                data: {cwId: cwId, carType: carType},
                success: function (data) {
                    if (!data.result) {
                        toastr.success("Не удалось получить информацию о стоимости ваших услуг. Обратитесь в поддержку");
                    } else {
                        console.log(data);
                        $.each(data.items, function (i, item) {
                            let id = item.id;
                            let serviceName = item.name;
                            let servicePrice = item.price;
                            let type = item.type;
                            $('.checkbox-service-list--item[data-type="' + type + '"][data-id="' + id + '"]').find('.service-new-price').text(servicePrice);
                        });
                    }
                }
            });
        }

    });
</script>
