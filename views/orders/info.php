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
 * @var CarwashSales[] $activeSales
 *
 * @var Complexes $selectedWashComplexes
 * @var Complexes $selectedDetailingComplexes
 * @var Services $selectedWashServices
 * @var Services $selectedDetailingServices
 */


$this->title = 'Информация';
$this->params['header_button'] = '';

?>

<?php $form = \yii\widgets\ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>

<div class="row">
    <div class="col-lg-4">
        <div class="card card-custom gutter-b">
            <div class="card-body">
                <h6><i class="fas fa-calendar-day text-primary mr-2"></i>Тип ТС</h6>
                <div class="row mt-6">
                    <div class="col-lg-12">
                        <div id="carTypeSelectFormGroup" class="form-group mt-4 mb-4">
                            <label>Тип ТС</label>
                            <select id="carTypeSelect" class="form-control default-select2"
                                    name="CarwashSales[only_subscribers]">
                                <?= Html::renderSelectOptions($model->car_type, CarTypeHelper::getMap()) ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card card-custom gutter-b">
            <div class="card-body">
                <h6><i class="fas fa-calendar-day text-primary mr-2"></i>Скидки и акции</h6>
                <div class="row mt-6">
                    <div class="col-lg-12">
                        <?php foreach ($activeSales as $sale): ?>
                            <div class="info-sale-block-wrapper">
                                <div class="info-sale-block d-flex justify-content-between info-sale-toggle"
                                     data-id="<?= $sale->id ?>">
                                    <div class="info-sale-name">
                                        <?= $sale->name ?? 'Без названия' ?>
                                    </div>
                                    <div>
                                        <i class="fas fa-caret-down text-muted"></i>
                                    </div>
                                </div>
                                <div class="sale-additional-info" style="display: none;" data-id="<?= $sale->id ?>">
                                    <?php if ($sale->description): ?>
                                        <div class="sale-additional-info-description">
                                            <strong>Описание:</strong>
                                            <?= $sale->description ?? '---' ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($saleItems = $sale->getCarwashSalesItems()->all())): ?>
                                        <div class="sale-additional-info-include"><strong>Действует на:</strong>
                                            <?php foreach ($saleItems as $saleItem): ?>
                                                <?php if (!empty($saleItem->complex)): ?>
                                                    <span><?= $saleItem->complex->name ?? '--' ?>;</span>
                                                <?php elseif (!empty($saleItem->service)): ?>
                                                    <span><?= $saleItem->service->name ?? '--' ?>;</span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="sale-additional-info-price">
                                        <strong>Скидка:</strong>
                                        <?php if ($sale->sale_type == CarwashSales::SALE_TYPE_PERCENT): ?>
                                            <?= $sale->sale ?? 'Не указана' ?>%
                                        <?php elseif ($sale->sale_type == CarwashSales::SALE_TYPE_PRICE): ?>
                                            <?= $sale->sale ?? 'Не указана' ?> руб.
                                        <?php endif; ?>
                                    </div>
                                    <div class="sale-additional-info-auditory">
                                        <strong>Аудитория:</strong>
                                        <?php if ($sale->only_subscribers == true): ?>
                                            Только для подписчиков
                                        <?php else: ?>
                                            Для всех
                                        <?php endif; ?>
                                    </div>
                                    <div class="sale-additional-info-price">
                                        <strong>Округление скидки:</strong>
                                        <?php if ($sale->rounding_to == CarwashSales::ROUND_NONE): ?>
                                            Нет
                                        <?php elseif ($sale->rounding_to > 0): ?>
                                            <?= $sale->rounding_to ?? '' ?> руб.
                                        <?php else: ?>
                                            Не используется
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
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
                                <div class="input-group" style="display: none">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text line-height-0 py-0">
                                            <i class="fa fa-search"></i>
                                        </span>
                                    </div>
                                    <input id="serviceFindInput" type="text" class="form-control"
                                           placeholder="Начните вводить название работы"
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
                                            <div class="checkbox-service-list--item" data-type="complex"
                                                 data-id="<?= $washComplex->id ?>">
                                                <div class="checkbox-service-header d-flex justify-content-between">
                                                    <div class="checkbox-service--check">
                                                        <label class="checkbox checkbox-lg checkbox-primary">
<!--                                                            <input type="checkbox" name="Checkboxes16"/>-->
<!--                                                            <span class="mr-2"></span>-->
                                                            <?= $washComplex->name ?? '' ?>
                                                        </label>
                                                    </div>
                                                    <div class="checkbox-service--price d-flex align-items-center">
                                                        <!--                                                        <div class="service-old-price text-muted">-->
                                                        <!--                                                            <span>-</span>₽-->
                                                        <!--                                                        </div>-->
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
                                            <div class="checkbox-service-list--item" data-type="service"
                                                 data-id="<?= $washService->id ?>">
                                                <div class="checkbox-service-header d-flex justify-content-between">
                                                    <div class="checkbox-service--check">
                                                        <label class="checkbox checkbox-lg checkbox-primary">
<!--                                                            <input type="checkbox" name="Checkboxes16"/>-->
<!--                                                            <span class="mr-2"></span>-->
                                                            <?= $washService->name ?? 'Без названия' ?>
                                                        </label>
                                                    </div>
                                                    <div class="checkbox-service--price d-flex align-items-center">
                                                        <!--                                                        <div class="service-old-price text-muted">-->
                                                        <!--                                                            <span>-</span>₽-->
                                                        <!--                                                        </div>-->
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
                                            <div class="checkbox-service-list--item" data-type="complex"
                                                 data-id="<?= $detailingComplex->id ?>">
                                                <div class="checkbox-service-header d-flex justify-content-between">
                                                    <div class="checkbox-service--check">
                                                        <label class="checkbox checkbox-lg checkbox-primary">
<!--                                                            <input type="checkbox" name="Checkboxes16"/>-->
<!--                                                            <span class="mr-2"></span>-->
                                                            <?= $detailingComplex->name ?? 'Без названия' ?>
                                                        </label>
                                                    </div>
                                                    <div class="checkbox-service--price d-flex align-items-center">
                                                        <!--                                                        <div class="service-old-price text-muted">-->
                                                        <!--                                                            <span>-</span>₽-->
                                                        <!--                                                        </div>-->
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
                                            <div class="checkbox-service-list--item" data-type="service"
                                                 data-id="<?= $detailingService->id ?>">
                                                <div class="checkbox-service-header d-flex justify-content-between">
                                                    <div class="checkbox-service--check">
                                                        <label class="checkbox checkbox-lg checkbox-primary">
<!--                                                            <input type="checkbox" name="Checkboxes16"/>-->
<!--                                                            <span class="mr-2"></span>-->
                                                            <?= $detailingService->name ?? 'Без названия' ?>
                                                        </label>
                                                    </div>
                                                    <div class="checkbox-service--price d-flex align-items-center">
                                                        <!--      <div class="service-old-price text-muted">-->
                                                        <!--   <span>-</span>₽-->
                                                        <!--     </div>-->
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
            </div>
        </div>

    </div>
</div>
<?php ActiveForm::end(); ?>

<script>
    $(document).ready(function () {
        let cwId = $('#mainInfoBlock').data('cw-id');
        getPrices()

        $('.toggle-additional-info').click(function () {
            let additionalInfoId = $(this).data('id');
            $('.checkbox-service-additional-info[data-id="' + additionalInfoId + '"]').toggle(100);
        });

        $('.info-sale-toggle').click(function () {
            let additionalInfoId = $(this).data('id');
            $('.sale-additional-info[data-id="' + additionalInfoId + '"]').toggle(200);
        });


        $('#carTypeSelect').on('change', function () {
            let carTypeText = $('#carTypeSelectFormGroup').find('#select2-carTypeSelect-container').text();
            $('#duplicateCarTypeInput').val(carTypeText);
            getPrices();
        });

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
