<?php

use app\commons\helpers\CarTypeHelper;
use app\models\ar\partner\PartnerStore;
use app\models\ar\partner\PartnerStoreItems;

/**
 * @var string $type
 * @var PartnerStore[] $stores
 */

$this->title = 'Склад';
?>

<div class="row">
    <div class="col-12">
        <ul class="nav justify-content-start nav-dark nav-bold nav-tabs-line nav-a-underline mb-2 mt--4">
            <li class="nav-item">
                <a class="nav-link <?= ($type == 'equipment') ? 'active' : '' ?>" href="/partner-shops/equipment">Оборудование</a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($type == 'materials') ? 'active' : '' ?>" href="/partner-shops/materials">Материалы</a>
            </li>
        </ul>
    </div>
</div>
<div class="row mt-6">
    <?php foreach ($stores as $store) : ?>
        <div class="col-lg-6 col-xl-4">
            <div class="card sales-card card-custom card-stretch gutter-b">
                <div class="card-header">
                    <div class="d-flex flex-wrap justify-content-between align-content-stretch w-100 mt-1">
                        <div class="card-title">
                            <?= $store->title ?? 'Без названия' ?>
                        </div>
                        <div class="card-toolbar">
                            <a href="<?= $store->link ?? '#' ?>">
                                <button class="btn btn-primary"> На сайт <i class="ml-2 fas fa-angle-right"></i>
                                </button>
                            </a>
                        </div>
                    </div>
                    <div class="mt-2 d-flex flex-wrap justify-content-between align-content-stretch w-100">
                        <div class="sales-title-block d-block mb-4 max-h-30px max-w-140px">
                            <img class="img-fluid" src="<?= $store->logo ?? '/media/logos/logo.png' ?>">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    $storeEquipment = PartnerStoreItems::find()
                        ->where(['partner_store_id' => $store->id, 'type' => PartnerStoreItems::TYPE_EQUIPMENT])
                        ->orderBy('position ASC')->all();
                    $storeMaterials = PartnerStoreItems::find()
                        ->where(['partner_store_id' => $store->id, 'type' => PartnerStoreItems::TYPE_MATERIALS])
                        ->orderBy('position ASC')->all();
                    ?>
                    <div class="sales-card-info card-switch-tab">
                        <div class="d-flex flex-wrap justify-content-between align-content-stretch mb-2">
                            <?php if (!empty($storeEquipment)) : ?>
                                <div class="complex-info--item sales-info--post d-block max-w-50">
                                    <div class="text-muted mr-2"> Оборудование:</div>
                                    <div class="material-desc-list">
                                        <ul class="pt-2 pl-5">
                                            <?php foreach ($storeEquipment as $item): ?>
                                                <li class="mb-2 text-muted"><span><?= $item->title ?? '' ?></span></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($storeMaterials)) : ?>
                                <div class="complex-info--item sales-info--post d-block max-w-50 min-w-50">
                                    <div class="text-muted mr-2"> Материалы:</div>
                                    <div class="material-desc-list">
                                        <ul class="pt-2 pl-5">
                                            <?php foreach ($storeMaterials as $item): ?>
                                                <li class="mb-2 text-muted"><span><?= $item->title ?? '' ?></span></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
