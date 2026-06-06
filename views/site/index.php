<?php

/* @var $this yii\web\View */

echo 'PersonID: ' . \Yii::$app->user->pid . '<br>';
echo 'CarwashID: ' . \Yii::$app->user->cwid . '<br>';

$this->title = 'Журнал';
$this->params['without_header'] = true;
$this->params['header_button'] = '<div class="d-block d-lg-none create-new-order-journal-button">
     <a href="/orders/auto-create">
     <button class="btn btn-primary px-6">+ Создать запись</button>
     </a>
    </div>';
?>

<div class="table-wrapper">
    <div id="tableJs" data-cw-id="<?= Yii::$app->user->identity->getCWid() ?? 0 ?>"
         data-p-id="<?= Yii::$app->user->identity->getPId() ?? 0 ?>"></div>
</div>
<script>


</script>

<style>
    /* В СЛУЧАЕ, ЕСЛИ ОТКЛЮЧАЕМ КРОСС-СКРОЛЛИНГ ! ! ! */
    /*div#kt_content{*/
    /*    overflow: auto;*/
    /*}*/
    /*.table-wrapper #tableJs {*/
    /*    width: 1152px;*/
    /*    overflow: scroll;*/
    /*}*/
    /*.dx-scheduler-date-table-scrollable .dx-scrollable-content{*/
    /*    overflow: auto;*/
    /*}*/
</style>