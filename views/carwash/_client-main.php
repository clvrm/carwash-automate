<?php


use yii\helpers\Html;

?>
<div id="carwashClientMain" class="card card-custom  gutter-b">
    <div class="card-body">
        <div class="row">
            <div class="col-10">
                <div class="image-input image-input-outline" id="profile_avatar_uploader">
                    <div class="image-input-wrapper"
                         style="background-position: center;background-size: contain;background-image: url(<?= $carwash->avatar ?? '/media/service/service-auth-logo.png' ?>)"></div>

                    <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                           data-action="change" data-toggle="tooltip" title=""
                           data-original-title="Изменить аватар">
                        <i class="fa fa-pen icon-sm text-muted"></i>
                        <input type="file" name="avatar" accept=".png, .jpg, .jpeg"/>
                    </label>

                    <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                          data-action="cancel" data-toggle="tooltip" title="Отменить">
  <i class="ki ki-bold-close icon-xs text-muted"></i>
 </span>
                </div>
                <div class="mt-4 form-group">
                    <label>Название автомойки</label>
                    <?= Html::activeInput('text', $carwash, 'name', ['class' => 'form-control',
                        'placeholder' => 'Введите название автомойки', 'required' => true]) ?>
                </div>

                <div class="form-group">
                    <label for="citySelect">Город</label>
                    <select class="form-control default-select2" id="citySelect">
                        <?php foreach ($cities as $city) : ?>
                            <?php if (!empty($carwash->city_id) && $carwash->city_id == $city->id) : ?>
                                <option selected value="<?= $city->id ?>"><?= $city->name ?? '' ?></option>
                            <?php else: ?>
                                <option value="<?= $city->id ?>"><?= $city->name ?? '' ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mt-4 form-group">
                    <label>Адрес</label>
                    <?= Html::activeInput('text', $carwash, 'address', ['class' => 'form-control',
                        'placeholder' => 'Введите адрес', 'required' => false]) ?>
                </div>
                <div class="form-group">
                    <label for="timezoneSelect">Временная зона</label>
                    <select class="form-control default-select2" name="Carwash[timezone]" id="timezoneSelect">
                        <?php foreach ($carwash->getTimezonesLabels() as $timezoneId => $timezonesLabel): ?>
                            <?php if (!empty($carwash->timezone) && $carwash->timezone == $timezoneId) : ?>
                                <option selected value="<?= $timezoneId ?>">
                                    <?= $timezonesLabel ?? '' ?>
                                </option>
                            <?php else: ?>
                                <option value="<?= $timezoneId ?>"><?= $timezonesLabel ?? '' ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <h3>Галерея</h3>
                <div class="client-carwash--images d-flex flex-wrap">
                    <?php foreach ($carwashImages as $image) : ?>
                        <div class="client-carwash--image mr-2 mb-2">
                            <div class="symbol symbol-40">
                                <img src="<?= $image->image ?? '' ?>">
                            </div>
                            <div class="client-carwash--image-control d-flex align-content-center align-items-center justify-content-center">
                                <i data-action="delete-image" data-id="<?= $image->id ?? '' ?>"
                                   class="deleteCarwashImage fas fa-trash text-light-danger text-hover-danger"></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="dropzone dropzone-default dz-clickable mt-8" id="carwashImages">
                    <div class="dropzone-msg dz-message needsclick">
                        <h3 class="dropzone-msg-title">Перетащите файлы сюда или нажмите
                            для загрузки</h3>
                        <span class="dropzone-msg-desc">Загрузите до 5 фотографий автомойки</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

