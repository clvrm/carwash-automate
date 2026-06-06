<?php

use app\models\ar\Users;
use app\models\forms\profile\ProfileContactsForm;

/**
 * @var ProfileContactsForm $contacts
 * @var Users $user
 */
?>
<div id="profileContacts" class="card card-custom  gutter-b">
    <div class="card-body">
        <h4 class="text-muted">КОНТАКТЫ
            <i class="far fa-question-circle p-1" data-toggle="popover" title="Видимость контактов" data-html="true"
            data-content="Контактные данные видны только для техподдержки, для клиентов они скрыты"></i>
        </h4>
        <div class="row">
            <div class="col-lg-12">
                <div class="profile-contact-list mt-4">
                    <div class="form-group row align-items-center">
                        <label class="col-5">Email</label>
                        <div class="col-7 text-muted text-right">
                            <?= $user->email ?? 'Не указан' ?>
                        </div>
                    </div>
                    <div class="form-group row align-items-center">
                        <label class="col-5">Номер телефона</label>
                        <div class="col-7 text-muted text-right" data-toggle="modal" data-target="#phoneChangeModal">
                            <?= $user->phone ?? 'Не указан' ?>
                            <i class="fas fa-chevron-right ml-4"></i>
                        </div>
                    </div>
                    <div class="form-group row align-items-center" data-toggle="modal"
                         data-target="#telegramChangeModal">
                        <label class="col-5 d-flex"><img class="img-fluid mr-2" src="/media/service/telegram-i.png">
                            Telegram</label>
                        <div class="col-7 text-muted text-right">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                    <div class="form-group row align-items-center" data-toggle="modal"
                         data-target="#whatsappChangeModal">
                        <label class="col-5 d-flex"><img class="img-fluid mr-2" src="/media/service/whatsapp-i.png">
                            WhatsApp</label>
                        <div class="col-7 text-muted text-right">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal-->
<div class="modal fade" id="phoneChangeModal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Изменение номера телефона</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                Для изменения номера телефона обратитесь к администратору автомойки.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Закрыть
                </button>
                <!--                <button type="button" class="btn btn-primary font-weight-bold">Сохранить</button>-->
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="telegramChangeModal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Привязка телеграм к аккаунту</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body"> На данный момент не реализовано. Ожидается в ближайших обновлениях</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Закрыть
                </button>
                <!--                <button type="button" class="btn btn-primary font-weight-bold">Сохранить</button>-->
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="whatsappChangeModal" tabindex="-1" role="dialog" aria-labelledby="staticBackdrop"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Привязка WhatsApp аккаунта</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Закрыть">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                На данный момент не реализовано. Ожидается в ближайших обновлениях
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Закрыть
                </button>
                <!--                <button type="button" class="btn btn-primary font-weight-bold">Сохранить</button>-->
            </div>
        </div>
    </div>
</div>
