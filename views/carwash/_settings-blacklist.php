<?php

use app\models\ar\carwash\CarwashBlacklist;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/**
 * @var ActiveDataProvider $blacklistDataProvider
 */

?>

<div id="carwashSettingsBlacklist" class="card card-custom  gutter-b">
    <?php Pjax::begin(['id' => 'blacklist']); ?>
    <div class="card-body">
        <h4 class="text-muted">ЧЁРНЫЙ СПИСОК
            <i class="far fa-question-circle p-1" data-toggle="popover" title="Черный список"
               data-html="true"
               data-content="Список клиентов, нарушивших правила обслуживания. "></i>
        </h4>
        <div class="row mt-4">
            <div class="col-12">
                <div class="form-group row align-items-center">
                    <div class="col-12">
                        <label>Поиск по номеру</label>
                    </div>
                    <div class="col-7">
                        <input id="blacklistSearchCarNumber" type="text"
                               maxlength="6" <?= !empty($_GET['car_number']) ? ' value=' . $_GET['car_number'] : '' ?>
                               name="car_number" class="form-control" placeholder="о000оо"/>
                    </div>
                    <div class="col-5">
                        <input id="blacklistSearchCarRegion" type="number"
                            <?= !empty($_GET['car_region']) ? ' value=' . $_GET['car_region'] : '' ?>
                               name="car_region" class="form-control"
                               placeholder="000"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="carwash-setting-blacklist">

                    <?= ListView::widget([
                        'dataProvider' => $blacklistDataProvider,
                        'itemView' => '_settings-blacklist-item',
                        'summary' => false,
                        'pager' => [
                            'disabledListItemSubTagOptions' => [
                                'tag' => 'a',
                                'class' => 'btn btn-icon btn-sm btn-light-primary disabled mr-2 my-1',
                            ],
                            'firstPageLabel' => '<i class="ki ki-bold-double-arrow-back icon-xs"></i>',
                            'lastPageLabel' => '<i class="ki ki-bold-double-arrow-next icon-xs"></i>',
                            'prevPageLabel' => '<i class="ki ki-bold-arrow-back icon-xs"></i>',
                            'nextPageLabel' => '<i class="ki ki-bold-arrow-next icon-xs"></i>',
                            'options' => [
                                'tag' => 'div',
                                'class' => 'd-flex flex-wrap py-2 mr-3 list-pager',
                            ],
                            'maxButtonCount' => 3,
                            'linkContainerOptions' => [
                                'tag' => 'div',
                                'class' => false,
                            ],
                            'linkOptions' => [
                                'tag' => 'a',
                                'class' => 'btn btn-icon btn-sm btn-light-primary mr-2 my-1',
                            ],
                        ],
                        'emptyText' => 'Нет записей в черном списке'
                    ]);
                    ?>

                </div>
            </div>
        </div>
    </div>
    <?php Pjax::end(); ?>
</div>