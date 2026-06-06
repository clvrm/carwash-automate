<?php

use app\models\ar\carwash\CarwashBlacklist;
use app\models\ar\carwash\CarwashSettings;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var CarwashSettings $settings
 * @var ActiveDataProvider $blacklistDataProvider
 */

$this->title = 'Настройки';
$this->params['header_button'] = '<div class="d-none d-md-block mr-4">
      <div>КОД ДЛЯ КЛИЕНТОВ</div>
      <div class="text-primary font-weight-bold font-size-h4">' . $settings->subscriber_code . '
      </div>
    </div> <div class="d-block">
     <button type="button" class="submitSettingForm btn btn-primary px-6">Сохранить</button>
    </div>';
$this->params['mobileFixedMenu'] = [
    '1' => ['title' => 'Количество постов', 'parent_id' => null, 'url' => '#carwashSettingsPost'],
    '2' => ['title' => 'Черный список', 'parent_id' => null, 'url' => '#carwashSettingsBlacklist'],
    '3' => ['title' => 'Запись', 'parent_id' => null, 'url' => '#carwashSettingsOrder'],
    '4' => ['title' => 'Настройки записи', 'parent_id' => null, 'url' => '#carwashSettingsOrderSettings'],
];


?>
<?php $form = \yii\widgets\ActiveForm::begin([
    'id' => 'settingPageForm',
    'options' => ['enctype' => 'multipart/form-data', 'data-pjax' => true],
]); ?>
<div class="row">
    <div class="col-lg-5">
        <div id="carwashSettingsPost" class="card card-custom  gutter-b">
            <div class="card-body">
                <div class="row">
                    <h4 class="text-muted">КОЛИЧЕСТВО ПОСТОВ</h4>
                    <div class="col-12">
                        <div class="form-group d-flex justify-content-between align-items-center">
                            <label for="postCountSelect">Машиномест на автомойке</label>
                            <select id="postCountSelect" class="form-control default-select2-dropdown"
                                    name="CarwashSettings[post_count]">
                                <?php foreach (range(1, $settings::MAX_POST_COUNT) as $postId) : ?>
                                    <option value="<?= $postId ?>"
                                            <?php if (!empty($settings->post_count) && $settings->post_count == $postId) : ?>selected<?php endif; ?>
                                    ><?= $postId ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?= $this->render('_settings-blacklist.php', [
            'blacklistDataProvider' => $blacklistDataProvider
        ]) ?>
    </div>
    <div class="col-lg-7">
        <div id="carwashSettingsOrder" class="card card-custom  gutter-b">
            <div class="card-body">
                <h4 class="text-muted">ЗАПИСЬ</h4>

                <div class="form-group row align-items-center mt-4">
                    <div class="col-5">
                        Онлайн-запись
                        <i class="far fa-question-circle p-1" data-toggle="popover" title="Онлайн-запись"
                           data-html="true"
                           data-content="Включить/выключить возможность клиентам самостоятельно записываться на услуги автомойки через свое приложение. "></i>
                    </div>
                    <div class="col-7 text-right">
                        <span class="switch switch-icon switch-sm">
                            <?= Html::activeCheckbox($settings, 'online_record', [
                                'uncheck' => 0,
                                'label' => '<span></span>',
                            ]) ?>
                        </span>
                    </div>
                </div>

                <div class="row align-items-center mb-0">
                    <div class="col-6">
                        <div class="form-group mb-0">
                            <div class="checkbox-inline">
                                <?= Html::activeCheckbox($settings, 'only_subscribers', [
                                    'uncheck' => 0,
                                    'label' => '<span></span>Для подписчиков',
                                    'labelOptions' => ['class' => 'checkbox']
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-0">
                            <div class="checkbox-inline">
                                <?= Html::activeCheckbox($settings, 'can_record_blacklist', [
                                    'uncheck' => 0,
                                    'label' => '<span></span>Клиенты из черного списка<i class="far fa-question-circle p-1" data-toggle="popover" title="Клиенты из черного списка"
               data-html="true"
               data-content="Включить/выключить возможность записи на услуги автомойки клиентам из черного списка"></i>',
                                    'labelOptions' => ['class' => 'checkbox']
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row align-items-center mt-4 mb-0">
                    <div class="col-6">
                        <div class="form-group-lg">
                            <label>Глубина записи
                                <i class="far fa-question-circle p-1" data-toggle="popover"
                                   title="Глубина записи "
                                   data-html="true"
                                   data-content="Количествово календарных дней, доступных клиенту для записи на услуги автомойки от текущей даты."></i></label>
                            <?= Html::activeInput('number', $settings, 'max_recording_range', ['class' => 'text-center form-control',
                                'placeholder' => 'количество дней', 'required' => true, 'min' => 1, 'max' => 30, 'step' => 1]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="carwashSettingsOrderSettings" class="card card-custom gutter-b">
            <div class="card-body">
                <h4 class="text-muted">НАСТРОЙКИ ЗАПИСИ</h4>

                <div class="row">
                    <div class="col-lg-6">

                        <div class="form-group row align-items-center mt-4">
                            <div class="col-9">
                                Плотная запись
                                <i class="far fa-question-circle p-1" data-toggle="popover" title="Плотная запись"
                                   data-html="true"
                                   data-content="Алгоритмы будут предлагать клиентам в их приложении время для записи, в зависимости от выбранной услуги для максимальной загрузки заказами каждого поста.<br>
Важно! необходимо указать среднюю длительность обслуживания"></i>
                            </div>
                            <div class="col-3 text-right">
                            <span class="switch switch-icon switch-sm">
                                <?= Html::activeCheckbox($settings, 'dense_record', [
                                    'uncheck' => 0,
                                    'label' => '<span></span>',
                                ]) ?>
                            </span>
                            </div>
                        </div>
                        <div id="averageDurationBlock"
                             class="form-group-lg <?= $settings->dense_record == 0 ? 'disabled-block' : '' ?>">
                            <label>Средняя длительность обслуживания
                                <i class="far fa-question-circle p-1" data-toggle="popover"
                                   title="Средняя длительность обслуживания"
                                   data-html="true"
                                   data-content="Время, чаще всего затрачиваемое персоналом на обслуживание одного автомобиля. Устанавливается вручную."></i>
                            </label>
                            <?= Html::activeTextInput($settings, 'average_duration', ['class' => 'text-center form-control',
                                'placeholder' => '0 мин', 'required' => false]) ?>
                        </div>
                        <div class="form-group row align-items-center mt-8">
                            <div class="col-9">
                                До последнего клиента
                                <i class="far fa-question-circle p-1" data-toggle="popover"
                                   title="До последнего клиента"
                                   data-html="true"
                                   data-content="Система дает возможность записываться клиентам перед окончанием рабочего дня автомойки. Важно! необходимо указать время, на которое может задержаться персонал."></i>
                            </div>
                            <div class="col-3 text-right">
                            <span class="switch switch-icon switch-sm">
                                <?= Html::activeCheckbox($settings, 'until_last_client', [
                                    'uncheck' => 0,
                                    'label' => '<span></span>',
                                ]) ?>
                            </span>
                            </div>
                        </div>
                        <div id="staffDelayTimeBlock"
                             class="form-group <?= $settings->until_last_client == 0 ? 'disabled-block' : '' ?>">
                            <label for="exampleSelect1">Макс. время задержки персонала</label>
                            <select class="form-control" name="CarwashSettings[staff_delay_time]">
                                <?= Html::renderSelectOptions($settings->staff_delay_time, [
                                    '15' => '15 минут',
                                    '30' => '30 минут',
                                    '45' => '45 минут',
                                    '60' => '60 минут',
                                ]) ?>
                            </select>
                        </div>

                        <div class="form-group mt-8">
                            <label for="exampleSelect1">Время на заезд авто
                                <i class="far fa-question-circle p-1" data-toggle="popover" title="Время на заезд авто"
                                                                               data-html="true"
                                                                               data-content="Время, которое требуется автомобилю на заезд\выезд из автомоечного комплекса, суммируется с временем, затраченным на оказание услуг"></i>
                            </label>
                            <select class="form-control" name="CarwashSettings[checkout_time]">
                                <?= Html::renderSelectOptions($settings->checkout_time, [
                                    '0' => 'Не учитывать',
                                    '5' => '5 минут',
                                    '10' => '10 минут',
                                    '15' => '15 минут',
                                ]) ?>
                            </select>
                        </div>
                    </div>
                    <div class="offset-lg-1 col-lg-5">
                        <div class="form-group row align-items-center mt-4">
                            <div class="col-9">
                                Время услуг
                                <i class="far fa-question-circle p-1" data-toggle="popover" title="Время услуг"
                                   data-html="true"
                                   data-content="Время, затрачиваемое персоналом на оказание услуги, без учета въезда/выезда автомобиля клиента"></i>
                            </div>
                            <div class="col-3 text-right">
                            <span class="switch switch-icon switch-sm">
                                <label>
                                    <input type="checkbox" id="carwashSettingsTimeSwitcherCheckbox"
                                           <?= $settings->service_time_multiplier == 0 ? '' : 'checked' ?>/><span></span>
                                </label>
                            </span>
                                <?= Html::activeHiddenInput($settings, 'service_time_multiplier', ['id' => 'timeMultiplierInput', 'class' => 'text-center form-control',
                                    'required' => false]) ?>
                            </div>
                        </div>
                        <div id="carwashSettingsTimeSwitcherBlock"
                             class="<?= $settings->service_time_multiplier == 0 ? 'disabled-block' : '' ?>">
                            <ul id="carwashSettingsTimeSwitcher" class="nav nav-pills nav-fill">
                                <li class="nav-item">
                                    <a class="nav-link  <?= in_array($settings->service_time_multiplier, ['-15', '-30']) ? 'active' : '' ?>"
                                       id="home-tab-4" data-toggle="tab"
                                       href="#carwashSettingsTimeSwitcherBig">
                                        <span class="nav-text">Меньше на</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link <?= in_array($settings->service_time_multiplier, ['0', '15', '30']) ? 'active' : '' ?>"
                                       id="profile-tab-4" data-toggle="tab"
                                       href="#carwashSettingsTimeSwitcherSmall" aria-controls="profile">
                                        <span class="nav-text">Больше на</span>
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content mt-5" id="myTabContent4">
                                <div class="tab-pane fade <?= in_array($settings->service_time_multiplier, ['-15', '-30']) ? 'active show' : '' ?>"
                                     id="carwashSettingsTimeSwitcherBig" role="tabpanel"
                                     aria-labelledby="home-tab-4">
                                    <div class="d-flex align-items-center flex-wrap justify-content-between ">
                                        <div class="setting-time-switcher <?= $settings->service_time_multiplier == '-15' ? 'setting-time-switcher--active' : '' ?>"
                                             data-multiplier="-15">
                                            -15%
                                        </div>
                                        <div class="setting-time-switcher <?= $settings->service_time_multiplier == '-30' ? 'setting-time-switcher--active' : '' ?>"
                                             data-multiplier="-30">
                                            -30%
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade <?= in_array($settings->service_time_multiplier, ['0', '15', '30']) ? 'active show' : '' ?>"
                                     id="carwashSettingsTimeSwitcherSmall" role="tabpanel"
                                     aria-labelledby="profile-tab-4">
                                    <div class="d-flex align-items-center flex-wrap justify-content-between ">
                                        <div class="setting-time-switcher <?= $settings->service_time_multiplier == '15' ? 'setting-time-switcher--active' : '' ?>"
                                             data-multiplier="15">
                                            +15%
                                        </div>
                                        <div class="setting-time-switcher <?= $settings->service_time_multiplier == '30' ? 'setting-time-switcher--active' : '' ?>"
                                             data-multiplier="30">
                                            +30%
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <i class="far fa-question-circle p-1" data-toggle="popover" title="Меньше - Больше"
                           data-html="true"
                           data-content="Возможность вручную корректировать время оказания услуг на определенный %.<br>Важно! мгновенно применяется сразу для всех услуг и комплексов автомойки для всех последующих заказов."></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>


<script>
    $(document).ready(function () {
        // let cwId = $('#mainInfoBlock').data('cw-id');

        $('.submitSettingForm').on('click', function () {
            if ($('#settingPageForm')[0].checkValidity()) {
                $('#settingPageForm').submit();
            } else {
                $('#settingPageForm')[0].reportValidity();
            }
        });

        $('.setting-time-switcher').click(function () {
            $('.setting-time-switcher').removeClass('setting-time-switcher--active');
            $(this).addClass('setting-time-switcher--active');

            let multiplier = $(this).data('multiplier');
            $('#timeMultiplierInput').val(multiplier);
        });

        $('#carwashSettingsTimeSwitcherCheckbox').click(function () {
            if ($(this).prop('checked')) {
                $('#carwashSettingsTimeSwitcherBlock').removeClass('disabled-block');
            } else {
                $('#carwashSettingsTimeSwitcherBlock').addClass('disabled-block');
                $('#timeMultiplierInput').val(0);
            }
        });

        $('#carwashsettings-dense_record').click(function () {
            if ($(this).prop('checked')) {
                $('#averageDurationBlock').removeClass('disabled-block');
            } else {
                $('#averageDurationBlock').addClass('disabled-block');
            }
        });

        $('#carwashsettings-until_last_client').click(function () {
            if ($(this).prop('checked')) {
                $('#staffDelayTimeBlock').removeClass('disabled-block');
            } else {
                $('#staffDelayTimeBlock').addClass('disabled-block');
            }
        });

        $(document).on('click', '.removeItemFromBlacklist', function () {
            let item = $(this).closest('.setting-blacklist--item');

            $.ajax({
                type: 'POST',
                url: '/ajax/carwash/remove-blacklist',
                data: {
                    'id': item.data('id'),
                    'clientId': item.data('cl-id'),
                },
                success: function (data) {
                    if (data.result) {
                        item.hide(300);
                        toastr.success("Удален из черного списка");
                    } else {
                        toastr.error("Произошла ошибка при удалении из черного списка");

                    }
                }
            });
        });

        $(document).on('change', '#blacklistSearchCarNumber, #blacklistSearchCarRegion', function () {
            let number = $('#blacklistSearchCarNumber').val();
            let region = $('#blacklistSearchCarRegion').val();
            let perPage = 10;

            $.pjax.reload({
                url: '/carwash/settings?page=1&car_number=' + number + '&car_region=' + region + '&pre_page=' + perPage,
                container: "#blacklist",
            });
        });

    });

</script>