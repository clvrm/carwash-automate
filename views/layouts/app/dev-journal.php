<?php

/* @var $this \yii\web\View */

/* @var $content string */

use app\assets\AuthAppAsset;
use app\assets\BaseAppAsset;
use app\assets\DevJournalAsset;
use app\assets\JournalAsset;
use app\widgets\Alert;
use app\widgets\helpers\SVG;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

DevJournalAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="ru">
<!--begin::Head-->
<head>
    <base href="">
    <meta charset="utf-8"/>
    <meta name="description" content="---"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="canonical" href="https://keenthemes.com/metronic"/>
    <!--end::Layout Themes-->
    <link rel="shortcut icon" href="/media/logos/favicon.ico"/>
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<!--end::Head-->
<!--begin::Body-->
<body id="kt_body"
      class="dx-viewport header-fixed header-mobile-fixed aside-enabled aside-fixed aside-minimize-hoverable page-loading">
<?php $this->beginBody() ?>
<!--begin::Main-->
<div class="page-loader page-loader-logo">
    <img alt="Logo" class="max-h-75px" src="/media/logos/logo.png">
    <div class="spinner spinner-primary"></div>
</div>
<!--begin::Header Mobile-->
<?php if (isset($this->params['mobileFixedMenu']) or isset($this->params['mobileFixedMenuBackUrl'])) : ?>
    <?php if (!isset($this->params['mobileFixedMenuActiveId'])) {
        $this->params['mobileFixedMenuActiveId'] = 'not-set';
    }
    if (!isset($this->params['mobileFixedMenu'])) {
        $this->params['mobileFixedMenu'] = [];
    }
    ?>

    <div id="fixed_header_mobile" class="mobile-fixed-menu--wrapper"
         data-default-id="<?= $this->params['mobileFixedMenuActiveId'] ?>"
         data-only-back="<?= $this->params['mobileFixedMenuBackUrl'] ?? 0 ?>">
        <div class="header-mobile align-items-center header-mobile-fixed">
            <div class="header-page-title">
                <?php if (!empty($this->params['mobileFixedMenuBackUrl'])) : ?>
                    <a href="<?= $this->params['mobileFixedMenuBackUrl'] ?? '' ?>">
                        <div class="mobile-fixed-menu-back back-arrow mr-4"></div>
                    </a>
                <?php else: ?>
                    <div class="mobile-fixed-menu-back back-arrow mr-4"></div>
                <?php endif; ?>
                <div class="mobile-fixed-menu--title">

                </div>
            </div>
        </div>
        <div class="mobile-fixed-menu--block">
            <?php
            $mFixedMenu = [];
            function mapCat($item, $key, &$mFixedMenu)
            {
                $mFixedMenu[$item['parent_id']][$key] = $item;
            }

            foreach ($this->params['mobileFixedMenu'] as $key => $item) {
                mapCat($item, $key, $mFixedMenu);
            }
            ?>
            <?php foreach ($mFixedMenu as $parentKey => $fMenu): ?>
                <div class="mobile-fixed-menu"
                    <?php if (!in_array($this->params['mobileFixedMenuActiveId'], array_keys($fMenu))) : ?> style="display: none"
                    <?php endif; ?>
                     <?php if (!empty($parentKey)): ?>data-parent="<?= $parentKey ?>"<?php endif; ?>>
                    <?php foreach ($fMenu as $menuId => $fMenuItem): ?>
                        <div class="mobile-fixed-menu-item" data-url="<?= $fMenuItem['url'] ?? '' ?>"
                             data-id="<?= $menuId ?>"><?= $fMenuItem['title'] ?? '' ?>
                            <span class="fas fa-angle-right"></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>


<div id="kt_header_mobile" class="header-mobile align-items-center header-mobile-fixed">
    <!--begin::Logo-->

    <div class="header-page-title">
        <!--begin::Aside Mobile Toggle-->
        <button class="btn p-0 burger-icon mr-4" id="kt_aside_mobile_toggle">
            <span></span>
        </button>
        <!--end::Aside Mobile Toggle-->
        <?= Html::encode($this->title) ?? '' ?>
        <?php if (isset($this->params['header_subtitle'])) : ?>
            <?= $this->params['header_subtitle'] ?? '' ?>
        <?php endif; ?>
    </div>
    <!--end::Logo-->
    <!--begin::Toolbar-->
    <div class="d-flex align-items-center">

        <!--begin::Topbar Mobile Toggle-->
        <?php if (isset($this->params['header_button'])) : ?>
            <?= $this->params['header_button'] ?? '' ?>
        <?php endif; ?>
        <!--end::Topbar Mobile Toggle-->
    </div>
    <!--end::Toolbar-->
</div>
<!--end::Header Mobile-->
<div class="d-flex flex-column flex-root">
    <!--begin::Page-->
    <div class="d-flex flex-row flex-column-fluid page">
        <!--begin::Aside-->
        <?= $this->render('aside') ?? '' ?>
        <!--end::Aside-->
        <!--begin::Wrapper-->
        <div class="d-flex flex-column flex-row-fluid wrapper
<?php if (isset($this->params['without_header']) and $this->params['without_header']): ?> without-header<?php endif; ?>"
             id="kt_wrapper">
            <!--begin::Header-->
            <div class="jo-page-header d-flex justify-content-between p-4">
                <div class="d-flex align-items-center journal-header-setting-block">
                    <div class="jo-calendar-block d-flex">
                        <div class="jo-calendar--prev-day mr-2">
                            <i class="flaticon2-back-1"></i>
                        </div>
                        <div class="jo-calendar-current-day">
                            Выберите дату
                        </div>
                        <div class="jo-calendar--next-day ml-2">
                            <i class="flaticon2-arrow"></i>
                        </div>
                    </div>
                    <div class="jo-calendar-open ml-2 d-none d-lg-block">
                        <i class="flaticon-event-calendar-symbol" style="color: #3983efd9;"></i>
                    </div>
                    <div class="position-relative">
                        <input id="joDatepickerInput" type="text" class="form-control" readonly/>
                    </div>
                    <div class="ml-6 jo-calendar-interval dropdown">
                        <div class="d-flex  dropdown-toggle" type="button" id="dropdownMenuButton"
                             data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="flaticon2-soft-icons mr-2"></i>
                            <span class="jo-current-interval">1 час</span>
                            <i class="flaticon2-down ml-2 font-size-xs" style="margin-top: 3px;"></i>
                            <div class="dropdown-menu jo-interval-dropdown" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item changeJournalDataCell" data-duration="5" href="#">5 минут</a>
                                <a class="dropdown-item changeJournalDataCell" data-duration="15" href="#">15 минут</a>
                                <a class="dropdown-item changeJournalDataCell" data-duration="30" href="#">30 минут</a>
                                <a class="dropdown-item changeJournalDataCell" data-duration="60" href="#">1 час</a>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="d-flex">
                    <a href="/orders/info">
                        <div class="btn btn-lg btn-outline-primary d-none d-lg-block">
                            <i class="la la-info"></i>
                            Информация
                        </div>
                    </a>
                    <a href="/orders/auto-create">
                        <div class="ml-4 btn btn-lg btn-primary d-none d-lg-block">
                            <i class="flaticon2-plus"></i>
                            Создать запись
                        </div>
                    </a>
                    <div class="journal-mobile-info-button ml-4 btn btn-sm px-6 btn-primary d-flex d-lg-none">
                        <i class="fas fa-info p-0"></i>
                    </div>
                </div>
            </div>
            <!--end::Header-->
            <!--begin::Content-->
            <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
                <!--begin::Entry-->
                <div class="d-flex flex-column-fluid">
                    <!--begin::Container-->
                    <div id="contentWrapper" class="container">
                        <?= Alert::widget([
                            'options' => ['class' => 'text-center']]) ?>
                        <?= $content ?? '' ?>

                    </div>
                    <!--end::Container-->
                </div>
                <!--end::Entry-->
            </div>
            <!--end::Content-->
            <!--begin::Footer-->
            <?= $this->render('footer'); ?>
            <!--end::Footer-->
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Page-->
</div>
<!--end::Main-->


<!--begin::Scrolltop-->
<div id="kt_scrolltop" class="scrolltop">
			<span class="svg-icon">
				<!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Up-2.svg-->
				<?= SVG::getIcon('Up-2') ?>
                <!--end::Svg Icon-->
			</span>
</div>
<!--end::Scrolltop-->

<script>var HOST_URL = "https://preview.keenthemes.com/metronic/theme/html/tools/preview";</script>
<!--begin::Global Config(global config for global JS scripts)-->
<script>var KTAppSettings = {
        "breakpoints": {"sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400},
        "colors": {
            "theme": {
                "base": {
                    "white": "#ffffff",
                    "primary": "#3699FF",
                    "secondary": "#E5EAEE",
                    "success": "#1BC5BD",
                    "info": "#8950FC",
                    "warning": "#FFA800",
                    "danger": "#F64E60",
                    "light": "#E4E6EF",
                    "dark": "#181C32"
                },
                "light": {
                    "white": "#ffffff",
                    "primary": "#E1F0FF",
                    "secondary": "#EBEDF3",
                    "success": "#C9F7F5",
                    "info": "#EEE5FF",
                    "warning": "#FFF4DE",
                    "danger": "#FFE2E5",
                    "light": "#F3F6F9",
                    "dark": "#D6D6E0"
                },
                "inverse": {
                    "white": "#ffffff",
                    "primary": "#ffffff",
                    "secondary": "#3F4254",
                    "success": "#ffffff",
                    "info": "#ffffff",
                    "warning": "#ffffff",
                    "danger": "#ffffff",
                    "light": "#464E5F",
                    "dark": "#ffffff"
                }
            },
            "gray": {
                "gray-100": "#F3F6F9",
                "gray-200": "#EBEDF3",
                "gray-300": "#E4E6EF",
                "gray-400": "#D1D3E0",
                "gray-500": "#B5B5C3",
                "gray-600": "#7E8299",
                "gray-700": "#5E6278",
                "gray-800": "#3F4254",
                "gray-900": "#181C32"
            }
        },
        "font-family": "Poppins"
    };</script>
<!--end::Global Config-->
<!--end::Page Scripts-->
<?php $this->endBody() ?>

</body>
<!--end::Body-->
</html>
<?php $this->endPage() ?>
