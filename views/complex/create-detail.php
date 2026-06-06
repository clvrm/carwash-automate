<?php

use app\commons\helpers\CarTypeHelper;
use app\models\ar\complex\Complexes;
use app\models\ar\complex\ComplexMaterials;
use app\models\ar\complex\ComplexServices;
use app\models\ar\Materials;
use app\models\ar\service\Services;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Создание комплекса';
$this->params['mobileFixedMenuBackUrl'] = '/complex/detail';
$this->params['without_header'] = true;


/**
 * @var Services[] $services
 * @var Complexes $model
 * @var ComplexMaterials[] $selectedMaterials
 * @var ComplexServices[] $selectedServices
 * @var Materials[] $materials
 */

$this->params['main-class'] = 'initiated-price initiated-time';
?>
<?= $this->render('/layouts/elements/page-header', [
    'is_detailing' => true,
    'menu' => 'complex',
    'tabs' => [
        [
            'id' => 'firstStepHeaderTab',
            'subtitle' => 'Шаг один',
            'title' => 'Выбор услуг',
            'link' => '#',
            'active' => true,
        ],
        [
            'id' => 'secondStepHeaderTab',
            'subtitle' => 'Шаг два',
            'title' => 'Параметры комплекса',
            'link' => '#',
        ],
    ]
]) ?>

<?php $form = \yii\widgets\ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>
<?= $this->render('_detail-form', [
    'model' => $model,
    'services' => $services,
    'selectedServices' => $selectedServices,
    'selectedMaterials' => $selectedMaterials,
    'materials' => $materials,
]) ?>

<?php ActiveForm::end(); ?>

<div class="modal fade" id="serviceMaterialModal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Прикрепить материал к комплексу</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div data-scroll="true" data-height="300">
                    <?php if (empty($materials)):?>
                        <p class="text-center">Сейчас у вас не создано материалов для услуг</p>
                        <p class="text-center">Для создания - перейдите в соответствующий раздел сайта</p>
                    <?php endif;?>
                    <?php foreach ($materials as $material) : ?>
                        <div class="modal-material-list--item d-flex service-material--info"
                             data-id="<?= $material->id ?>">
                            <div class="py-2 w-75 d-flex justify-content-between">
                                <div><?= $material->name ?? '' ?></div>
                                <div><?= $material->price ?? '' ?> ₽</div>
                            </div>
                            <div class="ml-8 min-w-auto d-flex justify-content-between">
                                <div class="modal-material-list--item-add mr-4 ml-4 align-self-center"
                                     data-id="<?= $material->id ?>" data-title="<?= $material->name ?? '' ?>"
                                     data-price="<?= $material->price ?? '' ?>">
                                    <i class="fas fa-plus text-primary"></i>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">
                            Готово
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        let cwId = $('#mainInfoBlock').data('cw-id');
        let pId = $('#mainInfoBlock').data('p-id');

        $('.service-list--add').on('click', function () {
            let serviceBlock = $(this).closest('.services-list--item');
            let serviceId = $(serviceBlock).data('id');
            let serviceTitle = $(serviceBlock).find('.service-list--title').text();

            serviceBlock.addClass('d-none-i');
            serviceBlock.addClass('service-already-selected');

            $('.services-selected-list--item.default-defined-block').clone().appendTo(".create-complex-selected-list");
            let dataBlock = $('.services-selected-list--item.default-defined-block').first();

            dataBlock.show(100);

            dataBlock.removeClass('default-defined-block');
            dataBlock.find('.service-list--title').text(serviceTitle);
            dataBlock.data('id', serviceId);
            dataBlock.find('.complex-selected-service-card-delete').data('id', serviceId);
            dataBlock.find('.selected-service--input').prop('name', 'service[' + serviceId + ']');
            dataBlock.find('.selected-service--input').val(serviceId);
        });

        $(document).on('click', '.complex-selected-service-card-delete', function () {
            let serviceId = $(this).data('id');
            let serviceSelectedBlock = $(this).closest('.services-selected-list--item');

            serviceSelectedBlock.remove();
            $('.services-list--item[data-id="' + serviceId + '"]').removeClass('service-already-selected')
            $('.services-list--item[data-id="' + serviceId + '"]').removeClass('d-none-i')
        });

        $('#serviceAutocompleteInput').on('change', function () {
            let queryVal = $(this).val();

            $.ajax({
                type: 'GET',
                url: '/ajax/common/get-services-list',
                data: {
                    'cwId': cwId,
                    'pId': pId,
                    'query': queryVal,
                    'isDetailing': true,
                },
                success: function (data) {
                    console.log(data);
                    if (!data.result) {
                        toastr.success("Не удалось получить список услуг");
                    } else {
                        if (data.items.length === 0) {
                            toastr.success("Список пуст");
                            $('.services-list--item:not(.service-already-selected)').removeClass('d-none-i');
                        } else {
                            $('.services-list--item').addClass('d-none-i');
                            $.each(data.items, function (index, item) {
                                let serviceId = item.id;
                                $('.services-list--item[data-id="' + serviceId + '"]:not(.service-already-selected)').removeClass('d-none-i');
                            })
                        }
                    }
                }
            });
        })

        $('#complexDetailingPriceByTypeNav').click(function () {
            $('#priceForAllInput').val(null);
        });

        $('#goToSecondStep').click(function () {
            let complexName = $('#complexes-name').val();
            if (complexName.length == 0) {
                $('#complexes-name').addClass('is-invalid');
                return false;
            }
            $('#complexes-name').removeClass('is-invalid');

            $('#complexWashFirst').hide();
            $('#complexWashSecond').show(300);
            $('#firstStepHeaderTab').find('.header-tab-subtitle').removeClass('text-primary').addClass('text-muted');
            $('#secondStepHeaderTab').find('.header-tab-subtitle').addClass('text-primary').removeClass('text-muted');
            $('.header-page-title').text('Цена');
        });

        $('#goToFirstStep').click(function () {
            $('#complexWashSecond').hide();
            $('#complexWashFirst').show(300);
            $('#firstStepHeaderTab').find('.header-tab-subtitle').addClass('text-primary').removeClass('text-muted');
            $('#secondStepHeaderTab').find('.header-tab-subtitle').removeClass('text-primary').addClass('text-muted');
            $('.header-page-title').text('Выбор услуг');
        });

        $('.modal-material-list--item-add').click(function () {
            $('.service-material--item.default-defined-block').clone().appendTo(".service-materials--block");
            let dataBlock = $('.service-material--item.default-defined-block').first();

            let modalData = $(this).closest('.modal-material-list--item');
            modalData.attr('style', 'display: none !important');
            let id = $(this).data('id');
            let title = $(this).data('title');
            let price = $(this).data('price');

            dataBlock.show(100);

            dataBlock.removeClass('default-defined-block');
            dataBlock.find('.service-material--info .service-material--title').text(title);
            dataBlock.find('.service-material--info .service-material--price').text(price + ' ₽');
            dataBlock.find('.service-card-edit--material').data('id', id);
            dataBlock.find('.service-card-delete--material').data('id', id);
            dataBlock.find('.service-material--price-input').prop('name', 'material[' + id + '][price]');
            dataBlock.find('.service-material--price-input').val(price);
        });

        $(document).on('click', '.service-card-delete--material', function () {
            let id = $(this).data('id');

            $(this).closest('.service-material--item').remove();
            $('.modal-material-list--item[data-id="' + id + '"]').show();
        });

        $(document).on('click', '.service-card-edit--material', function () {
            let dataBlock = $(this).closest('.service-material--item');
            dataBlock.find('.service-material--price').hide();
            dataBlock.find('.service-material--price-input').show();
            dataBlock.find('.service-material--price-input').attr('hidden', false);
        });

        $(document).on('change', '.service-material--price-input', function () {
            let dataBlock = $(this).closest('.service-material--item');
            let price = $(this).val();
            dataBlock.find('.service-material--price').text(price + ' ₽');
            dataBlock.find('.service-material--price').show();
            dataBlock.find('.service-material--price-input').hide();
            dataBlock.find('.service-material--price-input').attr('hidden', true);
        });
    })
</script>
