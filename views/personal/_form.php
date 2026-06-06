<?php

use app\models\ar\personal\Personal;
use app\models\ar\rbac\AuthItem;
use yii\helpers\Html;


/**
 * @var Personal $model
 */
?>


<div class="row">
    <div class="col-lg-5">
        <h5>Данные для входа</h5>
        <div class="form-group">
            <label>Email сотрудника</label>
            <?php if ($model->user->email): ?>
                <?= Html::input('text', 'email', $model->user->email ?? '', ['class' => 'form-control',
                    'placeholder' => 'Введите Email сотрудника', 'required' => true, 'disabled' => true
                ]) ?>
            <?php else: ?>
                <?= Html::input('text', 'email', null, ['class' => 'form-control',
                    'placeholder' => 'Введите Email сотрудника', 'required' => true,
                ]) ?>
            <?php endif; ?>
        </div>


        <h5 class="mt-6">Заработная плата</h5>
        <div class="form-group">
            <label>Вид оплаты</label>
            <select id="salaryTypeSelector" class="form-control" name="Personal[salary_type]">
                <?= Html::renderSelectOptions($model->salary_type, [
                    Personal::SALARY_TYPE_NONE => 'Не учитывать',
                    Personal::SALARY_TYPE_PERCENT => 'Процент от заказа',
                    Personal::SALARY_TYPE_FIXED => 'Фиксированная ставка',
                ]) ?>
            </select>
        </div>
        <div class="form-group" <?= $model->salary ?? 'style="display: none"' ?>>
            <label>Сумма</label>
            <?= Html::activeInput('number', $model, 'salary', ['class' => 'form-control',
                'placeholder' => 'Введите зарплату', 'required' => false]) ?>
        </div>
    </div>
    <div class="offset-lg-1 col-lg-6">
        <h5>Должность и возможности</h5>
        <div class="form-group">
            <label for="postSelector">Должность</label>
            <select class="form-control default-select2" name="Personal[post]" id="postSelector">
                <?= Html::renderSelectOptions(Personal::POST_WASHER, [
                    Personal::POST_MANAGER => 'Управляющий',
                    Personal::POST_ADMIN => 'Администратор',
                    Personal::POST_WASHER => 'Мойщик',
                ]) ?>
            </select>
        </div>

        <h4>Этот сотрудник может:</h4>
        <div class="form-group row mb-0 person-permission-checkbox-group">
            <label class="col-10 col-form-label">Отвечать на отзывы</label>
            <div class="col-2 col-form-label">
                <?= Html::checkbox('Perm[' . AuthItem::PERM_RESPOND_REVIEWS . ']', false, [
                    'id' => 'cbRespondReviews',
                    'label' => '<span></span>',
                    'labelOptions' => ['class' => 'checkbox']
                ]) ?>
            </div>
        </div>
        <div class="form-group row mb-0 person-permission-checkbox-group">
            <label class="col-10 col-form-label">Редактировать прайс-лист</label>
            <div class="col-2 col-form-label">
                <?= Html::checkbox('Perm[' . AuthItem::PERM_EDIT_PRICELIST . ']', false, [
                    'id' => 'cbEditPrice',
                    'label' => '<span></span>',
                    'labelOptions' => ['class' => 'checkbox']
                ]) ?>
            </div>
        </div>
        <div class="form-group row mb-0 person-permission-checkbox-group">
            <label class="col-10 col-form-label">Изменять настройки записи</label>
            <div class="col-2 col-form-label">
                <?= Html::checkbox('Perm[' . AuthItem::PERM_CHANGE_RECORD_SETTING . ']', false, [
                    'id' => 'cbChangeRecordSettings',
                    'label' => '<span></span>',
                    'labelOptions' => ['class' => 'checkbox']
                ]) ?>
            </div>
        </div>
        <div class="form-group row mb-0 person-permission-checkbox-group">
            <label class="col-10 col-form-label">Создавать рассылку</label>
            <div class="col-2 col-form-label">
                <?= Html::checkbox('Perm[' . AuthItem::PERM_CREATE_MAILING . ']', false, [
                    'id' => 'cbCreateMailing',
                    'label' => '<span></span>',
                    'labelOptions' => ['class' => 'checkbox']
                ]) ?>
            </div>
        </div>
        <div class="form-group row mb-0 person-permission-checkbox-group">
            <label class="col-10 col-form-label">Создавать / редактировать скидки</label>
            <div class="col-2 col-form-label">
                <?= Html::checkbox('Perm[' . AuthItem::PERM_CREATE_EDIT_SALES . ']', false, [
                    'id' => 'cbEditSales',
                    'label' => '<span></span>',
                    'labelOptions' => ['class' => 'checkbox']
                ]) ?>
            </div>
        </div>
        <div class="form-group row mb-0 person-permission-checkbox-group">
            <label class="col-10 col-form-label">Создавать / редактировать заказы</label>
            <div class="col-2 col-form-label">
                <?= Html::checkbox('Perm[' . AuthItem::PERM_CREATE_EDIT_ORDERS . ']', true, [
                    'id' => 'cbEditOrders',
                    'label' => '<span></span>',
                    'labelOptions' => ['class' => 'checkbox']
                ]) ?>
            </div>
        </div>
        <div class="form-group row mb-0 person-permission-checkbox-group">
            <label class="col-10 col-form-label">Закрывать заказы</label>
            <div class="col-2 col-form-label">
                <?= Html::checkbox('Perm[' . AuthItem::PERM_CLOSE_ORDERS . ']', true, [
                    'id' => 'cbCloseOrder',
                    'label' => '<span></span>',
                    'labelOptions' => ['class' => 'checkbox']
                ]) ?>
            </div>
        </div>
        <div class="form-group row mb-0 person-permission-checkbox-group">
            <label class="col-10 col-form-label text-muted">Редактировать персонал</label>
            <div class="col-2 col-form-label">
                <?= Html::checkbox('Perm[' . AuthItem::PERM_EDIT_PERSONAL . ']', false, [
                    'id' => 'cbEditPersonal',
                    'disabled' => true,
                    'label' => '<span></span>',
                    'labelOptions' => ['class' => 'checkbox checkbox-disabled']
                ]) ?>
            </div>
        </div>
        <div class="form-group row mb-0 person-permission-checkbox-group">
            <label class="col-10 col-form-label text-muted">Просматривать аналитику</label>
            <div class="col-2 col-form-label">
                <?= Html::checkbox('Perm[' . AuthItem::PERM_VIEW_ANALYTICS . ']', false, [
                    'id' => 'cbViewAnalytics',
                    'disabled' => true,
                    'label' => '<span></span>',
                    'labelOptions' => ['class' => 'checkbox checkbox-disabled']
                ]) ?>
            </div>
        </div>
        <div class="form-group row mb-0 person-permission-checkbox-group">
            <label class="col-10 col-form-label text-muted">Редактировать информацию об
                автомойке</label>
            <div class="col-2 col-form-label">
                <?= Html::checkbox('Perm[' . AuthItem::PERM_EDIT_CARWASH_INFO . ']', false, [
                    'id' => 'cbEditCw',
                    'disabled' => true,
                    'label' => '<span></span>',
                    'labelOptions' => ['class' => 'checkbox checkbox-disabled']
                ]) ?>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function () {
        let permList = [
            'cbEditCw', 'cbEditOrders', 'cbCloseOrder', 'cbEditPersonal', 'cbViewAnalytics', 'cbRespondReviews',
            'cbEditPrice', 'cbChangeRecordSettings', 'cbCreateMailing', 'cbEditSales'
        ];

        let assignedCheckboxes = {
            '40': {
                'selected': ['cbCloseOrder', 'cbEditOrders'],
                'disabled': ['cbEditCw', 'cbViewAnalytics', 'cbEditPersonal']
            },
            '30': {
                'selected': ['cbEditOrders', 'cbCloseOrder', 'cbRespondReviews',
                    'cbEditPrice', 'cbChangeRecordSettings', 'cbCreateMailing', 'cbEditSales'],
                'disabled': []
            },
            '20': {
                'selected': ['cbEditCw', 'cbEditOrders', 'cbCloseOrder', 'cbEditPersonal', 'cbViewAnalytics',
                    'cbEditSales'],
                'disabled': []
            },
        };

        $('#postSelector').on('change', function () {
            let postValue = $(this).val();

            let checkboxPreset = assignedCheckboxes[postValue];
            $('.person-permission-checkbox-group').each(function (index, item) {
                $(item).find('label.col-form-label').removeClass('text-muted');
                let checkbox = $(item).find('label.checkbox');
                checkbox.removeClass('checkbox-disabled');
                checkbox.find('input').prop('disabled', false)
                checkbox.find('input').prop('checked', false);
            });

            $.each(checkboxPreset.disabled, function (index, item) {
                let itemBlock = $('#' + item).closest('.person-permission-checkbox-group');
                $(itemBlock).find('label.col-form-label').addClass('text-muted');
                let checkbox = $(itemBlock).find('label.checkbox');
                checkbox.addClass('checkbox-disabled');
                checkbox.find('input').prop('disabled', true)
            });

            $.each(checkboxPreset.selected, function (index, item) {
                let itemBlock = $('#' + item).closest('.person-permission-checkbox-group');
                let checkbox = $(itemBlock).find('label.checkbox');
                checkbox.find('input').prop('checked', true);
            })
        });

        $('#salaryTypeSelector').on('change', function () {
            let salaryBlock = $('#personal-salary').closest('.form-group');
            if ($(this).val() == 0) {
                $(salaryBlock).hide(300);
            } else {
                $(salaryBlock).show(300);
            }

        });


    });

</script>
