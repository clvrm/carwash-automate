<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use app\models\ar\personal\PersonalLog;
use yii\helpers\Html;

$this->title = $name;

$this->params['main-data'] = [
    'data-log-event' => PersonalLog::ERROR_PAGE,
    'data-log-data' => 'Ошибка: ' . $name,
];
?>

<div class="site-error">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger font-size-md">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <h5>Возникла непредвиденная ошибка</h5>
</div>
