<?php

use app\models\ar\personal\Personal;
use app\models\ar\rbac\AuthItem;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Редактирование сотрудника';

/**
 * @var Personal $model
 * @var array $permissions
 * @var bool $isPersonalOwner
 */

?>
<?php $form = \yii\widgets\ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
]); ?>
<div class="row">
    <?php $errors = $form->errorSummary([$model]);
    if (!empty($model->errors)) : ?>
        <div class="col-12">
            <div class="alert alert-danger" role="alert">
                <?= $errors ?? '' ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="col-lg-12">
        <div id="personalEdit" class="card card-custom  gutter-b">
            <?php if ($isPersonalOwner): ?>
                <div class="alert alert-primary text-center" role="alert">
                    Вы редактируете владельца автомойки. Независимо от настроек у владельца всегда есть доступ к
                    страницам работы с персоналом
                </div>
            <?php endif; ?>
            <div class="card-body">

                <div class="row">
                    <div class="col-lg-5">
                        <h5>Данные для входа</h5>
                        <div class="form-group">
                            <label>Email сотрудника</label>
                            <?= Html::input('text', 'email', $model->user->email ?? '', ['class' => 'form-control',
                                'placeholder' => 'Введите Email сотрудника', 'required' => false, 'disabled' => true]) ?>
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
                                'placeholder' => 'Введите зарплату','max'=>99999999,'min'=> 0, 'required' => false]) ?>
                        </div>
                    </div>
                    <div class="offset-lg-1 col-lg-6">
                        <h5>Должность и возможности</h5>
                        <div class="form-group">
                            <label for="postSelector">Должность</label>
                            <select class="form-control default-select2" name="Personal[post]" id="postSelector">
                                <?= Html::renderSelectOptions($model->post, [
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
                                <?= Html::checkbox('Perm[' . AuthItem::PERM_RESPOND_REVIEWS . ']',
                                    in_array(AuthItem::PERM_RESPOND_REVIEWS, $permissions, true), [
                                        'id' => 'cbRespondReviews',
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group row mb-0 person-permission-checkbox-group">
                            <label class="col-10 col-form-label">Редактировать прайс-лист</label>
                            <div class="col-2 col-form-label">
                                <?= Html::checkbox('Perm[' . AuthItem::PERM_EDIT_PRICELIST . ']',
                                    in_array(AuthItem::PERM_EDIT_PRICELIST, $permissions, true), [
                                        'id' => 'cbEditPrice',
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group row mb-0 person-permission-checkbox-group">
                            <label class="col-10 col-form-label">Изменять настройки записи</label>
                            <div class="col-2 col-form-label">
                                <?= Html::checkbox('Perm[' . AuthItem::PERM_CHANGE_RECORD_SETTING . ']',
                                    in_array(AuthItem::PERM_CHANGE_RECORD_SETTING, $permissions, true), [
                                        'id' => 'cbChangeRecordSettings',
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group row mb-0 person-permission-checkbox-group">
                            <label class="col-10 col-form-label">Создавать рассылку</label>
                            <div class="col-2 col-form-label">
                                <?= Html::checkbox('Perm[' . AuthItem::PERM_CREATE_MAILING . ']',
                                    in_array(AuthItem::PERM_CREATE_MAILING, $permissions, true), [
                                        'id' => 'cbCreateMailing',
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group row mb-0 person-permission-checkbox-group">
                            <label class="col-10 col-form-label">Создавать / редактировать скидки</label>
                            <div class="col-2 col-form-label">
                                <?= Html::checkbox('Perm[' . AuthItem::PERM_CREATE_EDIT_SALES . ']',
                                    in_array(AuthItem::PERM_CREATE_EDIT_SALES, $permissions, true), [
                                        'id' => 'cbEditSales',
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group row mb-0 person-permission-checkbox-group">
                            <label class="col-10 col-form-label">Создавать / редактировать заказы</label>
                            <div class="col-2 col-form-label">
                                <?= Html::checkbox('Perm[' . AuthItem::PERM_CREATE_EDIT_ORDERS . ']',
                                    in_array(AuthItem::PERM_CREATE_EDIT_ORDERS, $permissions, true), [
                                        'id' => 'cbEditOrders',
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group row mb-0 person-permission-checkbox-group">
                            <label class="col-10 col-form-label">Закрывать заказы</label>
                            <div class="col-2 col-form-label">
                                <?= Html::checkbox('Perm[' . AuthItem::PERM_CLOSE_ORDERS . ']',
                                    in_array(AuthItem::PERM_CLOSE_ORDERS, $permissions, true), [
                                        'id' => 'cbCloseOrder',
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group row mb-0 person-permission-checkbox-group">
                            <label class="col-10 col-form-label">Редактировать персонал</label>
                            <div class="col-2 col-form-label">
                                <?= Html::checkbox('Perm[' . AuthItem::PERM_EDIT_PERSONAL . ']', ($isPersonalOwner ||
                                    in_array(AuthItem::PERM_EDIT_PERSONAL, $permissions, true)), [
                                    'id' => 'cbEditPersonal',
                                    'disabled' => $isPersonalOwner, // Если пользователь владелец - то у него всегда должен быть доступ к странице персонала
                                    'label' => '<span></span>',
                                    'labelOptions' => ['class' => 'checkbox']
                                ]) ?>
                            </div>
                        </div>
                        <div class="form-group row mb-0 person-permission-checkbox-group">
                            <label class="col-10 col-form-label">Просматривать аналитику</label>
                            <div class="col-2 col-form-label">
                                <?= Html::checkbox('Perm[' . AuthItem::PERM_VIEW_ANALYTICS . ']',
                                    in_array(AuthItem::PERM_VIEW_ANALYTICS, $permissions, true), [
                                        'id' => 'cbViewAnalytics',
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                        <div class="form-group row mb-0 person-permission-checkbox-group">
                            <label class="col-10 col-form-label">Редактировать информацию об
                                автомойке</label>
                            <div class="col-2 col-form-label">
                                <?= Html::checkbox('Perm[' . AuthItem::PERM_EDIT_CARWASH_INFO . ']',
                                    in_array(AuthItem::PERM_EDIT_CARWASH_INFO, $permissions, true), [
                                        'id' => 'cbEditCw',
                                        'label' => '<span></span>',
                                        'labelOptions' => ['class' => 'checkbox']
                                    ]) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-6">
                    <div class="col-12">
                        <div class="text-right">
                            <a href="/personal/index">
                                <button type="button" class="btn btn-lg btn-outline-primary mr-2">Закрыть</button>
                            </a>
                            <?php if ($model->is_approved): ?>
                                <button type="submit" class="btn btn-lg btn-primary">
                                    Изменить
                                </button>
                            <?php else : ?>
                                <button type="submit" class="btn btn-lg btn-primary">
                                    <i class="fas fa-retweet"></i>Отправить запрос заново
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

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
                'selected': [
                    'cbEditCw', 'cbEditOrders', 'cbCloseOrder', 'cbEditPersonal', 'cbViewAnalytics', 'cbRespondReviews',
                    'cbEditPrice', 'cbChangeRecordSettings', 'cbCreateMailing', 'cbEditSales'
                ],
                'disabled': []
            },
        };
        let postValue = $('#postSelector').val();
        let currentCheckboxPreset = assignedCheckboxes[postValue];
        $.each(currentCheckboxPreset.disabled, function (index, item) {
            let itemBlock = $('#' + item).closest('.person-permission-checkbox-group');
            $(itemBlock).find('label.col-form-label').addClass('text-muted');
            let checkbox = $(itemBlock).find('label.checkbox');
            checkbox.addClass('checkbox-disabled');
            checkbox.find('input').prop('disabled', true)
        });

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