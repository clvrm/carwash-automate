<?php

use app\commons\helpers\CarTypeHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Для клиента';
$this->params['header_button'] = '<div class="d-block ">
     <button type="button" class="submitClientForm btn btn-primary px-6">Сохранить</button>
    </div>';
$this->params['mobileFixedMenu'] = [
    '1' => ['title' => 'Основное', 'parent_id' => null, 'url' => '#carwashClientMain'],
    '2' => ['title' => 'Контакты', 'parent_id' => null, 'url' => '#carwashClientContacts'],
    '3' => ['title' => 'Режим работы', 'parent_id' => null, 'url' => '#carwashClientSchedule'],
    '4' => ['title' => 'Комфорт для клиента', 'parent_id' => null, 'url' => '#carwashClientComfort'],
    '5' => ['title' => 'Способы оплаты', 'parent_id' => null, 'url' => '#carwashClientPayments'],
];


?>
<?php $form = \yii\widgets\ActiveForm::begin([
    'id' => 'clientPageForm',
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>
<div class="row">
    <div class="col-lg-5">
        <?= $this->render('_client-main', [
            'carwash' => $carwash,
            'cities' => $cities,
            'carwashImages' => $carwashImages
        ]) ?>

        <?= $this->render('_client-contacts', [
            'contacts' => $contacts,
        ]) ?>

    </div>
    <div class="col-lg-7">
        <?= $this->render('_client-schedule', [
            'schedule' => $schedule,
        ]) ?>

        <?= $this->render('_client-comfort-payments', [
            'comfort' => $comfort,
        ]) ?>
    </div>
</div>
<div class="modal fade" id="carwashContactModal" tabindex="-1" role="dialog"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <img alt="Pic" class="img-fluid mr-2" src="/media/service/social/alternate_email.png"/>
                    Социальные сети и мессенджеры
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-25 mr-2">
                                <img alt="Pic" class="img-fluid mr-2" src="/media/service/social/public.png">
                            </div>
                            <div class="form-group w-100">
                                <label>Сайт</label>
                                <?= Html::activeInput('text', $contacts, 'site', ['class' => 'form-control',
                                    'placeholder' => 'Введите ссылку на сайт']) ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-25 mr-2">
                                <img alt="Pic" class="img-fluid mr-2" src="/media/service/social/vk.png">
                            </div>
                            <div class="form-group w-100">
                                <label>Вконтакте</label>
                                <?= Html::activeInput('text', $contacts, 'vk', ['class' => 'form-control',
                                    'placeholder' => 'Введите ссылку или никнейм']) ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-25 mr-2">
                                <img alt="Pic" class="img-fluid mr-2" src="/media/service/social/instagram.png">
                            </div>
                            <div class="form-group w-100">
                                <label>Instagram</label>
                                <?= Html::activeInput('text', $contacts, 'instagram', ['class' => 'form-control',
                                    'placeholder' => 'Введите ссылку или никнейм']) ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-25 mr-2">
                                <img alt="Pic" class="img-fluid mr-2" src="/media/service/social/facebook.png">
                            </div>
                            <div class="form-group w-100">
                                <label>Facebook</label>
                                <?= Html::activeInput('text', $contacts, 'facebook', ['class' => 'form-control',
                                    'placeholder' => 'Введите ссылку или никнейм']) ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-25 mr-2">
                                <img alt="Pic" class="img-fluid mr-2" src="/media/service/social/telegram.png">
                            </div>
                            <div class="form-group w-100">
                                <label>Telegram</label>
                                <?= Html::activeInput('text', $contacts, 'telegram', ['class' => 'form-control',
                                    'placeholder' => 'Введите ссылку или никнейм']) ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-25 mr-2">
                                <img alt="Pic" class="img-fluid mr-2" src="/media/service/social/whatsapp.png">
                            </div>
                            <div class="form-group w-100">
                                <label>WhatsApp</label>
                                <?= Html::activeInput('text', $contacts, 'whatsapp', ['class' => 'form-control',
                                    'placeholder' => 'Введите ссылку или никнейм']) ?>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-25 mr-2">
                                <img alt="Pic" class="img-fluid mr-2" src="/media/service/social/viber.png">
                            </div>
                            <div class="form-group w-100">
                                <label>Viber</label>
                                <?= Html::activeInput('text', $contacts, 'viber', ['class' => 'form-control',
                                    'placeholder' => 'Введите ссылку или никнейм']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="d-flex align-items-center">
                    <p class="text-muted mb-0">Не забудьте сохранить всю страницу</p>
                <button type="button" class="btn btn-primary px-6 ml-4" data-dismiss="modal">Сохранить</button>
            </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<script>
    $(document).ready(function () {
        let cwId = $('#mainInfoBlock').data('cw-id');

        var imageDropzone = new Dropzone("#carwashImages", {url: "/ajax/carwash/upload-images?carwashId=" + cwId});

        imageDropzone.on("success", function (file, response) {
            if (response.error == 'max-images') {
                Swal.fire("Ограничение на количество фото", "Достигнуто максимальное количество фотографий", "error");
            }
            console.log(response);
        });
        $("#carwashcontacts-phone_1").inputmask("mask", {
            "mask": "+9 (999) 999-9999"
        });
        $("#carwashcontacts-phone_2").inputmask("mask", {
            "mask": "+9 (999) 999-9999"
        });
        $("#carwashcontacts-phone_3").inputmask("mask", {
            "mask": "+9 (999) 999-9999"
        });


        $('.schedule-time-start input').timepicker({
            defaultTime: '00:00',
            minuteStep: 5,
            showSeconds: false,
            showMeridian: false
        });

        $('.schedule-time-end input').timepicker({
            defaultTime: '23:59',
            minuteStep: 5,
            showSeconds: false,
            showMeridian: false
        });

        $('.submitClientForm').on('click', function () {
            if ($('#clientPageForm')[0].checkValidity()) {
                $('#clientPageForm').submit();
            } else {
                $('#clientPageForm')[0].reportValidity();
            }
        });

        $('#addCarwashPhone').click(function () {
            var block = $('.phone-block.d-none').first();
            if (block.length > 0) {
                block.removeClass('d-none');
            }
            if ($('.phone-block.d-none').length == 0) {
                $(this).hide();
            }
        });

        $('.deleteCarwashImage').click(function () {
            let imageId = $(this).data('id');
            let imageBlock = $(this).closest('.client-carwash--image');
            $.ajax({
                type: 'GET',
                url: '/ajax/carwash/delete-image',
                data: {
                    'imageId': imageId,
                    'cwId': cwId,
                },
                success: function (data) {
                    imageBlock.hide(400);
                    toastr.success("Изображение удалено");
                }
            });
        });

        // Предварительное состояние инпутов
        $('.workFulldaySwitcher').each(function (index, item) {
            if ($(item).prop('checked')) {
                let row = $(this).closest('tr.table-schedule--row-item');
                row.find('.schedule-time-start input').prop('readonly', true);
                row.find('.schedule-time-end input').prop('readonly', true);
            }
        });

        // Переключение круглосуточной работы
        $('.workFulldaySwitcher').click(function () {
            let checkboxState = $(this).prop('checked');
            let row = $(this).closest('tr.table-schedule--row-item');

            let startInput = row.find('.schedule-time-start input');
            let endInput = row.find('.schedule-time-end input');

            if (checkboxState) {
                startInput.val('00:00');
                endInput.val('23:59');
            } else{
                startInput.val('00:00');
                endInput.val('23:55');
            }

            startInput.prop('readonly', checkboxState)
            endInput.prop('readonly', checkboxState)
        });
    })
</script>
