<?php

use app\models\ar\Users;
use yii\helpers\Html;

/**
 * @var Users $user
 */

?>
<div id="profilePersonal" class="card card-custom  gutter-b">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="image-input image-input-outline" id="profile_avatar_uploader">
                    <div class="image-input-wrapper"
                         style="background-image: url(<?= $user->avatar ?? '/media/service/user/user-default.jpg' ?>)"></div>

                    <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                           data-action="change" data-toggle="tooltip" title=""
                           data-original-title="Изменить аватар">
                        <i class="fa fa-pen icon-sm text-muted"></i>
                        <input type="file" name="profile_avatar" accept=".png, .jpg, .jpeg"/>
                    </label>

                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                          data-action="cancel" data-toggle="tooltip" title="Отменить">
  <i class="ki ki-bold-close icon-xs text-muted"></i>
 </span>
                </div>
                <div class="form-group row mb-0">
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

