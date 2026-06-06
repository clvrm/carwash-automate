<?php

use app\models\ar\documentation\Documentation;
use app\models\ar\documentation\DocumentationCategory;

$this->title = 'Инструкции';

/**
 * @var DocumentationCategory $categories
 * @var DocumentationCategory $subCategories
 * @var int $currentCategory
 */


$this->params['mobileFixedMenu'] = [
    '1' => ['title' => 'Журнал', 'parent_id' => null, 'url' => '/123'],
    '2' => ['title' => 'Инфо', 'parent_id' => null, 'url' => '/123'],
    '3' => ['title' => 'Дата', 'parent_id' => null, 'url' => '/123'],
    '40' => ['title' => '1', 'parent_id' => 1, 'url' => '/123'],
    '50' => ['title' => '2', 'parent_id' => 1, 'url' => '/123'],
    '600' => ['title' => '3', 'parent_id' => 40, 'url' => '/123'],
    '700' => ['title' => '4', 'parent_id' => 40, 'url' => '/123'],
];
$this->params['mobileFixedMenuActiveId'] = 600;
?>

<div class="row">
    <div class="col-12">
        <ul class="nav justify-content-start nav-dark nav-bold nav-tabs-line nav-a-underline mb-2 mt--4">
            <?php foreach ($categories as $category): ?>
                <li class="nav-item">
                    <a class="nav-link <?= $category->id == $currentCategory ? 'active' : '' ?>"
                       href="/documentation?category=<?= $category->id ?>"><?= $category->title ?? '---' ?></a>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if (!empty($subCategories)): ?>
            <ul class="nav justify-content-start nav-dark nav-bold nav-tabs-line nav-a-underline">
                <?php foreach ($subCategories as $key => $subCategory): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= array_key_first($subCategories) == $key ? 'active' : '' ?>"
                           data-toggle="tab" href="#tab<?= $subCategory->id ?>" role="tab"
                           aria-selected="false"><?= $subCategory->title ?? '---' ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (!empty($subCategories)): ?>
            <div class="tab-content" id="myTabContent">
                <?php foreach ($subCategories as $subCategoryKey => $subCategory): ?>
                    <?php $documentations = Documentation::find()->where(['category_id' => $subCategory->id])->orderBy('position ASC')->all(); ?>
                    <div class="tab-pane fade <?= $subCategoryKey == array_key_first($subCategories) ? 'show active' : '' ?>"
                         id="tab<?= $subCategory->id ?>" role="tabpanel" aria-labelledby="home-tab">
                        <div class="card card-custom mt-4">
                            <div class="card-body">
                                <div class="row">
                                    <?php foreach ($documentations as $documentation): ?>
                                        <div class="col-sm-12 <?= count($documentations) > 1 ? 'col-md-6' : '' ?>">
                                            <h3><?= $documentation->title ?? '' ?></h3>
                                            <div class="documentation-video">
                                                <?= $documentation->video ?? ''?>
                                            </div>
                                            <div class="documentation-text">
                                                <?= $documentation->text ?? ''?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <?php $documentations = Documentation::find()->where(['category_id' => $currentCategory])->orderBy('position ASC')->all(); ?>
            <div class="card card-custom mt-4">
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($documentations as $documentation): ?>
                            <div class="col-sm-12 <?= count($documentations) > 1 ? 'col-md-6' : '' ?>">
                                <h3><?= $documentation->title ?? '' ?></h3>
                                <div class="documentation-video">
                                    <?= $documentation->video ?? ''?>
                                </div>
                                <div class="documentation-text">
                                    <?= $documentation->text ?? ''?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

</div>
