<?php
/**
 * @var CarwashComfort $comfort
 */

use app\models\ar\carwash\CarwashComfort;
use yii\helpers\Html;

?>
<div id="carwashClientComfort" class="card card-custom gutter-b">
    <div class="card-body">
        <h4 class="text-muted">КОМФОРТ КЛИЕНТА</h4>

        <div class="row mt-8">
            <div class="col-lg-4">
                <div class="checkbox-inline pb-4">
                    <?= Html::activeCheckbox($comfort, 'cf_ATM', [
                        'uncheck' => 0,
                        'label' => '<span></span>Банкомат',
                        'labelOptions' => ['class' => 'checkbox']
                    ]) ?>
                </div>
                <div class="checkbox-inline pb-4">
                    <?= Html::activeCheckbox($comfort, 'cf_toilet', [
                        'uncheck' => 0,
                        'label' => '<span></span>Туалет',
                        'labelOptions' => ['class' => 'checkbox']
                    ]) ?>
                </div>
                <div class="checkbox-inline pb-4">
                    <?= Html::activeCheckbox($comfort, 'cf_coffee', [
                        'uncheck' => 0,
                        'label' => '<span></span>Кофемашина',
                        'labelOptions' => ['class' => 'checkbox']
                    ]) ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="checkbox-inline pb-4">
                    <?= Html::activeCheckbox($comfort, 'cf_postomat', [
                        'uncheck' => 0,
                        'label' => '<span></span>Постомат',
                        'labelOptions' => ['class' => 'checkbox']
                    ]) ?>
                </div>
                <div class="checkbox-inline pb-4">
                    <?= Html::activeCheckbox($comfort, 'cf_shop', [
                        'uncheck' => 0,
                        'label' => '<span></span>Магазин',
                        'labelOptions' => ['class' => 'checkbox']
                    ]) ?>
                </div>
                <div class="checkbox-inline pb-4">
                    <?= Html::activeCheckbox($comfort, 'cf_TV', [
                        'uncheck' => 0,
                        'label' => '<span></span>Телевизор',
                        'labelOptions' => ['class' => 'checkbox']
                    ]) ?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="checkbox-inline pb-4">
                    <?= Html::activeCheckbox($comfort, 'cf_cafe', [
                        'uncheck' => 0,
                        'label' => '<span></span>Кафе',
                        'labelOptions' => ['class' => 'checkbox']
                    ]) ?>

                </div>
                <div class="checkbox-inline pb-4">

                    <?= Html::activeCheckbox($comfort, 'cf_rest_zone', [
                        'uncheck' => 0,
                        'label' => '<span></span>Зона отдыха',
                        'labelOptions' => ['class' => 'checkbox']
                    ]) ?>
                </div>
                <div class="checkbox-inline pb-4">
                    <?= Html::activeCheckbox($comfort, 'cf_videocam', [
                        'uncheck' => 0,
                        'label' => '<span></span>Видеонаблюдение',
                        'labelOptions' => ['class' => 'checkbox']
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="carwashClientPayments" class="card card-custom gutter-b">
    <div class="card-body">
        <h4 class="text-muted">СПОСОБЫ ОПЛАТЫ</h4>

        <div class="row mt-8">
            <div class="col-lg-4">
                <div class="checkbox-inline pb-4">
                    <?= Html::activeCheckbox($comfort, 'pay_cash', [
                        'uncheck' => 0,
                        'label' => '<span></span>Наличные',
                        'labelOptions' => ['class' => 'checkbox']
                    ]) ?>
                </div>
                <div class="checkbox-inline pb-4">
                    <?= Html::activeCheckbox($comfort, 'pay_online', [
                        'uncheck' => 0,
                        'label' => '<span></span>Онлайн',
                        'labelOptions' => ['class' => 'checkbox']
                    ]) ?>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="checkbox-inline pb-4">
                    <?= Html::activeCheckbox($comfort, 'pay_terminal', [
                        'uncheck' => 0,
                        'label' => '<span></span>Картой через терминал',
                        'labelOptions' => ['class' => 'checkbox']
                    ]) ?>
                </div>
                <div class="checkbox-inline pb-4">
                    <?= Html::activeCheckbox($comfort, 'pay_invoice', [
                        'uncheck' => 0,
                        'label' => '<span></span>Безналичный расчет (юр. лица)',
                        'labelOptions' => ['class' => 'checkbox']
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>
