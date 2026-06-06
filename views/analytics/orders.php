<?php

use app\models\ar\order\Orders;
use yii\data\Pagination;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;

/**
 * @var Orders[] $orders
 * @var Pagination $ordersPages
 */


$this->title = 'Заказы';
$this->params['header_button'] = '<div class="d-block mr-12">
     <button class="btn btn-primary printAnalyticsButton px-6">Скачать таблицу</button>
    </div>';
?>
<div class="row">
    <div class="col-12">
        <div class="card card-custom  gutter-b">
            <div class="card-header h-auto border-0">
                <!--begin::Title-->
                <div class="card-title py-5">
                    <h3 class="card-label">
                        <span class="d-block text-muted font-weight-bolder">СЕГОДНЯ</span>
                        <span class="d-flex align-items-center text-muted mt-2 font-size-sm">
                            <span id="todayCountOrders" class="text-primary font-size-h4 mr-2">0</span>
                            заказов</span>
                    </h3>
                </div>
                <!--end::Title-->
                <!--begin::Toolbar-->
                <div class="card-toolbar">
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
                <div id="ordersChart"></div>
            </div>
        </div>
        <div class="card card-custom  gutter-b">
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-lg-4">
                        <div class="input-group date-picker-range">
                            <input id="dateFilter" type="text" class="form-control" name="date" readonly
                                   placeholder="Дата поиска"/>
                            <div class="input-group-append">
                                <span class="input-group-text">
                                    <i class="la la-calendar-check-o"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="d-block">
                            <div class="text-muted font-size-sm">НАШЛОСЬ</div>
                            <div class="font-size-h5">
                                <span id="tableOrdersCount"><?= $ordersPages->totalCount ?? 0 ?></span>
                                заказов
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="d-flex">
                            <div class="input-group mr-2">
                                <select class="form-control" id="sortSelect">
                                    <option value="date">По дате</option>
                                    <option value="price">По стоимости</option>
                                </select>
                            </div>
                            <div class="input-group">
                                <select class="form-control" multiple id="filterSelect">
                                    <option value="with-reviews">С отзывом</option>
                                    <option value="removed">Удаленные</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <?php Pjax::begin(['id' => 'ordersList']); ?>
                <div class="table-responsive" data-total="<?= $ordersPages->totalCount ?? 0 ?>">
                    <table class="table">
                        <thead style="border-bottom: 3px solid #efefef;">
                        <tr class="text-muted">
                            <th>№ заказа</th>
                            <th>Номер ТС</th>
                            <th>Статус</th>
                            <th>Создан</th>
                            <th>Сумма</th>
                            <th>Пост</th>
                            <th>Дата</th>
                            <th>Отзыв</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr class="table-list-item">
                                <td><?= $order->id ?? 0 ?></td>
                                <td><?= ($order->car_number) ?? 'A111AA' ?> <?= ($order->car_region) ?? '00' ?></td>
                                <td>
                                    <div class="px-4 py-1 bordered-circle-status <?= Orders::getCssStatusClass($order->status) ?>">
                                        <?= $order->currentStatusLabel() ?? '' ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($order->client_id): ?>
                                        Подписчиком
                                    <?php else: ?>
                                        Автомойкой
                                    <?php endif; ?>
                                </td>
                                <td><?= $order->total_price ?? 0 ?> ₽</td>
                                <td><?= $order->post ?? 'Не указан' ?></td>
                                <td><?= $order->date ?? 'Нет информации' ?></td>
                                <td><?= $order->getChats()->one() ? 'Есть' : 'Нет' ?></td>
                                <td><a href="/orders/show?id=<?= $order->id ?? 0 ?>" target="_blank">Подробнее</a></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?= LinkPager::widget([
                        'pagination' => $ordersPages,
                        'linkContainerOptions' => [
                            'tag' => 'div',
                            'class' => false,
                        ],
                        'linkOptions' => [
                            'tag' => 'a',
                            'class' => 'btn btn-icon btn-sm btn-light-primary mr-2 my-1',
                        ],
                        'disabledListItemSubTagOptions' => [
                            'tag' => 'a',
                            'class' => 'btn btn-icon btn-sm btn-light-primary disabled mr-2 my-1',
                        ],
                        'firstPageLabel' => '<i class="ki ki-bold-double-arrow-back icon-xs"></i>',
                        'lastPageLabel' => '<i class="ki ki-bold-double-arrow-next icon-xs"></i>',
                        'prevPageLabel' => '<i class="ki ki-bold-arrow-back icon-xs"></i>',
                        'nextPageLabel' => '<i class="ki ki-bold-arrow-next icon-xs"></i>',
                        'options' => [
                            'tag' => 'div',
                            'class' => 'd-flex flex-wrap py-2 mr-3 float-right list-pager',
                        ],
                        'maxButtonCount' => 3,
                    ]); ?>
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
        let apexChart = "#ordersChart";
        let chart = null;
        let categories = [];
        let ordersCount = [];
        let todayCount = 0;

        let filterDateFrom = false;
        let filterDateTo = false;

        $.ajax({
            type: 'GET',
            url: '/ajax/analytics/get-orders-for-chart',
            data: {cwId: cwId, pId: pId, type: chartType},
            success: function (data) {
                if (!data.result) {
                    toastr.success("Не удалось получить список заказов");
                } else {
                    console.log(data);
                    categories = data.categories;
                    ordersCount = data.counts;
                    todayCount = data.todayCounts;
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
                url: '/ajax/analytics/get-orders-for-chart',
                data: {cwId: cwId, pId: pId, type: chartType},
                success: function (data) {
                    if (!data.result) {
                        toastr.success("Не удалось получить список заказов");
                    } else {
                        console.log(data);
                        categories = data.categories;
                        ordersCount = data.counts;
                        todayCount = data.todayCounts;
                    }
                    chart.destroy();
                    initChart();
                }
            });
        });

        function initChart() {
            var options = {
                series: [{
                    name: 'Заказов',
                    data: ordersCount
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
                        text: 'Заказы'
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


        $(document).on('change', ['#dateFilter, #sortSelect', '#filterSelect'], function () {
            KTApp.block('#ordersList', {
                overlayColor: '#000000',
                state: 'warning',
                size: 'lg'
            });

            setTimeout(() => {
                let dateFrom = filterDateFrom;
                let dateTo = filterDateTo;
                let sort = $('#sortSelect').val();
                let filter = $('#filterSelect').val();

                $.pjax.reload({
                    url: '/analytics/orders?dateFrom=' + dateFrom + '&dateTo=' + dateTo + '&sort=' + sort + '&filter=' + filter,
                    container: "#ordersList"
                });
                KTApp.unblock('#ordersList');
            }, 1000);
        });

        $('#ordersList').on('pjax:success', function (event, data, status, xhr, options) {
            let ordersCount = $('#ordersList > .table-responsive').data('total');
            $('#tableOrdersCount').text(ordersCount);
        });

        $("#sortSelect").select2({
            minimumResultsForSearch: -1,
            placeholder: "Сортировка",
        });

        $("#filterSelect").select2({
            placeholder: "Фильтр",
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