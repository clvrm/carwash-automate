<?php

use app\models\ar\Materials;
use yii\helpers\Html;

/**
 * @var Materials $model
 */
?>
<div class="row">
    <div class="col-lg-6">
        <div class="mt-4 form-group">
            <label>Название материала</label>
            <?= Html::activeInput('text', $model, 'name', ['class' => 'form-control',
                'placeholder' => 'Введите название материала', 'required' => true]) ?>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="mt-4 form-group">
            <label>Стоимость</label>
            <?= Html::activeInput('number', $model, 'price', ['class' => 'form-control',
                'placeholder' => 'Введите стоимость', 'required' => true]) ?>
        </div>
    </div>
</div>

