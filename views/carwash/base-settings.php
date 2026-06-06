<?php
$this->title = 'Настройки';
$this->params['header_button'] = '<div class="d-block mr-12">
      <div>КОД ДЛЯ КЛИЕНТОВ</div>
      <div class="text-primary font-weight-bold font-size-h4">AV000000
      </div>
    </div>';
$this->params['mobileFixedMenu'] = [
    '1' => ['title' => 'Количество постов', 'parent_id' => null, 'url' => '#carwashSettingsPost'],
    '2' => ['title' => 'Черный список', 'parent_id' => null, 'url' => '#carwashSettingsBlacklist'],
    '3' => ['title' => 'Запись', 'parent_id' => null, 'url' => '#carwashSettingsOrder'],
    '4' => ['title' => 'Настройки записи', 'parent_id' => null, 'url' => '#carwashSettingsOrderSettings'],
];


?>
<div class="row">
    <div class="col-lg-5">
        <div id="carwashSettingsPost" class="card card-custom  gutter-b">
            <div class="card-body">
                <div class="row">
                    <h4 class="text-muted">КОЛИЧЕСТВО ПОСТОВ</h4>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="exampleSelect1">Количество постов</label>
                            <select class="form-control" id="exampleSelect1">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="carwashSettingsBlacklist" class="card card-custom  gutter-b">
            <div class="card-body">
                <h4 class="text-muted">ЧЁРНЫЙ СПИСОК</h4>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="form-group row align-items-center">
                            <div class="col-12">
                                <label>Поиск по номеру</label>
                            </div>
                            <div class="col-7">
                                <input type="number" class="form-control" placeholder="о000оо"/>
                            </div>
                            <div class="col-5">
                                <input type="number" class="form-control" placeholder="000"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="carwash-setting-blacklist">
                            <div class="setting-blacklist--item form-group row align-items-center">
                                <label class="col-5">а111аа</label>
                                <div class="col-7 text-primary text-right">
                                    Удалить из списка
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div id="carwashSettingsOrder" class="card card-custom  gutter-b">
            <div class="card-body">
                <h4 class="text-muted">ЗАПИСЬ</h4>

                <div class="form-group row align-items-center mt-4">
                    <div class="col-5">
                        Онлайн-запись
                        <i class="far fa-question-circle p-1" data-toggle="popover" title="Popover title"
                           data-html="true"
                           data-content="And here's some amazing <span class='label label-inline font-weight-bold label-light-primary'>HTML</span> content. It's very <code>engaging</code>. Right?"></i>
                    </div>
                    <div class="col-7 text-right">
                        <span class="switch switch-icon switch-sm">
                            <label>
                                <input type="checkbox" checked="checked" name="select"/><span></span>
                            </label>
                        </span>
                    </div>
                </div>

                <div class="row align-items-center mb-0">
                    <div class="col-6">
                        <div class="form-group mb-0">
                            <div class="checkbox-inline">
                                <label class="checkbox">
                                    <input type="checkbox" name="Checkboxes2"/>
                                    <span></span>
                                    Для подписчиков
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-0">
                            <div class="checkbox-inline">
                                <label class="checkbox">
                                    <input type="checkbox" name="Checkboxes2"/>
                                    <span></span>
                                    Клиенты из черного списка
                                </label>
                            </div>
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
                                <i class="far fa-question-circle p-1" data-toggle="popover" title="Popover title"
                                   data-html="true"
                                   data-content="And here's some amazing <span class='label label-inline font-weight-bold label-light-primary'>HTML</span> content. It's very <code>engaging</code>. Right?"></i>
                            </div>
                            <div class="col-3 text-right">
                            <span class="switch switch-icon switch-sm">
                            <label>
                                <input type="checkbox" checked="checked" name="select"/><span></span>
                            </label>
                            </span>
                            </div>
                        </div>
                        <div class="form-group-lg">
                            <label>Средняя длительность обслуживания</label>
                            <input type="email" class="form-control " placeholder="Enter contact number"/>
                        </div>
                        <div class="form-group row align-items-center mt-4">
                            <div class="col-9">
                                До последнего клиента
                                <i class="far fa-question-circle p-1" data-toggle="popover" title="Popover title"
                                   data-html="true"
                                   data-content="And here's some amazing <span class='label label-inline font-weight-bold label-light-primary'>HTML</span> content. It's very <code>engaging</code>. Right?"></i>
                            </div>
                            <div class="col-3 text-right">
                            <span class="switch switch-icon switch-sm">
                            <label>
                                <input type="checkbox" checked="checked" name="select"/><span></span>
                            </label>
                            </span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="exampleSelect1">Макс. время задержки персонала</label>
                            <select class="form-control" id="exampleSelect1">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                            </select>
                        </div>
                    </div>
                    <div class="offset-lg-1 col-lg-5">
                        <div class="form-group row align-items-center mt-4">
                            <div class="col-9">
                                Время услуг
                                <i class="far fa-question-circle p-1" data-toggle="popover" title="Popover title"
                                   data-html="true"
                                   data-content="And here's some amazing <span class='label label-inline font-weight-bold label-light-primary'>HTML</span> content. It's very <code>engaging</code>. Right?"></i>
                            </div>
                            <div class="col-3 text-right">
                            <span class="switch switch-icon switch-sm">
                            <label>
                                <input type="checkbox" checked="checked" name="select"/><span></span>
                            </label>
                            </span>
                            </div>
                        </div>
                        <ul id="carwashSettingsTimeSwitcher" class="nav nav-pills nav-fill">
                            <li class="nav-item">
                                <a class="nav-link active" id="home-tab-4" data-toggle="tab"
                                   href="#carwashSettingsTimeSwitcherBig">
                                    <span class="nav-text">Больше на</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="profile-tab-4" data-toggle="tab"
                                   href="#carwashSettingsTimeSwitcherSmall" aria-controls="profile">
                                    <span class="nav-text">Меньше на</span>
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content mt-5" id="myTabContent4">
                            <div class="tab-pane fade active show" id="carwashSettingsTimeSwitcherBig" role="tabpanel"
                                 aria-labelledby="home-tab-4">
                                <div class="d-flex align-items-center flex-wrap justify-content-between ">
                                    <div class="setting-time-switcher">
                                        15%
                                    </div>
                                    <div class="setting-time-switcher setting-time-switcher--active">
                                        30%
                                    </div>
                                    <div class="setting-time-switcher">
                                        1
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="carwashSettingsTimeSwitcherSmall" role="tabpanel"
                                 aria-labelledby="profile-tab-4">
                                <div class="d-flex align-items-center flex-wrap justify-content-between ">
                                    <div class="setting-time-switcher">
                                        15%
                                    </div>
                                    <div class="setting-time-switcher setting-time-switcher--active">
                                        30%
                                    </div>
                                    <div class="setting-time-switcher">
                                        1
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

