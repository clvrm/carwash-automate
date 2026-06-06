<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Регистрация';


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
                <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>"
                       value="<?= Yii::$app->request->getCsrfToken(); ?>"/>

                <h3 class="text-center mb-6 font-weight-bold">Регистрация</h3>
                <div class="mt-4 mb-6 font-weight-bold text-center text-danger font-size-h6">
                    <?= $error ?? ' ' ?>
                </div>

                <div class="form-group">
                    <label>Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control form-control-lg" placeholder="Введите email"/>
                </div>
                <div class="form-group">
                    <label>Пароль <span class="text-danger">*</span></label>
                    <input type="password" name="password" class="form-control form-control-lg"
                           placeholder="Введите пароль"/>
                </div>
                <div class="form-group">
                    <label>Повторите пароль <span class="text-danger">*</span></label>
                    <input type="password" name="passwordRepeat" class="form-control form-control-lg"
                           placeholder="Повторите пароль"/>
                </div>
                <div class="form-group">
                    <div class="checkbox-list">
                        <label class="checkbox">
                            <input type="checkbox" checked="checked" name="rememberMe"/>
                            <span></span>
                            Запомнить данные
                        </label>
                        <div class="auth-confidence-agree">
                            <label class="checkbox">
                                <input type="checkbox" checked="checked" name="agreeConfidence"/>
                                <span></span>
                                Я согласен с
                            </label>
                            <a class="ml-1" href="/site/policy"> Политикой конфиденциальности</a>
                        </div>

                    </div>
                </div>

                <button type="submit" class="btn btn-lg btn-block btn-primary mb-5">Зарегистрироваться</button>
                <p>Уже есть аккаунт? <a href="/auth/login">Войти</a></p>
                <?php ActiveForm::end(); ?>
            </div>
            <!--end: Card Body-->
        </div>
    </div>
</div>