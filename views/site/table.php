<?php
$this->title = 'Журнал';
$this->params['without_header'] = true;
$this->params['header_button'] = '<div class="d-block d-lg-none create-new-order-journal-button">
     <a href="/orders/auto-create"><button class="btn btn-primary px-6">+ Создать запись</button></a>
    </div>';
?>

<div id="tableWrapperBlockUi" class="table-wrapper">
    <div id="tableJs" data-cw-id="<?= Yii::$app->user->identity->getCWid() ?? 0 ?>"
         data-p-id="<?= Yii::$app->user->identity->getPId() ?? 0 ?>"
         data-can-edit-orders="<?= Yii::$app->user->can('perm_create_edit_orders') ? 1 : 0 ?>"
         data-can-close-orders="<?= Yii::$app->user->can('perm_close_orders') ? 1 : 0 ?>"
    ></div>
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

