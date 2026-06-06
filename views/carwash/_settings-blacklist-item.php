<?php

use app\models\ar\carwash\CarwashBlacklist;

/**
 * @var CarwashBlacklist $model
 */
?>

<div class="setting-blacklist--item form-group row align-items-center" data-id="<?= $model->id ?>"
     data-cl-id="<?= $model->client_id ?>">
    <label class="col-5"><?= $model->car_number ?? '' ?> - <?= $model->car_region ?? '' ?></label>
    <div class="col-7 text-primary text-right removeItemFromBlacklist">
        Удалить из списка
    </div>
</div>
