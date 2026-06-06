<?php

use app\models\ar\personal\Personal;

$this->title = 'Персонал';

$this->params['header_button'] = '<a href="/personal/create"><div class="d-block">
     <button class="btn btn-primary px-6">+ Добавить сотрудника</button>
    </div></a>';

/**
 * @var Personal[] $personals
 */
?>

<div class="row">
    <?php foreach ($personals as $personal): ?>
        <div class="col-lg-6 col-xl-4">
            <div class="card personal-card card-custom gutter-b">
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between align-content-stretch w-100">
                        <div class="card-title">
                            <div class="personal-image symbol symbol-50 mr-3 mt-1">
                                <div class="symbol-label"
                                     style="background-image: url(<?= $personal->user->avatar ?? '/media/service/user/user-default.jpg' ?>)"></div>
                            </div>
                            <div class="d-block personal-title">
                                <div class="personal-post"><small>Сотрудник</small></div>
                                <h5 class="personal-name"><?= $personal->getShortUsername() ?? '' ?></h5>
                            </div>
                        </div>
                        <div class="card-toolbar">
                            <div class="d-flex">
                                <a href="/personal/edit?id=<?= $personal->id ?>">
                                    <div class="personal-card-edit mr-4">
                                        <i class="fas fa-pen text-primary"></i>
                                    </div>
                                </a>
                                <div class="personal-card-delete cursor-pointer" data-id="<?= $personal->id ?>">
                                    <i class="fas fa-trash text-light-danger text-hover-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 mb-2 d-flex flex-wrap justify-content-between align-content-stretch w-100">
                        <div class="person-price d-flex">
                            <div class="text-muted mr-4">З/П</div>
                            <?= $personal->getSalaryFullLabel() ?? '-' ?>
                        </div>
                        <?php if ($personal->isOnline()): ?>
                        <div class="person-last-login font-size-sm text-success">
                            Online
                        </div>
                        <?php else :?>
                            <div class="person-last-login font-size-sm text-danger">
                                Offline <span class="text-dark-25">(<?= $personal->lastOnline() ?>)</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="person-card-info d-block">
                        <div class="d-flex flex-wrap justify-content-between align-content-stretch mb-2">
                            <div class="align-items-center person-info--item person-info--post d-flex">
                                <div class="text-muted mr-2">Должность:</div>
                                <?= $personal::getPostLabel($personal->post) ?? 'Не указан' ?>
                            </div>
                            <?php if ($personal->is_approved): ?>
                                <div class="person-info--status">
                                    Активирован
                                </div>
                            <?php else: ?>
                                <div class="person-info--status bg-warning-o-40">
                                    В ожидании
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($personal->user->email)) : ?>
                            <div class="d-flex flex-wrap justify-content-between align-content-stretch mb-2">
                                <div class="align-items-center person-info--item person-info--post d-flex">
                                    <div class="text-muted mr-2">Email:</div>
                                    <?= $personal->user->email ?? 'Не указан' ?>
                                </div>
                                <?php if ($personal->is_approved): ?>
                                    <div class="person-info--status">
                                        Активирован
                                    </div>
                                <?php else: ?>
                                    <div class="person-info--status bg-warning-o-40">
                                        В ожидании
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($personal->user->phone)) : ?>
                            <div class="d-flex flex-wrap justify-content-between align-content-stretch">
                                <div class="align-items-center person-info--item person-info--post d-flex min-h-35px">
                                    <div class="text-muted mr-2">Телефон:</div>
                                    <?= $personal->user->phone ?? 'Не указан' ?>
                                </div>
                                <?php if ($personal->user->phone_verified) : ?>
                                    <div class="person-info--status">
                                        Подтвержден
                                    </div>
                                <?php else: ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    $(document).ready(function () {

        let cwId = $('#mainInfoBlock').data('cw-id');

        $('.personal-card-delete').on('click', function () {
            let id = $(this).data('id');
            let personalBlock = $(this).closest('.personal-card');
            Swal.fire({
                title: "Вы действительно хотите удалить данного сотрудника?",
                text: "Это действие невозможно отменить. Это запретит доступ сотрудника к вашей автомойке, а также удалит его данные из аналитики",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                cancelButtonText: 'Отменить',
                confirmButtonText: "Да, удалить!",
                reverseButtons: true,
                customClass: {
                    confirmButton: "btn btn-danger btn-lg",
                    cancelButton: "btn btn-outline-secondary btn-lg"
                }
            }).then(function (result) {
                if (result.value) {
                    $.ajax({
                        type: 'POST',
                        url: '/ajax/personal/delete-personal',
                        data: {
                            'id': id,
                            'cwId': cwId,
                        },
                        success: function (data) {
                            personalBlock.hide(400);
                            toastr.success("Пользователь удалён");
                        }
                    });
                }
            });

        })
    });

</script>
