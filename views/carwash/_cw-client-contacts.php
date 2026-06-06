<?php


use yii\helpers\Html;

?>
<div id="carwashClientContacts" class="card card-custom  gutter-b">
    <div class="card-body">
        <h4 class="text-muted">КОНТАКТЫ</h4>
        <div class="row mt-4">
            <div class="col-12">
                <div class="mt-4 form-group">
                    <div class="phone-block">
                        <label>Номер телефона</label>
                        <?= Html::activeInput('text', $contacts, 'phone_1', ['class' => 'form-control',
                            'placeholder' => '+7 (___) ___-__-__']) ?>
                    </div>
                    <div class="phone-block mt-2 <?php if (empty($contacts->phone_2)) : ?> d-none<?php endif; ?>">
                        <label>Номер телефона 2</label>
                        <?= Html::activeInput('text', $contacts, 'phone_2', ['class' => 'form-control',
                            'placeholder' => '+7 (___) ___-__-__']) ?>
                    </div>
                    <div class="phone-block mt-2 <?php if (empty($contacts->phone_3)) : ?> d-none<?php endif; ?>">
                        <label>Номер телефона 3</label>
                        <?= Html::activeInput('text', $contacts, 'phone_3', ['class' => 'form-control',
                            'placeholder' => '+7 (___) ___-__-__']) ?>
                    </div>

                    <?php if (empty($contacts->phone_3) or empty($contacts->phone_2) or empty($contacts->phone_1)) : ?>
                        <div class="mt-6">
                            <a href="#" onClick="return false;" id="addCarwashPhone"><i
                                        class="fas fa-plus mr-4 text-primary"></i>Добавить
                                телефон</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="client-social-list--block">
                    <div class="d-flex flex-wrap justify-content-between mb-6">
                        <div class="d-flex  align-items-center">
                            <img alt="Pic" class="img-fluid mr-2"
                                 src="/media/service/social/alternate_email.png"/>
                            <div>Социальные сети</div>
                        </div>
                        <div class="">
                            <button type="button" class="btn btn-outline-primary" data-toggle="modal"
                                    data-target="#carwashContactModal">
                                <i class="fas fa-pen text-primary"></i>
                                Редактировать
                            </button>
                        </div>
                    </div>
                    <?php if (!empty($contacts->site)) : ?>
                        <div class="client-social--items d-flex align-items-center">
                            <div class="symbol symbol-25 mr-2">
                                <img alt="Pic" class="img-fluid mr-2"
                                     src="/media/service/social/public.png"/>
                            </div>
                            <?= $contacts->site ?? '' ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($contacts->vk)) : ?>
                        <div class="client-social--items d-flex align-items-center">
                            <div class="symbol symbol-25 mr-2">
                                <img alt="Pic" class="img-fluid mr-2"
                                     src="/media/service/social/vk.png"/>
                            </div>
                            <?= $contacts->vk ?? '' ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($contacts->facebook)) : ?>
                        <div class="client-social--items d-flex align-items-center">
                            <div class="symbol symbol-25 mr-2">
                                <img alt="Pic" class="img-fluid mr-2"
                                     src="/media/service/social/facebook.png"/>
                            </div>
                            <?= $contacts->facebook ?? '' ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($contacts->instagram)) : ?>
                        <div class="client-social--items d-flex align-items-center">
                            <div class="symbol symbol-25 mr-2">
                                <img alt="Pic" class="img-fluid mr-2"
                                     src="/media/service/social/instagram.png"/>
                            </div>
                            <?= $contacts->instagram ?? '' ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($contacts->telegram)) : ?>
                        <div class="client-social--items d-flex align-items-center">
                            <div class="symbol symbol-25 mr-2">
                                <img alt="Pic" class="img-fluid mr-2"
                                     src="/media/service/social/telegram.png"/>
                            </div>
                            <?= $contacts->telegram ?? '' ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($contacts->whatsapp)) : ?>
                        <div class="client-social--items d-flex align-items-center">
                            <div class="symbol symbol-25 mr-2">
                                <img alt="Pic" class="img-fluid mr-2"
                                     src="/media/service/social/whatsapp.png"/>
                            </div>
                            <?= $contacts->whatsapp ?? '' ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($contacts->viber)) : ?>
                        <div class="client-social--items d-flex align-items-center">
                            <div class="symbol symbol-25 mr-2">
                                <img alt="Pic" class="img-fluid mr-2"
                                     src="/media/service/social/viber.png"/>
                            </div>
                            <?= $contacts->viber ?? '' ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
