<?php

use app\models\ar\Materials;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Редактирование материала';
$this->params['mobileFixedMenuBackUrl'] = '/sales/';

/**
 * @var Materials $model
 */
?>
<?php $form = \yii\widgets\ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>
<div class="row">
    <div class="col-lg-12">
        <div id="personalCreate" class="card card-custom gutter-b">
            <div class="card-body">
                <?= $this->render('_form', ['model' => $model]) ?>
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="text-right">
                            <a href="<?= Yii::$app->request->referrer ?? '/material/' ?>">
                                <button type="button" class="btn btn-lg btn-outline-primary mr-2">Закрыть</button>
                            </a>
                            <button type="submit" class="btn btn-lg btn-primary">Создать</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
