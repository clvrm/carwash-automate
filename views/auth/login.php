<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Вход';


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
                <h3 class="text-center mb-6 font-weight-bold">Вход</h3>
                <div class="mt-4 mb-6 font-weight-bold text-center text-danger font-size-h6">
                    <?= $error ?? ' ' ?>
                </div>
                <div class="form-group">
                    <label>Email </label>
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="Введите Email"/>
                </div>
                <div class="form-group">
                    <label>Пароль</label>
                    <input type="password" name="password" class="form-control form-control-lg"
                           placeholder="Введите пароль"/>
                    <p>Забыли пароль? <a href="/auth/reset">Восстановить пароль</a></p>

                </div>
                <div class="form-group">
                    <div class="checkbox-list">
                        <label class="checkbox">
                            <input type="checkbox" checked="checked" name="rememberMe"/>
                            <span></span>
                            Запомнить данные
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-lg btn-block btn-primary mb-5">Войти</button>
                <p class="text-muted text-center mb-0">
                    <i class="flaticon2-lock mr-1"></i>
                    Регистрация доступна только через администратора
                </p>
                <?php ActiveForm::end(); ?>
            </div>
            <!--end: Card Body-->
        </div>
    </div>
</div>