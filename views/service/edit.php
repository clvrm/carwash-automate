<?php

use app\commons\helpers\CarTypeHelper;
use app\models\ar\Materials;
use app\models\ar\service\ServiceMaterials;
use app\models\ar\service\Services;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Редактирование услуги';
if ($isDetailing) {
    $this->params['mobileFixedMenuBackUrl'] = '/service/detail';
} else {
    $this->params['mobileFixedMenuBackUrl'] = '/service/wash';
}
$this->params['without_header'] = true;


/**
 * @var Services $model
 * @var Materials $materials
 * @var ServiceMaterials[] $selectedMaterials
 * @var int $isDetailing
 */
$arrayMapSelectedIds = ArrayHelper::map($selectedMaterials, 'id', 'material_id');
?>
<?= $this->render('/layouts/elements/page-header', [
    'is_detailing' => $isDetailing,
    'menu' => 'service',
    'question' => '123123123<small>text</small> 23 аыва рав выф аупыаф вф ывфыа ыаа '
]) ?>

<?php $form = \yii\widgets\ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>
<div class="row">
    <div class="col-lg-12">
        <div id="serviceCreate" class="card card-custom gutter-b initiated-time initiated-price">
            <?= $this->render('_form', ['model' => $model, 'selectedMaterials' => $selectedMaterials]) ?>
        </div>
    </div>
</div>

<div class="modal fade" id="serviceMaterialModal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Прикрепить материал к услуге</h5>
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
                             data-id="<?= $material->id ?>"
                            <?= in_array($material->id, $arrayMapSelectedIds) ? 'style="display:none !important;"' : '' ?>>
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
<?php ActiveForm::end(); ?>

<script>
    $(document).ready(function () {
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
    });

</script>
