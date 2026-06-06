<?php

use yii\widgets\ActiveForm;

$this->title = 'Восстановление пароля';

?>

<div class="row h-100">
    <div class="offset-md-3 col-md-6 col-xs-12 align-self-center">
        <div class="card card-custom card-stretch gutter-b ">
            <!--begin::Header-->
            <div class="mt-8 d-flex align-self-center">
                <h2 class="font-weight-bolder text-dark">Carwash</h2>
            </div>
            <!--end::Header-->
            <!--begin::Body-->
            <div class="card-body pt-10">
                <?php $form = \yii\widgets\ActiveForm::begin([
                    'id' => 'login-form',
                    'options' => ['class' => 'text-left'],
                ]); ?>
                <h3 class="text-center mb-6 font-weight-bold">Восстановление пароля</h3>
                <p class="text-center">Введите Email адрес, указанный при регистрации.
                    На него мы отправим ссылку для восстановления пароля.</p>
                <div class="mt-4 mb-6 font-weight-bold text-center text-danger font-size-h6">
                    <?= $error ?? ' ' ?>
                </div>
                <div class="form-group">
                    <label>Email </label>
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="Введите Email"/>
                </div>
                <button type="submit" class="btn btn-lg btn-block btn-primary mb-5">Изменить пароль</button>
                <?php ActiveForm::end(); ?>
            </div>
            <!--end: Card Body-->
        </div>
    </div>
</div>