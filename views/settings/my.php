<?php
$this->title = 'Профиль';
$this->params['header_button'] = '<div class="d-block mr-12">
     <button class="btn btn-primary px-6">Сохранить</button>
    </div>';
$this->params['mobileFixedMenu'] = [
    '1' => ['title' => 'Личные данные', 'parent_id' => null, 'url' => '#profilePersonal'],
    '2' => ['title' => 'Контакты', 'parent_id' => null, 'url' => '#profileContacts'],
    '3' => ['title' => 'Уведомления', 'parent_id' => null, 'url' => '#profileNotifications'],
];
?>
<div class="row">
    <div class="col-lg-5">
        <div id="profilePersonal" class="card card-custom  gutter-b">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="image-input image-input-outline" id="profile_avatar_uploader">
                            <div class="image-input-wrapper"
                                 style="background-image: url(/media/users/100_1.jpg)"></div>

                            <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                   data-action="change" data-toggle="tooltip" title=""
                                   data-original-title="Изменить аватар">
                                <i class="fa fa-pen icon-sm text-muted"></i>
                                <input type="file" name="profile_avatar" accept=".png, .jpg, .jpeg"/>
                                <input type="hidden" name="profile_avatar_remove"/>
                            </label>

                            <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                  data-action="cancel" data-toggle="tooltip" title="Отменить">
  <i class="ki ki-bold-close icon-xs text-muted"></i>
 </span>
                            <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                  data-action="remove" data-toggle="tooltip" title="Удалить аватар">
  <i class="ki ki-bold-close icon-xs text-muted"></i>
 </span>
                        </div>
                        <div class="form-group row mb-0">
                            <div class="col-lg-6">
                                <label>Имя</label>
                                <input type="email" class="form-control" placeholder="Enter full name"/>
                            </div>
                            <div class="col-lg-6">
                                <label>Фамилия</label>
                                <input type="email" class="form-control" placeholder="Enter contact number"/>
                            </div>
                            <div class="col-lg-12">
                                <label>Отчество</label>
                                <input type="email" class="form-control" placeholder="Enter contact number"/>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div id="profileContacts" class="card card-custom  gutter-b">
            <div class="card-body">
                <h4 class="text-muted">КОНТАКТЫ</h4>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="profile-contact-list mt-4">
                            <div class="form-group row align-items-center">
                                <label class="col-5">Email</label>
                                <div class="col-7 text-muted text-right">
                                    asdasdasd@mail.ru
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label class="col-5">Номер телефона</label>
                                <div class="col-7 text-muted text-right">
                                    +791133333333
                                    <i class="fas fa-chevron-right ml-4"></i>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label class="col-5"><img class="img-fluid mr-2" src="/media/service/telegram-i.png">
                                    Telegram</label>
                                <div class="col-7 text-muted text-right">
                                    <i class="fas fa-chevron-right"></i>
                                </div>
                            </div>
                            <div class="form-group row align-items-center">
                                <label class="col-5"><img class="img-fluid mr-2" src="/media/service/whatsapp-i.png">
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
    </div>
    <div id="profileNotifications" class="col-lg-7">
        <div class="card card-custom  gutter-b">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <h4 class="text-muted">АККАУНТЫ</h4>
                    </div>
                    <div class="col-6 text-right">
                        <button class="btn btn-outline-primary px-6">Добавить</button>
                    </div>
                </div>

                <div class="row mt-8">
                    <div class="col-8">
                    </div>
                    <div class="col-4">
                        <div class="text-muted">Должность</div>
                    </div>
                </div>
                <div class="row profile-account-switcher">
                    <div class="col-12">
                        <div class="d-flex align-items-center row active-profile profile-account-item">
                            <div class="col-2">
                                <div class="text-center image-input-circle">
                                    <img src="/media/users/100_1.jpg" class="img-fluid rounded-circle">
                                </div>
                            </div>
                            <div class="col-6 d-flex">
                                <div class="text-primary mr-2 font-weight-bold">
                                    Супермойка
                                </div>
                                <i class="fas fa-check-circle text-primary"></i>
                            </div>
                            <div class="col-4">
                                Управляющий
                            </div>
                        </div>
                        <div class="d-flex align-items-center row profile-account-item">
                            <div class="col-2">
                                <div class="text-center image-input-circle">
                                    <img src="/media/users/100_1.jpg" class="img-fluid rounded-circle">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-muted">
                                    Супермойка
                                </div>
                            </div>
                            <div class="col-4">
                                Управляющий
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-custom  gutter-b">
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <h4 class="text-muted">УВЕДОМЛЕНИЯ</h4>
                    </div>
                    <div class="col-6 text-right">
                        <button class="btn btn-primary px-6" disabled>Подтвердить</button>
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
                                     <label>
                                         <input type="checkbox" checked="checked" name="select"/><span></span>
                                     </label>
                                 </span>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <div class="col-5">
                                <img class="img-fluid mr-2" src="/media/service/telegram-i.png">
                                Telegram
                            </div>
                            <div class="col-7 text-right">
                                 <span class="switch switch-sm">
                                     <label>
                                         <input type="checkbox" checked="checked" name="select"/><span></span>
                                     </label>
                                 </span>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <div class="col-5">
                                <img class="img-fluid mr-2" src="/media/service/whatsapp-i.png">
                                WhatsApp
                            </div>
                            <div class="col-7 text-right">
                                 <span class="switch switch-sm">
                                     <label>
                                         <input type="checkbox" checked="checked" name="select"/><span></span>
                                     </label>
                                 </span>
                            </div>
                        </div>
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
                                        <label class="checkbox">
                                            <input type="checkbox" name="Checkboxes4"/>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <label class="col-10 col-form-label">Об удалении заказа</label>
                                    <div class="col-2 col-form-label">
                                        <label class="checkbox">
                                            <input type="checkbox" name="Checkboxes4"/>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                            </div>
                            <div class="col-6">
                                <div class="form-group row mb-0">
                                    <label class="col-10 col-form-label">О новом отзыве</label>
                                    <div class="col-2 col-form-label">
                                        <label class="checkbox">
                                            <input type="checkbox" name="Checkboxes4"/>
                                            <span></span>
                                        </label>
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
                                    <label class="col-10 col-form-label">О новом заказе</label>
                                    <div class="col-2 col-form-label">
                                        <label class="checkbox">
                                            <input type="checkbox" name="Checkboxes4"/>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group row mb-0">
                                    <label class="col-10 col-form-label">Об удалении заказа</label>
                                    <div class="col-2 col-form-label">
                                        <label class="checkbox">
                                            <input type="checkbox" name="Checkboxes4"/>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>

                            </div>
                            <div class="col-6">
                                <div class="form-group row mb-0">
                                    <label class="col-10 col-form-label">О новом отзыве</label>
                                    <div class="col-2 col-form-label">
                                        <label class="checkbox">
                                            <input type="checkbox" name="Checkboxes4"/>
                                            <span></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

