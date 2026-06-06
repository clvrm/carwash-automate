<?php

use app\models\ar\order\Orders;
use yii\data\Pagination;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;


/**
 * @var Pagination $pagination
 * @var array $data
 * @var string $dataType - personal / complexes / services
 */

$category = Yii::$app->request->get('category');

$this->title = 'Финансы';
$this->params['header_button'] = '<div class="d-block mr-12">
     <button class="printAnalyticsButton btn btn-primary px-6">Скачать таблицу</button>
    </div>';
?>
<div class="row">
    <div class="col-12">
        <div class="card card-custom  gutter-b">
            <div class="card-header h-auto border-0">
                <!--begin::Title-->
                <div class="card-title py-5">
                    <h3 class="card-label d-flex align-items-center">
                        Выручка:
                        <div class="ml-4 min-w-100px">
                            <select id="chartCategory" class="form-control form-selectpicker min-w-100px">
                                <option value="all">Общая</option>
                                <option value="wash">Мойка</option>
                                <option value="detail">Детейлинг</option>
                            </select>
                        </div>
                    </h3>
                </div>
                <!--end::Title-->
                <!--begin::Toolbar-->
                <div class="card-toolbar">
                    <div class="input-group date-picker-range w-auto max-w-275px mr-4 d-none">
                        <input type="text" class="form-control" readonly placeholder="Дата"/>
                        <div class="input-group-append">
                                        <span class="input-group-text">
                                            <i class="la la-calendar-check-o"></i>
                                        </span>
                        </div>
                    </div>
                    <div class="btn-group btn-group-lg btn-group-analistic-table" role="group">
                        <button data-type="week" type="button"
                                class="btn btn-reload-chart btn-outline-secondary btn-active">День
                        </button>
                        <button data-type="month" type="button" class="btn btn-reload-chart btn-outline-secondary">
                            Неделя
                        </button>
                        <button data-type="year" type="button" class="btn btn-reload-chart btn-outline-secondary">
                            Месяц
                        </button>
                    </div>
                </div>
                <!--end::Toolbar-->
            </div>
            <div class="card-body">
                <div id="financeChart"></div>
            </div>
        </div>
        <div class="card card-custom  gutter-b">
            <div class="card-header h-auto border-0">
                <!--begin::Title-->
                <div class="card-title py-5">
                    <h3 class="card-label d-flex align-items-center">
                        Выручка:
                        <div class="ml-4 mr-2 min-w-80px">
                            <select id="tableCategory" class="form-control form-selectpicker min-w-120px">
                                <option <?= ($category && $category == 'personal') ? 'selected' : '' ?>
                                        value="personal">Сотрудники
                                </option>
                                <option <?= ($category && $category == 'wash') ? 'selected' : '' ?> value="wash">Мойка
                                </option>
                                <option <?= ($category && $category == 'detail') ? 'selected' : '' ?> value="detail">
                                    Детейлинг
                                </option>
                            </select>
                        </div>
                        <div id="typeSelectorBlock"
                             class="min-w-120px" <?= (!$category || $category == 'personal') ? 'style="display: none"' : '' ?>>
                            <select id="serviceType" class="form-control form-selectpicker min-w-80px">
                                <option value="all">Все</option>
                                <option value="complex">Комплексы</option>
                                <option value="service">Услуги</option>
                            </select>
                        </div>
                    </h3>
                </div>
                <!--end::Title-->
                <!--begin::Toolbar-->
                <div class="card-toolbar">
                    <div class="d-flex justify-content-between">
                        <div class="input-group date-picker-range mr-4">
                            <input id="dateFilter" type="text" class="form-control" name="date" readonly
                                   placeholder="Дата поиска"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                        <div class="input-group">
                            <select class="form-control" id="sortSelect">
                                <option value="sum">По сумме</option>
                                <option value="count">По количеству</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <!--end::Toolbar-->
            <div class="card-body">
                <?php Pjax::begin(['id' => 'financeList']); ?>

                <div class="table-responsive" data-total="<?= $pagination->totalCount ?? 0 ?>">
                    <?php if ($dataType === 'personal') : ?>
                        <table class="table">
                            <thead style="border-bottom: 3px solid #efefef;">
                            <tr class="text-muted text-uppercase">
                                <th>Сотрудник</th>
                                <th>Сумма с сотрудника</th>
                                <th>Выполненные заказы</th>
                                <th>З/П</th>
                                <th>Рабочих дней</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data as $item): ?>
                                <tr class="table-list-item">
                                    <td><?= $item['personalName'] ?? 'Не задано' ?></td>
                                    <td><?= $item['personalSum'] ?? 0 ?> ₽</td>
                                    <td><?= $item['totalOrders'] ?? 0 ?></td>
                                    <td><?= $item['personalSalary'] ?? 'не указана' ?></td>
                                    <td><?= $item['workDays'] ?? 0 ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php elseif ($dataType === 'complexes'): ?>
                        <table class="table">
                            <thead style="border-bottom: 3px solid #efefef;">
                            <tr class="text-muted text-uppercase">
                                <th>Комплекс</th>
                                <th>Сумма с комплекса</th>
                                <th>Выполненные комплексы</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data as $item): ?>
                                <tr class="table-list-item">
                                    <td><?= $item['name'] ?? 'Не задано' ?></td>
                                    <td><?= $item['serviceSum'] ?? 0 ?> Р.</td>
                                    <td><?= $item['totalServices'] ?? 0 ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php elseif ($dataType === 'services'): ?>
                        <table class="table">
                            <thead style="border-bottom: 3px solid #efefef;">
                            <tr class="text-muted text-uppercase">
                                <th>Услуга</th>
                                <th>Сумма с услуги</th>
                                <th>Выполненные услуги</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($data as $item): ?>
                                <tr class="table-list-item">
                                    <td><?= $item['name'] ?? 'Не задано' ?></td>
                                    <td><?= $item['serviceSum'] ?? 0 ?> Р.</td>
                                    <td><?= $item['totalServices'] ?? 0 ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                </div>
                <?php Pjax::end(); ?>
            </div>
        </div>

    </div>
</div>
<script>
    $(document).ready(function () {
        let cwId = $('#mainInfoBlock').data('cw-id');
        let pId = $('#mainInfoBlock').data('p-id');
        let chartType = 'week';
        let chartCategory = 'all';
        let apexChart = "#financeChart";
        let chart = null;
        let categories = [];
        let finances = [];
        let todayCount = 0;

        let filterDateFrom = false;
        let filterDateTo = false;

        $.ajax({
            type: 'GET',
            url: '/ajax/analytics/get-finance-for-chart',
            data: {cwId: cwId, pId: pId, type: chartType, category: chartCategory},
            success: function (data) {
                if (!data.result) {
                    toastr.success("Не удалось получить список финансов");
                } else {
                    console.log(data);
                    categories = data.categories;
                    finances = data.finances;
                }
                $('#todayCountOrders').text(todayCount);
                initChart();
            }
        });

        $('.btn-reload-chart').on('click', function () {
            chartType = $(this).data('type');
            $('.btn-reload-chart').removeClass('btn-active');
            $(this).addClass('btn-active');

            $.ajax({
                type: 'GET',
                url: '/ajax/analytics/get-finance-for-chart',
                data: {cwId: cwId, pId: pId, type: chartType, category: chartCategory},
                success: function (data) {
                    if (!data.result) {
                        toastr.success("Не удалось получить список финансов");
                    } else {
                        console.log(data);
                        categories = data.categories;
                        finances = data.finances;
                    }
                    chart.destroy();
                    initChart();
                }
            });
        });

        function initChart() {
            var options = {
                series: [{
                    name: 'Выручка',
                    data: finances
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded',
                        dataLabels: {
                            position: 'top',
                        },
                    },

                },
                dataLabels: {
                    enabled: true,
                    formatter: function (val) {
                        return val;
                    },
                    offsetY: -20,
                    style: {
                        fontSize: '12px',
                        colors: ["#304758"]
                    }
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: categories,
                },
                yaxis: {
                    title: {
                        text: 'Выручка'
                    }
                },
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val;
                        }
                    }
                },
                colors: ['#B3D2FF', '#1BC5BD', '#FFA800']
            };

            chart = new ApexCharts(document.querySelector(apexChart), options);
            chart.render();
        }

        $('#dateFilter').on('apply.daterangepicker', function (ev, picker) {
            filterDateFrom = picker.startDate.format('YYYY-MM-DD');
            filterDateTo = picker.endDate.format('YYYY-MM-DD');
        });


        $(document).on('change', ['#dateFilter, #sortSelect', '#tableCategory', '#serviceType'], function () {
            KTApp.block('#financeList', {
                overlayColor: '#000000',
                state: 'warning',
                size: 'lg'
            });

            let category = $('#tableCategory').val();
            let serviceType = $('#serviceType').val();
            if (category === 'personal') {
                $('#typeSelectorBlock').hide();
                $("#serviceType").val('all').selectpicker('refresh');
                serviceType = $('#serviceType').val();
            } else {
                $('#typeSelectorBlock').show();
            }

            setTimeout(() => {
                let dateFrom = filterDateFrom;
                let dateTo = filterDateTo;
                let sort = $('#sortSelect').val();

                console.log(category);

                $.pjax.reload({
                    url: '/analytics/finance?dateFrom=' + dateFrom + '&dateTo=' + dateTo + '&sort=' + sort +
                        '&category=' + category + '&serviceType=' + serviceType,
                    container: "#financeList"
                });
                KTApp.unblock('#financeList');
            }, 1000);
        });

        $('#financeList').on('pjax:success', function (event, data, status, xhr, options) {
            // let ordersCount = $('#financeList > .table-responsive').data('total');
            // $('#tableOrdersCount').text(ordersCount);
        });

        $("#sortSelect").select2({
            minimumResultsForSearch: -1,
            placeholder: "Сортировка",
        });

        let url = new URL(document.location);
        let searchParams = url.searchParams;
        $('.printAnalyticsButton').on('click', function () {
            url = new URL(document.location);
            searchParams = url.searchParams;

            searchParams.set('print', '1');
            window.location.href = url.toString();
        });


        if (searchParams.get('print') == '1') {
            searchParams.delete('print'); // удалить параметр "test"
            window.history.pushState({}, '', url.toString());
        }
    })
</script>
<style>
    table.table tbody tr:nth-child(2n + 1) {
        background: #F8F8F8;
    }
</style>