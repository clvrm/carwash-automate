<?php

use yii\helpers\Html;

?>
<div id="kt_header" class="header header-fixed">
    <!--begin::Container-->
    <div class="container d-flex align-items-stretch justify-content-between">
        <!--begin::Header Menu Wrapper-->
        <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
            <!--begin::Header Menu-->
            <div id="kt_header_menu" class="header-menu header-menu-mobile header-menu-layout-default">
                <!--begin::Header Nav-->
                <div class="header-page-title">
                    <?= Html::encode($this->title) ?? ''?>
                    <?php if (isset($this->params['header_subtitle'])) : ?>
                        <?= $this->params['header_subtitle'] ?? '' ?>
                    <?php endif; ?>
                </div>
                <!--end::Header Nav-->
            </div>
            <!--end::Header Menu-->
        </div>
        <!--end::Header Menu Wrapper-->
        <!--begin::Topbar-->
        <div class="topbar">
            <!--begin::User-->
            <div class="topbar-item">
                <?php if (isset($this->params['header_button'])) : ?>
                    <?= $this->params['header_button'] ?? '' ?>
                <?php endif; ?>
            </div>
            <!--end::User-->
        </div>
        <!--end::Topbar-->
    </div>
    <!--end::Container-->
</div>
