<?php

use app\models\ar\personal\Personal;
use yii\widgets\ActiveForm;

$this->title = 'Выбор аккаунта для входа';

?>


<div class="row h-100">
    <div class="offset-md-3 col-md-6 col-xs-12 align-self-center">
        <div class="card card-custom card-stretch gutter-b ">
            <!--begin::Header-->
            <div class="mt-8 d-flex align-self-center">
                <a class="back-arrow" href="/auth/logout"></a>
                <h2 class="font-weight-bolder text-dark">Carwash</h2>
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body pt-10">
                <h3 class="text-center mb-6 font-weight-bold">Выберите нужный аккаунт</h3>
                <div class="auth-account-selector mb-10 mt-4">
                    <?php foreach ($personal as $personalItem) : ?>
                        <div class="auth-account_item d-flex pt-4" data-personal-id="<?= $personalItem->id ?>">
                            <div class="auth-account_image rounded-circle">
                                <img src="<?= $personalItem->carwash->avatar ?? '/media/service/service-auth-logo.png' ?>"
                                     alt="<?= $personalItem->carwash->name ?? 'Автомойка' ?>" class="rounded-circle">
                            </div>
                            <div class="auth-account_info">
                                <h5 class="font-weight-bolder"><?= $personalItem->carwash->name ?? 'Автомойка' ?></h5>
                                <p><?= Personal::getPostLabel($personalItem->post) ?? '' ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php $form = \yii\widgets\ActiveForm::begin(); ?>
                <input id="selectedPersonalInput" name="personalId" hidden>
                <button type="submit" class="btn btn-lg btn-block btn-primary mb-5">Авторизация</button>
                <?php ActiveForm::end(); ?>
            </div>
            <!--end: Card Body-->
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.auth-account_item').on('click', function (e) {
            $('.auth-account_item').removeClass('selected-account');
            $(this).addClass('selected-account');
            let selectedId = $(this).data('personal-id');

            $('#selectedPersonalInput').val(selectedId);
        });
    });
</script>