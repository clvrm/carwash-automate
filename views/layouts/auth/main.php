<?php

/* @var $this \yii\web\View */

/* @var $content string */

use app\assets\AuthAppAsset;
use app\assets\BaseAppAsset;
use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AuthAppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="en">
<!--begin::Head-->
<head>
    <base href="">
    <meta charset="utf-8"/>
    <meta name="description" content="---"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <link rel="canonical" href="https://keenthemes.com/metronic"/>
    <!--end::Layout Themes-->
    <link rel="shortcut icon" href="/media/logos/favicon.ico"/>
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_auth_body"
      class="">
<?php $this->beginBody() ?>
<!--begin::Main-->
<!--begin::Header Mobile-->
<div class="container h-100">
    <?= Alert::widget([
            'options' => ['class' => 'mt-10 text-center']]) ?>

    <?= $content ?? ''?>

</div>


<!--end::Demo Panel-->
<?= $this->render('//layouts/_kt_app_settings') ?>
<!--end::Global Config-->
<!--end::Page Scripts-->
<?php $this->endBody() ?>

</body>
<!--end::Body-->
</html>
<?php $this->endPage() ?>
