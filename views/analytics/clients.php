<?php

use app\commons\helpers\ClientHelper;
use app\models\ar\order\Orders;
use yii\base\BaseObject;
use yii\data\Pagination;
use yii\db\Expression;
use yii\db\Query;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;


/**
 * @var Orders[] $orders
 * @var Pagination $ordersPages
 */


$this->title = 'Клиенты';
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
                    <h3 class="card-label">
                        <span class="d-block text-muted font-weight-bolder">СЕГОДНЯ</span>
                        <span id="todayCountClients"
                              class="d-flex align-items-center text-muted mt-2 font-size-sm"><span
                                    class="text-primary font-size-h4 mr-2">0</span> клиентов</span>
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
                <div class="row">
                    <div class="col-lg-4">
                        <div class="input-group">
                            <input id="carNumber" type="text" class="form-control" placeholder="о000оо"/>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="d-block">
                            <div class="text-muted font-size-sm">НАШЛОСЬ</div>
                            <div class="font-size-h5">
                                <span id="tableClientsCount"><?= $ordersPages->totalCount ?? 0 ?></span>
                                клиентов
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="d-flex">
                            <div class="input-group mr-2">
                                <select class="form-control" id="sortSelect">
                                    <option value="default">По дате</option>
                                    <option value="visits">По посещениям</option>
                                    <option value="total_price">По сумме</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <?php Pjax::begin(['id' => 'clientsList']); ?>
                <div class="table-responsive" data-total="<?= $ordersPages->totalCount ?? 0 ?>">
                    <table class="table">
                        <thead>
                        <tr class="text-muted">
                            <th>Номер ТС</th>
                            <th>Статус</th>
                            <th>Кол-во посещений</th>
                            <th>Всего с клиента</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($orders as $order): ?>
                            <?php $totalOrdersCount = Orders::find()->where(['carwash_id' => Yii::$app->user->identity->getCWid()])
                                ->andWhere(['car_number' => $order->car_number, 'car_region' => $order->car_region])
                                ->count();

                            $totalOrdersSum = (new Query())
                                ->select(['total' => 'SUM(total_price)'])
                                ->from('orders')
                                ->where(['carwash_id' => Yii::$app->user->identity->getCWid()])
                                ->andWhere(['car_number' => $order->car_number, 'car_region' => $order->car_region])
                                ->one();
                            $totalOrdersSum = $totalOrdersSum['total'] ?? 0;

                            $isSubscriber = ClientHelper::isSubscriberByCarNumber(Yii::$app->user->identity->getCWid(),
                                $order->car_number, $order->car_region);

                            ?>

                            <tr class="table-list-item">
                                <td><?= ($order->car_number) ?? 'A111AA' ?> <?= ($order->car_region) ?? '00' ?></td>
                                <td><?= $isSubscriber ? 'Подписчик' : 'Клиент' ?></td>
                                <td><?= $totalOrdersCount ?? 0 ?></td>
                                <td><?= $totalOrdersSum ?? 0 ?> ₽</td>
                                <td><a href="/analytics/show-client-by-order?orderId=<?= $order->id ?>">Подробнее</a></td>
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
        let clientsCount = [];
        let subscribersCount = [];
        let todayCount = 0;

        let filterDateFrom = false;
        let filterDateTo = false;

        $.ajax({
            type: 'GET',
            url: '/ajax/analytics/get-clients-for-chart',
            data: {cwId: cwId, pId: pId, type: chartType},
            success: function (data) {
                if (!data.result) {
                    toastr.success("Не удалось получить список клиентов");
                } else {
                    console.log(data);
                    categories = data.categories;
                    clientsCount = data.clients;
                    subscribersCount = data.subscribers;
                    todayCount = data.todayCounts;
                }
                $('#todayCountClients').text(todayCount);
                initChart();
            }
        });

        $('.btn-reload-chart').on('click', function () {
            chartType = $(this).data('type');
            $('.btn-reload-chart').removeClass('btn-active');
            $(this).addClass('btn-active');

            $.ajax({
                type: 'GET',
                url: '/ajax/analytics/get-clients-for-chart',
                data: {cwId: cwId, pId: pId, type: chartType},
                success: function (data) {
                    if (!data.result) {
                        toastr.success("Не удалось получить список клиентов");
                    } else {
                        console.log(data);
                        categories = data.categories;
                        clientsCount = data.clients;
                        subscribersCount = data.subscribers;
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
                    name: 'Не подписчики',
                    data: clientsCount
                }, {
                    name: 'Подписчики',
                    data: subscribersCount
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    stacked: true,
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
                colors: ['#B3FFB9', '#B3D2FF']
            };

            chart = new ApexCharts(document.querySelector(apexChart), options);
            chart.render();
        }


        $(document).on('change', ['#carNumber, #sortSelect', '#filterSelect'], function () {
            KTApp.block('#clientsList', {
                overlayColor: '#000000',
                state: 'warning',
                size: 'lg'
            });

            setTimeout(() => {
                let sort = $('#sortSelect').val();
                let filter = $('#filterSelect').val();
                let carNumber = $('#carNumber').val();

                $.pjax.reload({
                    url: '/analytics/clients?sort=' + sort + '&filter=' + filter + '&number=' + carNumber,
                    container: "#clientsList"
                });
                KTApp.unblock('#clientsList');
            }, 1000);
        });

        $('#clientsList').on('pjax:success', function (event, data, status, xhr, options) {
            let ordersCount = $('#clientsList > .table-responsive').data('total');
            $('#tableClientsCount').text(ordersCount);
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