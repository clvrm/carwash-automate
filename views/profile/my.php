<?php

use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalNotification;
use app\models\ar\Users;
use app\models\forms\profile\ProfileContactsForm;
use app\models\forms\profile\ProfileNotificationsForm;
use yii\widgets\ActiveForm;

$this->title = 'Профиль';
$this->params['header_button'] = '<div class="d-block mr-12">
     <button class="btn btn-primary px-6 save-profile-button">Сохранить</button>
    </div>';
$this->params['mobileFixedMenu'] = [
    '1' => ['title' => 'Личные данные', 'parent_id' => null, 'url' => '#profilePersonal'],
    '2' => ['title' => 'Контакты', 'parent_id' => null, 'url' => '#profileContacts'],
    '3' => ['title' => 'Уведомления', 'parent_id' => null, 'url' => '#profileNotificationsBlock'],
];


/**
 * @var int $pId
 * @var Users $user
 * @var Personal[] $accounts
 * @var ProfileContactsForm $contacts
 * @var ProfileNotificationsForm $notify
 */


?>
<?php $form = \yii\widgets\ActiveForm::begin([
    'id' => 'myProfileForm',
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>
<div class="row">
    <div class="col-lg-5">
        <?= $this->render('_my-main', [
            'user' => $user
        ]) ?>

        <?= $this->render('_my-contacts', [
            'contacts' => $contacts,
            'user' => $user
        ]) ?>

    </div>
    <div id="profileNotificationsBlock" class="col-lg-7">
        <?= $this->render('_my-accounts', [
            'accounts' => $accounts,
            'pId' => $pId
        ]) ?>


        <?= $this->render('_my-notification', [
            'notify' => $notify
        ]) ?>

    </div>
</div>
<?php ActiveForm::end(); ?>

<script>
    $(document).ready(function () {
        $('.save-profile-button').on('click', function () {
            if ($('#myProfileForm')[0].checkValidity()) {
                $('#myProfileForm').submit();
            } else {
                $('#myProfileForm')[0].reportValidity();
            }
        });

        $('.profile-account-item').on('click', function () {
            if ($(this).hasClass('active-profile')) {
                toastr.success("Данный профиль уже активен");
                return true;
            }
            toastr.success("Переключаем аккаунт");

            let url = $(this).data('url');
            window.location.href = url;

        });
    })
</script>
