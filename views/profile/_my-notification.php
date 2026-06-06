<?php


use app\models\ar\personal\PersonalNotification;
use app\models\ar\rbac\AuthItem;
use app\models\forms\profile\ProfileNotificationsForm;
use app\widgets\helpers\SVG;
use yii\helpers\Html;

/**
 * @var ProfileNotificationsForm $notify
 */
?>
<div id="profileNotifications" class="card card-custom  gutter-b">
    <div class="card-body">
        <div class="row">
            <div class="col-6">
                <h4 class="text-muted">УВЕДОМЛЕНИЯ</h4>
            </div>
            <div class="col-6 text-right">
                <!--                <button class="btn btn-primary px-6" disabled>Подтвердить</button>-->
            </div>
        </div>

        <div class="row mt-6">
            <div class="col-12 profile-notification-selector">
                Куда отправлять уведомления?
                <div class="form-group row align-items-center mt-4">
                    <div class="col-5">
                        <img class="img-fluid mr-2" src="/media/service/email-i.png">
                        Email
                    </div>
                    <div class="col-7 text-right">
                        <span class="switch switch-sm">
                            <?= Html::activeCheckbox($notify, 'systemEmail', [
                                'uncheck' => 0,
                                'label' => '<span></span>',
                            ]) ?>
                        </span>
                    </div>
                </div>
                <div class="form-group row align-items-center mt-4">
                    <div class="col-5">
                        <div class="img-fluid mr-2">
                        <?= SVG::getIcon('Marker1')?>
                            Push
                        </div>

                    </div>
                    <div class="col-7 text-right">
                        <span class="switch switch-sm">
                            <?= Html::activeCheckbox($notify, 'systemPush', [
                                'uncheck' => 0,
                                'label' => '<span></span>',
                            ]) ?>
                        </span>
                    </div>
                </div>
                <div class="form-group row align-items-center d-none">
                    <div class="col-5">
                        <img class="img-fluid mr-2" src="/media/service/telegram-i.png">
                        Telegram
                    </div>
                    <div class="col-7 text-right">
                        <span class="switch switch-sm">
                            <?= Html::activeCheckbox($notify, 'systemTelegram', [
                                'uncheck' => 0,
                                'label' => '<span></span>',
                            ]) ?>
                        </span>
                    </div>
                </div>
<!--                <div class="form-group row align-items-center">-->
<!--                    <div class="col-5">-->
<!--                        <img class="img-fluid mr-2" src="/media/service/whatsapp-i.png">-->
<!--                        WhatsApp-->
<!--                    </div>-->
<!--                    <div class="col-7 text-right">-->
<!--                        <span class="switch switch-sm">-->
                            <?php //Html::activeCheckbox($notify, 'systemWhatsapp', [
//                                'uncheck' => 0,
//                                'label' => '<span></span>',
//                            ]) ?>
<!--                        </span>-->
<!--                    </div>-->
<!--                </div>-->
            </div>
            <div class="mt-6 profile-notification-checkboxes col-12">
                <div class="row">
                    <div class="col-12">
                        <h4 class="text-muted">СОБЫТИЯ</h4>
                    </div>
                    <div class="col-6">
                        <div class="form-group row mb-0">
                            <label class="col-10 col-form-label">О новом заказе</label>
                            <div class="col-2 col-form-label">
                                <?= Html::activeCheckbox($notify,PersonalNotification::EVENT_NEW_ORDER,
                                    [
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label class="col-10 col-form-label">О новом отзыве</label>
                            <div class="col-2 col-form-label">
                                <?= Html::activeCheckbox($notify,PersonalNotification::EVENT_NEW_REVIEW,
                                    [
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group row mb-0">
                            <label class="col-10 col-form-label">Об удалении заказа</label>
                            <div class="col-2 col-form-label">
                                <?= Html::activeCheckbox($notify,PersonalNotification::EVENT_CANCEL_ORDER,
                                    [
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label class="col-10 col-form-label">Формирование отчета за период</label>
                            <div class="col-2 col-form-label">
                                <?= Html::activeCheckbox($notify,PersonalNotification::EVENT_NEW_SALES_REPORT,
                                    [
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="row mt-8">
                    <div class="col-12">
                        <h4 class="text-muted">ИЗМЕНЕНИЕ НАСТРОЕК</h4>
                    </div>
                    <div class="col-6">
                        <div class="form-group row mb-0">
                            <label class="col-10 col-form-label">Редактирование графика работы автомойки</label>
                            <div class="col-2 col-form-label">
                                <?= Html::activeCheckbox($notify,PersonalNotification::EVENT_EDIT_SCHEDULE,
                                    [
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label class="col-10 col-form-label">Редактирование настроек online записи</label>
                            <div class="col-2 col-form-label">
                                <?= Html::activeCheckbox($notify,PersonalNotification::EVENT_EDIT_RECORD_SETTINGS,
                                    [
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label class="col-10 col-form-label">Редактирование прайс-листа</label>
                            <div class="col-2 col-form-label">
                                <?= Html::activeCheckbox($notify,PersonalNotification::EVENT_EDIT_PRICE_LIST,
                                    [
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>

                    </div>
                    <div class="col-6">
                        <div class="form-group row mb-0">
                            <label class="col-10 col-form-label">Редактирование учета ЗП</label>
                            <div class="col-2 col-form-label">
                                <?= Html::activeCheckbox($notify,PersonalNotification::EVENT_EDIT_PERSONAL_SALARY,
                                    [
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label class="col-10 col-form-label">Добавление нового сотрудника</label>
                            <div class="col-2 col-form-label">
                                <?= Html::activeCheckbox($notify,PersonalNotification::EVENT_NEW_PERSONAL,
                                    [
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group row mb-0">
                            <label class="col-10 col-form-label">Отключение / Включение online записи</label>
                            <div class="col-2 col-form-label">
                                <?= Html::activeCheckbox($notify,PersonalNotification::EVENT_CHANGE_ONLINE_RECORD_STATUS,
                                    [
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

