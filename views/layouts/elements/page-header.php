<?php

use yii\helpers\Html;

$washUrl = '#';
$detailingUrl = '#';
switch ($menu) {
    case 'complex':
        $washUrl = '/complex/wash';
        $detailingUrl = '/complex/detail';
        break;
    case 'service':
        $washUrl = '/service/wash';
        $detailingUrl = '/service/detail';
        break;
    case 'material':
        $washUrl = '/material/wash';
        $detailingUrl = '/material/detail';
        break;
}

?>

<div id="pageHeaderElem" class="row page-header-elem">
    <div class="col-lg-6">
        <ul class="nav justify-content-start nav-dark nav-bold nav-tabs-line nav-a-underline mb-2 mt--4">
            <li class="nav-item">
                <a class="nav-link <?php if (!$is_detailing): ?>active<?php endif; ?>" href="<?= $washUrl ?>">Мойка</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php if ($is_detailing): ?>active<?php endif; ?>" href="<?= $detailingUrl ?>">Дитейлинг</a>
            </li>
        </ul>
        <ul class="nav justify-content-start nav-dark nav-bold nav-tabs-line nav-a-underline mb-6">
            <?php if ($is_detailing): ?>
                <li class="nav-item">
                    <a class="nav-link <?php if ($menu == 'complex'): ?>active<?php endif; ?>" href="/complex/detail">Комплексы</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($menu == 'service'): ?>active<?php endif; ?>" href="/service/detail">Услуги</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($menu == 'material'): ?>active<?php endif; ?>"
                       href="/material/detail">Материалы</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link <?php if ($menu == 'complex'): ?>active<?php endif; ?>" href="/complex/wash">Комплексы</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($menu == 'service'): ?>active<?php endif; ?>" href="/service/wash">Услуги</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php if ($menu == 'material'): ?>active<?php endif; ?>"
                       href="/material/wash">Материалы</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="col-lg-6">
        <?php if (isset($tabs)) : ?>
            <div class="page-header-switch p-6 pr-0 mb-6 bg-white d-flex justify-content-between">
                <?php foreach ($tabs as $key => $tab): ?>
                    <div <?= isset($tab['id'])?'id="'.$tab['id'].'"' : ''?> class="d-block">
                        <div class="header-tab-subtitle <?= (isset($tab['active']) and $tab['active']) ? 'text-primary' : 'text-muted' ?>">
                            <?= $tab['subtitle'] ?? ''; ?>
                        </div>
                        <a class="header-tab-title text-dark" href="<?= $tab['link'] ?? '' ?>">
                            <div class="font-size-h6"><?= $tab['title'] ?? '' ?></div>
                        </a>
                    </div>
                    <?php if (!array_key_last($tabs) == $key): ?>
                        <div class="page-header-switch--divider"></div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="col-12">
        <div class="page-header-elem--head p-6 pr-0 mb-6 bg-white d-flex justify-content-between">
            <div class="header-page-title">
                <?= Html::encode($this->title) ?? '' ?>
                <?php if (isset($question)): ?>
                    <div class="ml-2 header-page-question">
                        <i class="far fa-question-circle p-1" data-toggle="popover" title="<?= $questionTitle ?? 'Подсказка'?>"
                           data-html="true"
                           data-content="<?= $question ?? '' ?>"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="topbar-item">
                <?php if (isset($this->params['header_button'])) : ?>
                    <?= $this->params['header_button'] ?? '' ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

