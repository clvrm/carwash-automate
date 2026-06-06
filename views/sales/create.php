<?php

use app\models\ar\carwash\CarwashSales;
use app\models\ar\complex\Complexes;
use app\models\ar\service\Services;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = (!empty($sale->id) ? 'Обновление ' : 'Создание') . ' скидки';
$this->params['mobileFixedMenuBackUrl'] = '/sales/';


/**
 * @var CarwashSales $sale
 * @var Complexes $complexList
 * @var Services $serviceList
 * @var array $selectedServices
 * @var array $selectedComplexes
 */
?>

<?php $form = \yii\widgets\ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>
<div class="row">
    <?php $errors = $form->errorSummary([$sale]);
    if (!empty($sale->errors)) : ?>
        <div class="col-12">
            <div class="alert alert-danger" role="alert">
                <?= $errors ?? '' ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="col-lg-12">
        <div id="personalCreate" class="card card-custom gutter-b">
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-5">
                        <h5 class="mt-6">Общие параметры</h5>
                        <div class="mt-4 form-group">
                            <label>Название скидки</label>
                            <?= Html::activeInput('text', $sale, 'name', ['class' => 'form-control',
                                'placeholder' => 'Введите название скидки', 'required' => true]) ?>
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <label>Начало</label>
                                <div class="input-group date-picker">
                                    <input type="text" name="CarwashSales[start_at]" class="form-control" readonly
                                           placeholder="гггг-мм-дд" value="<?= $sale->start_at ?? '' ?>"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-calendar-check-o"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <label>Окончание</label>
                                <div class="input-group date-picker">
                                    <input type="text" name="CarwashSales[end_at]" class="form-control" readonly
                                           placeholder="гггг-мм-дд" value="<?= $sale->end_at ?? '' ?>"/>
                                    <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-calendar-check-o"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mt-4 form-group">
                                    <label>Описание скидки</label>
                                    <?= Html::activeTextarea($sale, 'description', ['rows' => 4, 'class' => 'form-control']) ?>
                                </div>

                                <h5 class="mt-8">Аудитория</h5>
                                <div class="form-group mb-4">
                                    <label>Для кого будет действовать скидка</label>
                                    <select class="form-control default-select2-dropdown" name="CarwashSales[only_subscribers]">
                                        <?= Html::renderSelectOptions($sale->only_subscribers, [
                                            CarwashSales::SALE_FOR_ALL => 'Для всех',
                                            CarwashSales::SALE_FOR_SUBSCRIBER => 'Для подписчиков',
                                        ]) ?>
                                    </select>
                                </div>

                                <h5 class="mt-8">Условия и ограничения</h5>
                                <div class="form-group mb-4">
                                    <label>На что будет действовать скидка</label>
                                    <select id="saleForServiceTypeSelector" class="form-control default-select2-dropdown"
                                            name="CarwashSales[for_service_type]">
                                        <?= Html::renderSelectOptions($sale->for_service_type, [
                                            CarwashSales::SERVICE_TYPE_COMPLEX => 'Комплекс',
                                            CarwashSales::SERVICE_TYPE_SERVICE => 'Услуга',
                                        ]) ?>
                                    </select>
                                </div>
                                <div id="complexFormGroup"
                                     class="form-group" <?= ($sale->for_service_type !== CarwashSales::SERVICE_TYPE_COMPLEX and $sale->for_service_type === 0) ? 'style="display:none"' : '' ?>>
                                    <label>Выберите комплекс</label>
                                    <select class="form-control default-select2" multiple name="complexesList[]">
                                        <?php foreach ($complexList as $complex) : ?>
                                            <option <?= in_array($complex->id, $selectedComplexes) ? 'selected' : '' ?>
                                                    value="<?= $complex->id ?>"><?= $complex->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div id="serviceFormGroup"
                                     class="form-group" <?= $sale->for_service_type !== CarwashSales::SERVICE_TYPE_SERVICE ? 'style="display:none"' : '' ?>>
                                    <label>Выберите услуги</label>
                                    <select class="form-control default-select2" multiple name="servicesList[]">
                                        <?php foreach ($serviceList as $service) : ?>
                                            <option <?= in_array($service->id, $selectedServices) ? 'selected' : '' ?>
                                                    value="<?= $service->id ?>"><?= $service->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="offset-lg-1 col-lg-6">
                        <h5 class="mt-6">Сумма и вид скидки</h5>
                        <div class="form-group mt-4">
                            <label>Вид скидки</label>
                            <select class="form-control default-select2-dropdown" name="CarwashSales[sale_type]">
                                <?= Html::renderSelectOptions($sale->sale_type, [
                                    CarwashSales::SALE_TYPE_PERCENT => 'Процент от суммы',
                                    CarwashSales::SALE_TYPE_PRICE => 'Скидка в рублях от суммы',
                                ]) ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label id="saleSumLabel">Сумма
                                <?php if ($sale->sale_type): ?>
                                    <?php if ($sale->sale_type == CarwashSales::SALE_TYPE_PERCENT): ?>
                                        в процентах, %
                                    <?php else: ?>
                                        в рублях, руб.
                                    <?php endif; ?>
                                <?php endif; ?>
                            </label>
                            <?= Html::activeInput('number', $sale, 'sale', ['class' => 'form-control text-center',
                                'placeholder' => '', 'required' => true]) ?>
                        </div>
                        <div class="form-group">
                            <label>Округление</label>
                            <select class="form-control default-select2-dropdown" name="CarwashSales[rounding_to]">
                                <?= Html::renderSelectOptions($sale->rounding_to, [
                                    CarwashSales::ROUND_NONE => 'Нет',
                                    10 => 'до 10 Руб.',
                                    50 => 'до 50 Руб.',
                                    100 => 'до 100 Руб.',
                                ]) ?>
                            </select>
                        </div>
                        <h5 class="mt-10">Дополнительно</h5>
                        <div id="sumUpDiscountBlock" class="form-group mt-4">
                            <label>Суммировать эту скидку с остальными?</label>
                            <div class="radio-inline">
                                <label class="radio">
                                    <input type="radio" <?= (isset($sale->sum_up_discount) and $sale->sum_up_discount == 1) ? 'checked' : '' ?>
                                           value="1" name="CarwashSales[sum_up_discount]"/>
                                    <span></span>
                                    Да
                                </label>
                                <label class="radio">
                                    <input type="radio" <?= (isset($sale->sum_up_discount) and $sale->sum_up_discount === 0) ? 'checked' : '' ?>
                                           value="0" name="CarwashSales[sum_up_discount]"/>
                                    <span></span>
                                    Нет
                                </label>
                            </div>
                        </div>
                        <div id="applyGreaterBlock" class="form-group mb-4"
                             style="<?= $sale->sum_up_discount == 1 ? 'display:none' : '' ?>">
                            <label>Какую скидку применять?</label>
                            <div class="radio-inline">
                                <label class="radio">
                                    <input type="radio" <?= (isset($sale->apply_greater) and $sale->apply_greater == 1) ? 'checked' : '' ?>
                                           value="1" name="CarwashSales[apply_greater]"/>
                                    <span></span>
                                    Большую
                                </label>
                                <label class="radio">
                                    <input type="radio" <?= (isset($sale->apply_greater) and $sale->apply_greater === 0) ? 'checked' : '' ?>
                                           value="0" name="CarwashSales[apply_greater]"/>
                                    <span></span>
                                    Меньшую
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="text-right">
                            <a href="/sales/">
                                <button type="button" class="btn btn-lg btn-outline-primary mr-2">Закрыть</button>
                            </a>
                            <button type="submit"
                                    class="btn btn-lg btn-primary"><?= !empty($sale->id) ? 'Обновить ' : 'Создать' ?></button>
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
        let saleType = $('[name="CarwashSales[sale_type]"]').val();


        changeSaleSumLabel();

        $('#saleForServiceTypeSelector').on('change', function () {
            if ($(this).val() == 0) {
                $('#complexFormGroup').hide(200);
                $('#serviceFormGroup').show(200);
                $('#serviceFormGroup .default-select2').select2({});
            } else {
                $('#serviceFormGroup').hide(200);
                $('#complexFormGroup').show(200);
                $('#complexFormGroup .default-select2').select2({});

            }
        });

        $("#sumUpDiscountBlock input[type='radio']").on('input', function () {
            let currentValue = $(this).val();
            if (currentValue == 1) {
                $('#applyGreaterBlock').hide();
            } else {
                $('#applyGreaterBlock').show();
            }
        })

        $('[name="CarwashSales[sale_type]"]').on('change', function (){
            saleType = $(this).val();
            changeSaleSumLabel();
        });

        function changeSaleSumLabel(){
            if (saleType == 0){
                $('#saleSumLabel').text('Сумма в процентах, %');
                $('#carwashsales-sale').attr('max', 100);
                $('#carwashsales-sale').attr('min', 1);
                $('#carwashsales-sale').attr('step', 1);
                $('#carwashsales-sale').attr('type', 'number');
            } else if(saleType == 1){
                $('#saleSumLabel').text('Сумма в рублях, руб.');
                $('#carwashsales-sale').attr('max', 100000);
                $('#carwashsales-sale').attr('min', 1);
                $('#carwashsales-sale').attr('step', 1);
                $('#carwashsales-sale').attr('type', 'number');
            }
        }
    });
</script>