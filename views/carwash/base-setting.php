<?php

use yii\base\Model;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Настройки';

?>
<?php $form = \yii\widgets\ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>
<div class="row">
    <?php $errors = $form->errorSummary([$user ?? new Model(), $carwash ?? new Model()]);
    if (!empty($carwash->errors) or !empty($user->errors)) : ?>
        <div class="col-12">
            <div class="alert alert-danger" role="alert">
                <?= $errors ?? '' ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="col-lg-5">
        <div id="carwashSettingsPost" class="card card-custom  gutter-b">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h4 class="text-muted">Данные для входа</h4>
                        <div class="form-group">
                            <label>Ваш номер телефона</label>
                            <input id="userPhone" type="text" name="phone" value="<?= $user->phone ?? '' ?>"
                                   class="form-control"
                                   placeholder="+7 (___) ___-__-__"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="carwashSettingsBlacklist" class="card card-custom  gutter-b">
            <div class="card-body">
                <h4 class="text-muted">Информация профиля</h4>
                <div class="row">
                    <div class="col-12">
                        <div class="image-input image-input-outline" id="profile_avatar_uploader">
                            <div class="image-input-wrapper"
                                 style="background-image: url(<?= $user->avatar ?? '/media/service/user/user-default.jpg' ?>)"></div>

                            <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                   data-action="change" data-toggle="tooltip" title=""
                                   data-original-title="Изменить аватар">
                                <i class="fa fa-pen icon-sm text-muted"></i>
                                <input type="file" name="avatar"/>
                            </label>

                            <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                  data-action="cancel" data-toggle="tooltip" title="Отменить">
  <i class="ki ki-bold-close icon-xs text-muted"></i>
 </span>
                        </div>
                        <div class="form-group row mb-4">
                            <div class="col-lg-6">
                                <label>Имя</label>
                                <?= Html::activeInput('text', $user, 'firstname', ['class' => 'form-control',
                                    'placeholder' => 'Введите имя', 'required' => true]) ?>
                            </div>
                            <div class="col-lg-6">
                                <label>Фамилия</label>
                                <?= Html::activeInput('text', $user, 'lastname', ['class' => 'form-control',
                                    'placeholder' => 'Введите фамилию', 'required' => true]) ?>
                            </div>
                        </div>
                        <div class="form-group row mb-0">

                            <div class="col-lg-12">
                                <label>Отчество</label>
                                <?= Html::activeInput('text', $user, 'patronymic', ['class' => 'form-control',
                                    'placeholder' => 'Введите отчество', 'required' => false]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($carwash) : ?>
        <div class="col-lg-7">
            <div id="carwashSettingsOrder" class="card card-custom  gutter-b">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">

                            <h4>Заполните данные, прежде чем продолжить</h4>
                            <div class="form-group row align-items-center mt-4">
                                <div class="col-8">
                                    <label>Название автомойки</label>
                                    <input type="text" name="carwash_name" value="<?= $carwash->name ?? '' ?>"
                                           class="form-control"
                                           placeholder="Введите название автомойки"/>

                                    <label class="mt-4">Город</label>
                                    <select class="form-control select2 default-select2" name="city_id">
                                        <?php foreach ($cities as $city) : ?>
                                            <?php if (!empty($carwash->city_id) && $carwash->city_id == $city->id) : ?>
                                                <option selected
                                                        value="<?= $city->id ?>"><?= $city->name ?? '' ?></option>
                                            <?php elseif (empty($carwash->city_id) && !empty($preDefinedCity) && $preDefinedCity == $city->name): ?>
                                                <option selected
                                                        value="<?= $city->id ?>"><?= $city->name ?? '' ?></option>
                                            <?php else: ?>
                                                <option value="<?= $city->id ?>"><?= $city->name ?? '' ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row flex-wrap align-items-center mt-4">
                                <div class="col-8">
                                    <label>Адрес</label>
                                    <input type="text" name="carwash_address" value="<?= $carwash->address ?? '' ?>"
                                           class="form-control"
                                           placeholder="Введите адрес"/>
                                </div>
                                <div class="col-4" style="display:none;">
                                    <div id="openCarwashAddressMap" class="mt-7">
                                <span class="label label-rounded label-primary p-6 text-primary">
                                    <i class="fa fa-map-marker-alt text-primary"></i>
                                </span>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>
<div class="row">
    <div class="col-12">
        <div class="row mt-2">
            <div class="col-12">
                <div class="text-right">
                    <button type="submit" class="btn btn-lg btn-primary">Сохранить</button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<script>
    $(document).ready(function () {
        $("#userPhone").inputmask("mask", {
            "mask": "(999) 999-9999"
        });
    });
</script>

