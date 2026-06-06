<?php

use app\models\ar\personal\Personal;

/**
 * @var int $pId
 * @var Personal[] $accounts
 */

?>
<div id="profileAccounts" class="card card-custom  gutter-b">
    <div class="card-body">
        <div class="row">
            <div class="col-6">
                <h4 class="text-muted">АККАУНТЫ</h4>
            </div>
            <div class="col-6 text-right">
<!--                <button class="btn btn-outline-primary px-6">Добавить</button>-->
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
                <?php foreach ($accounts as $account) : ?>
                    <?php
                    $carwashImage = $account->carwash->avatar ?? '/media/service/service-auth-logo.png';
                    $carwashName = $account->carwash->name ?? 'Без названия';
                    $personalPost = Personal::getPostLabel($account->post) ?? 'Работник';
                    ?>
                    <div class="d-flex cursor-pointer align-items-center row <?= $pId == $account->id ? 'active-profile' : '' ?>
                     profile-account-item" data-url="/auth/switch-account?cPId=<?= $pId ?>&sPId=<?= $account->id ?>">
                        <div class="col-2">
                            <div class="text-center image-input-circle">
                                <div style="background-image: url(<?= $carwashImage ?>);background-size: cover;
                                        border: 2px solid #0260e8;" class="img-fluid rounded-circle h-40px w-40px">
                                </div>
                            </div>
                        </div>
                        <div class="col-6 d-flex">
                            <div class="<?= $pId == $account->id ? 'text-primary' : 'text-dark' ?> mr-2 font-weight-bold">
                                <?= $carwashName ?? '' ?>
                            </div>
                            <?php if ($pId == $account->id): ?>
                                <i class="fas fa-check-circle text-primary"></i>
                            <?php endif; ?>
                        </div>
                        <div class="col-4">
                            Управляющий
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

