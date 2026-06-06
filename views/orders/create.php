<?php

use app\commons\helpers\CarTypeHelper;
use app\models\ar\car\CarBrands;
use app\models\ar\car\CarModels;
use app\models\ar\complex\Complexes;
use app\models\ar\order\Orders;
use app\models\ar\personal\Personal;
use app\models\ar\service\Services;
use yii\helpers\ArrayHelper;
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

$this->title = 'Создание записи';
$this->params['header_button'] = '<div class="d-flex justify-content-between">
<div class="d-block mr-8">
     <div>СТОИМОСТЬ</div>
        <div class="text-primary font-weight-bold font-size-h4 d-flex"><div class="headTotalOrderPrice">0</div> ₽</div>
    </div>
    <div class="d-block mr-12">
        <div>ВРЕМЯ РАБОТ</div>
        <div class="text-primary font-weight-bold font-size-h4 d-flex"><div class="headTotalWorkTime">00</div> мин</div>
    </div>
<div class="d-block">
     <button class="btn btn-primary px-6 create-order-button">Создать запись</button>
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
                        <div class="input-group date-picker">
                            <input id="dateInput" type="text" name="Orders[date]" class="form-control" readonly
                                   placeholder="гггг-мм-дд" value="<?= $model->date ?? '' ?>"/>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-12 col-xl-6">
                        <label>Номер поста</label>
                        <div class="form-group mb-4">
                            <select class="form-control default-select2" name="Orders[post]">
                                <?php foreach (range(1, $postCounts) as $postId) : ?>
                                    <?php if (!empty($model->post) && $model->post == $postId): ?>
                                        <option selected value="<?= $postId ?>"><?= $postId ?></option>
                                    <?php else : ?>
                                        <option value="<?= $postId ?>"><?= $postId ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 col-lg-12 col-xl-6">
                        <label>Начало*</label>
                        <div class="input-group ">
                            <input type="text" name="Orders[start_time]"
                                   class="form-control order-start-time-picker"
                                   readonly id="startTimeInput"
                                   autocomplete="false" placeholder="чч:мм"
                                   value="<?= $model->start_time ?? '' ?>"/>
                            <div class="input-group-append">
                                <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-12 col-xl-6">
                        <div class="form-group mb-4">
                            <label>Окончание</label>
                            <div class="input-group">
                                <input type="text" name="Orders[end_time]"
                                       class="form-control order-end-time-picker" readonly
                                       autocomplete="false" placeholder="чч:мм" id="endTimeInput"
                                       value="<?= $model->end_time ?? '' ?>"/>
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="la la-calendar-check-o"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div id="freeTimeIntervals">

                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div id="" class="card card-custom gutter-b">
            <div class="card-body">
                <h6><i class="fas fa-car text-primary mr-2"></i>Автомобиль</h6>
                <div class="row">
                    <div class="col-12">
                        <label>Тип ТС</label>
                        <div class="form-group mb-4">
                            <select id="carTypeSelect" class="form-control default-select2"
                                    name="Orders[car_type]">
                                <?= Html::renderSelectOptions($model->car_type, CarTypeHelper::getMap()) ?>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="row mt-4">
                    <div class="col-4 col-lg-6 col-xl-4">
                        <div class="form-group mb-4">
                            <label>Номер ТС</label>
                            <?= Html::activeInput('text', $model, 'car_number', ['class' => 'form-control text-center car-number-validator',
                                'placeholder' => '', 'required' => true]) ?>
                        </div>
                    </div>
                    <div class="col-3 col-lg-6 col-xl-3">
                        <div class="form-group mb-4">
                            <label>Регион</label>
                            <?= Html::activeInput('number', $model, 'car_region', ['class' => 'form-control text-center',
                                'placeholder' => '', 'min' => '1', 'max' => '999', 'step' => '1', 'required' => true]) ?>
                        </div>
                    </div>
                    <div class="col-5 col-lg-12 col-xl-5">
                        <div class="typeahead mb-4">
                            <label>Цвет</label>
                            <?= Html::activeInput('text', $model, 'color', ['class' => 'form-control',
                                'placeholder' => '', 'required' => false]) ?>
                        </div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-6">
                        <label>Марка</label>
                        <div class="form-group mb-4">
                            <select id="carBrandSelector" class="form-control default-select2 w-100"
                                    name="Orders[car_brand_id]">
                                <?= Html::renderSelectOptions($model->car_brand_id, ArrayHelper::map($carBrands, 'id', 'title')) ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <label>Модель</label>
                        <div class="form-group mb-4">
                            <select id="carModelSelector" class="form-control default-select2"
                                    name="Orders[car_model_id]">
                                <?= Html::renderSelectOptions($model->car_model_id, ArrayHelper::map($carModels, 'id', 'title')) ?>
                            </select>
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
                                                <select id="washComplexSelect"
                                                        class="form-control default-select2 order-complex-select"
                                                        multiple
                                                        name="ComplexesList[]">
                                                    <?php foreach ($washComplexes as $washComplex) : ?>
                                                        <option <?= in_array($washComplex->id, $selectedWashComplexes) ? 'selected' : '' ?>
                                                                value="<?= $washComplex->id ?>"><?= $washComplex->name ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="wash-service-selector-block">
                                            <label>Услуги</label>
                                            <div class="form-group mb-4">
                                                <select id="washServiceSelect"
                                                        class="form-control default-select2 order-service-select"
                                                        multiple
                                                        name="ServicesList[]">
                                                    <?php foreach ($washServices as $washService) : ?>
                                                        <option <?= in_array($washService->id, $selectedWashServices) ? 'selected' : '' ?>
                                                                value="<?= $washService->id ?>"><?= $washService->name ?></option>
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
                                                <select id="detailingComplexSelect"
                                                        class="form-control default-select2 order-complex-select"
                                                        multiple
                                                        name="ComplexesList[]">
                                                    <?php foreach ($detailingComplexes as $detailingComplex) : ?>
                                                        <option <?= in_array($detailingComplex->id, $selectedDetailingComplexes) ? 'selected' : '' ?>
                                                                value="<?= $detailingComplex->id ?>"><?= $detailingComplex->name ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="detail-service-selector-block">
                                            <label>Услуги</label>
                                            <div class="form-group mb-4">
                                                <select id="detailingServiceSelect"
                                                        class="form-control default-select2 order-service-select"
                                                        multiple
                                                        name="ServicesList[]">
                                                    <?php foreach ($detailingServices as $detailingService) : ?>
                                                        <option <?= in_array($detailingService->id, $selectedDetailingServices) ? 'selected' : '' ?>
                                                                value="<?= $detailingService->id ?>"><?= $detailingService->name ?></option>
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
                <div class="row mt-6">
                    <div class="col-12">
                        <label>Исполнитель</label>
                        <div class="form-group mb-4">
                            <select class="form-control default-select2" name="Orders[personal_id]">
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
                        <div class="mt-4 row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-content-center align-items-center">
                                    <div class="mr-4 d-flex">
                                        <i class="fas fa-receipt text-primary mr-2"></i>
                                        Итого
                                    </div>
                                    <?= Html::activeInput('number', $model, 'total_price', ['id' => 'orderTotalPrice', 'class' => 'form-control text-center',
                                        'placeholder' => '0', 'required' => true, 'value' => '0', 'min' => '0',
                                        'max' => '9999999', 'step' => '1', 'data' => ['is-touched' => '0']]) ?>
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
                            <?= Html::activeInput('text', $model, 'client_fullname', ['class' => 'form-control',
                                'placeholder' => 'Иванов Иван Иванович', 'required' => false]) ?>
                        </div>
                        <div class="form-group mb-4">
                            <label>Телефон</label>
                            <?= Html::activeInput('text', $model, 'client_phone', ['class' => 'form-control',
                                'placeholder' => '+7 (___) ___-__-__', 'required' => false]) ?>
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
        let selectedPost = $('[name="Orders[post]"]').val();
        let orderServices = [];
        let orderComplexes = [];


        loadFreeIntervals();
        checkIsWorkDate();

        $('#carBrandSelector').on('change', function () {
            let brandId = $(this).val();

            $.ajax({
                type: 'GET',
                url: '/ajax/common/get-car-models-by-brand',
                data: {brandId: brandId, carwashId: cwId},
                success: function (data) {
                    if (!data.result) {
                        toastr.success("Не удалось получить список моделей автомобилей");
                    } else {
                        $("#carModelSelector").empty();

                        $('#carModelSelector').select2({
                            data: data.items,
                        });
                    }
                }
            });
        });

        $('.create-order-button').on('click', function () {
            if ($('#createOrderForm')[0].checkValidity()) {
                let startTime = $('#startTimeInput').val();
                let endTime = $('#endTimeInput').val();
                if (startTime === endTime) {
                    toastr.warning("Время начала и окончания не может быть равно");
                    return false;
                }
                $('#createOrderForm').submit();
            } else {
                $('#createOrderForm')[0].reportValidity();
            }
        });


        $('.order-complex-select').on('change', function (e) {
            orderComplexes = [];
            $('.order-complex-select').each(function () {
                let values = $(this).val();
                orderComplexes = [...values, ...orderComplexes];
            });
            console.log('Комплексы: ');
            console.log(orderComplexes);
            liveCalculatePrice();
        });

        $('.order-service-select').on('change', function (e) {
            orderServices = [];
            $('.order-service-select').each(function () {
                let values = $(this).val();
                orderServices = [...values, ...orderServices];
            });
            console.log('Услуги: ');
            console.log(orderServices);
            liveCalculatePrice();
        });

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
                        let orderSale = data.sale ?? 0;
                        let orderSaleName = data.saleName;

                        if (parseInt(orderSale) > 0) {
                            toastr.success("Применена скидка: " + orderSaleName);
                        }
                        $('.headTotalWorkTime').text(orderWorkTimeWithMultiplier);
                        $('#calculatedTotalOrderPrice').text(parseInt(orderPrice + orderSale));
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
        }

        function checkIsWorkDate() {
            let date = $('#dateInput').val();

            $.ajax({
                type: 'POST',
                url: '/ajax/carwash/is-working-day',
                data: {cwId: cwId, date: date},
                success: function (data) {
                    isWorkingDate = data.result;
                    if (!data.result) {
                        $('#dateInput').addClass('border-danger');
                        toastr.error("Дата: " + date + " указана в расписании как нерабочий день. Пожалуйста, измените дату записи");
                    } else {
                        $('#dateInput').removeClass('border-danger');
                    }
                }
            })
        }

        $('#carTypeSelect').on('change', function () {
            liveCalculatePrice();
        })

        $('#orderTotalPrice').on('change', function () {
            $(this).data('is-touched', 1);
            $('.headTotalOrderPrice').text($(this).val());
        })

        $('.order-start-time-picker').timepicker({
            minuteStep: 5,
            showSeconds: false,
            showMeridian: false,
            snapToStep: true
        });

        $('.order-end-time-picker').timepicker({
            minuteStep: 5,
            showSeconds: false,
            showMeridian: false,
            snapToStep: true
        });

        function loadFreeIntervals() {
            let date = $('#dateInput').val();
            let startAt = $('#startTimeInput').val();
            let endAt = $('#endTimeInput').val();
            let workTime = $('.headTotalWorkTime').val();
            if (!startAt) {
                startAt = '00:00:00';
            } else {
                startAt = new Date("01.01.1970 " + startAt).toLocaleTimeString();
            }
            if (!endAt) {
                endAt = '23:59:00';
            } else {
                endAt = new Date("01.01.1970 " + endAt).toLocaleTimeString();
            }
            if (!workTime) {
                workTime = 0;
            }

            console.log(startAt);
            console.log(endAt);
            console.log(workTime);

            $.ajax({
                type: 'POST',
                url: '/ajax/orders/get-free-time-intervals',
                data: {
                    cwId: cwId,
                    date: date,
                    startAt: startAt,
                    endAt: endAt,
                    workTimeMin: workTime,
                    post: selectedPost
                },
                success: function (data) {
                    console.log(data);
                    if (data.result) {
                        $('#freeTimeIntervals').empty();

                        $.each(data.intervals, function (i, item) {
                            let interval = item;
                            let html = '<span class="label label-dark-2 label-inline mr-2">' + interval + '</span>';

                            $('#freeTimeIntervals').append(html);
                        });
                    } else {
                        console.log('Не удалось получить список свободных интервалов для блока времени');
                    }
                }
            });
        }

        $("#orders-client_phone").inputmask("mask", {
            "mask": "(999) 999-9999"
        });


        var defaultColorList = ['бежевый', 'желтый', 'красный', 'фиолетовый', 'зеленый', 'оранжевый', 'черный',
            'белый', 'синий', 'голубой', 'серый', 'серебристый'];
        var substringMatcher = function (strs) {
            return function findMatches(q, cb) {
                var matches, substringRegex;

                // an array that will be populated with substring matches
                matches = [];

                // regex used to determine if a string contains the substring `q`
                substrRegex = new RegExp(q, 'i');

                // iterate through the pool of strings and for any string that
                // contains the substring `q`, add it to the `matches` array
                $.each(strs, function (i, str) {
                    if (substrRegex.test(str)) {
                        matches.push(str);
                    }
                });

                cb(matches);
            };
        };
        $('#orders-color').typeahead({hint: true, highlight: true, minLength: 1},
            {
                name: 'defaultColorList',
                source: substringMatcher(defaultColorList)
            }
        );
        $('[name="Orders[post]"]').on('change', function () {
            selectedPost = $(this).val();
            loadFreeIntervals();
        })

        $('#dateInput').on('change', function () {
            loadFreeIntervals();
            checkIsWorkDate();
        })
    })
</script>