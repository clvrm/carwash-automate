let currentScale = 1; // 1 - час; 2 - 30 минут; 3 - 15 минут; 4 - 5 минут;

let Settings = {
    posts: 0,
    cellMinutes: 0,
    startDayHour: 0,
    endDayHour: 0,
    isWorkDay: true,
};
let Orders = [];
let lastMouseEvent = parseInt(+(new Date() / 1000)); // Время последнего события нажатия / движения мыши
let isFirstContentDraw = false; // Впервые ли отрисовали контент?

$(document).ready(function () {
    let cwId = $('#tableJs').data('cw-id');
    let pId = $('#tableJs').data('p-id');
    let userCanEdit = userCanRemove = userCanCreate = $('#tableJs').data('can-edit-orders');
    let userCanClose = $('#tableJs').data('can-close-orders');
    let scheduler = null;
    let currentDate = new Date();
    currentScale = 1;

    init();

    async function init() {
        changeCalendarCurrentDay(currentDate);
        let localeDate = currentDate.toLocaleDateString(); // format: 25.10.2021
        Promise.all([getSettings(localeDate), getOrders(localeDate)]).then(() => {
            console.log('Все замечательно! Рад что вы тут!');

            let savedScale = Cookies.get('jo-Scale');
            if (savedScale && savedScale != 1) {
                currentScale = parseInt(savedScale);
                let scaleText = getScaleText(currentScale);
                $('.jo-current-interval').text(scaleText);
            }
            updateCurrentJournalSettings();

            initCalendar();
        }).catch(() => {
            toastr.error('Проблемы с получением данных журнала');
        })
    }

    function getSettings(date) {
        return $.ajax({
            type: 'POST',
            url: '/ajax/orders/journal-get-settings',
            data: {cwId: cwId, pId: pId, date: date},
            success: function (data) {
                if (data.result) {
                    Settings = data.settings;
                } else {
                    toastr.error("Не удалось получить базовые настройки");
                }
            }
        });
    }

    function getOrders(date) {
        return $.ajax({
            type: 'POST',
            url: '/ajax/orders/journal-get-orders',
            data: {cwId: cwId, pId: pId, date: date},
            success: function (data) {
                if (data.result) {
                    Orders = data.orders;
                } else {
                    toastr.error("Не удалось получить заказы за выбранный день");
                }
            }
        });
    }

    function updateOrders(force = false) {
        let localeDate = currentDate.toLocaleDateString();
        getOrders(localeDate);

        if (force || (parseInt(lastMouseEvent) + 10 < parseInt(+(new Date() / 1000)))) {
            $("#tableJs").dxScheduler('instance').option('dataSource', Orders);
            lastMouseEvent = (+new Date() / 1000);
            console.log('Прошло обновление заказов');
        } else {
            console.log(new Date(lastMouseEvent * 1000));
            // console.log('Были действия на странице за последние 10 сек');
        }
    }

    window.addEventListener('touchmove', function (e) {
        if (document.getElementById('tableJs').contains(e.target)) {
            lastMouseEvent = (+new Date() / 1000);
            console.log('touchmove');
        }
    });
    window.addEventListener('touchstart', function (e) {
        if (document.getElementById('tableJs').contains(e.target)) {
            lastMouseEvent = (+new Date() / 1000);
            console.log('touchstart');
        }
    });
    window.addEventListener('click', function (e) {
        if (document.getElementById('tableJs').contains(e.target)) {
            lastMouseEvent = (+new Date() / 1000);
            console.log('click');
        }
    });
    window.addEventListener('mousedown', function (e) {
        if (document.getElementById('tableJs').contains(e.target)) {
            lastMouseEvent = (+new Date() / 1000);
            console.log('mousedown');
        }
    });

    setInterval(updateOrders, 2000);

    function initCalendar() {
        if (!Settings.isWorkDay) {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": false,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "onclick": null,
                "showDuration": "3000",
                "hideDuration": "1000",
                "timeOut": "50000",
                "extendedTimeOut": "50000",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            toastr.warning("Вы выбрали нерабочий день. Изменить режим работы можно в меню настроек", "Нерабочий день");
        } else {
            toastr.clear();
        }

        console.log('init calendar');
        var postData = [];
        for (let i = 1; i <= Settings.posts; i++) {
            postData.push({'text': i + ' пост', 'id': i})
        }

        scheduler = $("#tableJs").dxScheduler({
            timeZone: "Europe/Moscow",
            textExpr: "text",
            dataSource: Orders,
            editing: {
                allowAdding: false,
                allowResizing: [true, true],
                allowDragging: [true, true],
            },
            views: [{
                type: "day",
                name: "123",
                groupOrientation: "horizontal",
                cellDuration: Settings.cellMinutes,
            }],
            scrolling: {
                mode: 'standart',
                // mode: 'virtual',
            },
            groups: ["postId"],
            crossScrollingEnabled: true, // скролл в двух направлениях одновременно. В dev-table, есть фикс для работы с отключенным
            timeCellTemplate: function (e) {
                let hours = e.date.getHours();
                let time = e.date.getMinutes();

                if (hours == "0" && time == "00") {
                    return "00:00";
                }
                if (time != "00") {
                    return '<span class="jo-time-cell">:' + time + '</span>';
                }
                return '<span class="jo-time-cell">' + hours + ':00</span>';
            },
            resources: [
                {
                    fieldExpr: "postId",
                    allowMultiple: false,
                    dataSource: postData,
                    label: "Пост",
                }
            ],
            onAppointmentDblClick: function (e) {
                e.cancel = true;
            },
            onAppointmentFormOpening: function (e) {
                e.cancel = true;
            },
            appointmentTemplate: function (model, index, element) {
                element.append(prepareOrderHtml(model.appointmentData));
            },
            appointmentTooltipTemplate: function (model) {
                console.log(model);
                let html = prepareOrderTooltipInfo(model.appointmentData.id)
                return html;
            },
            onContentReady: function (e) {
                console.log('tableContentReady');
                updateShortOrdersResize();
            },
            showCurrentTimeIndicator: true,
            showAllDayPanel: false,
            shadeUntilCurrentTime: true,
            currentView: "day",
            currentDate: currentDate,
            startDayHour: Settings.startDayHour,
            endDayHour: Settings.endDayHour,
            firstDayOfWeek: 1,
            height: 'calc(100vh - 154px)',
            onCellClick: function (e) {
                $('a.jo-create-link-from-schedule').remove();

                let cellStartDate = e.cellData.startDate;
                let cellEndDate = e.cellData.endDate;
                let cellPostId = e.cellData.groups.postId;

                if (!checkAlreadyOrdered(false, cellPostId, cellStartDate, cellEndDate) && cellStartDate > new Date()) {
                    let day = e.cellData.startDate.getDate();
                    // Приводим значение месяца в Date формат
                    let month = (e.cellData.startDate.getUTCMonth() + 1);
                    if (month < 10) {
                        month = '0' + month;
                    }
                    let year = e.cellData.startDate.getFullYear();
                    let startHour = e.cellData.startDate.getHours();
                    let startMinutes = e.cellData.startDate.getMinutes();
                    let endHour = e.cellData.endDate.getHours();
                    let endMinutes = e.cellData.endDate.getMinutes();
                    let fullDate = year + '-' + month + '-' + day;
                    let startFullTime = startHour + ':' + startMinutes;
                    let endFullTime = endHour + ':' + endMinutes;

                    let element = e.cellElement[0];
                    if (userCanCreate) {
                        $(element).html("<a target='_blank' class='jo-create-link-from-schedule' href='/orders/create?date=" + fullDate + "&startTime=" + startFullTime + "&post=" + cellPostId + "&endTime=" + endFullTime + "'><i class='fas fa-plus'></i></a>");
                    }
                }
            },
            onAppointmentUpdating: function (e) {
                let orderId = e.newData.id;
                let order = Orders.find(x => x.id === orderId);
                if (!userCanEdit) {
                    e.cancel = true;
                    DevExpress.ui.notify("У вас нет прав на редактирование заказов", "warning", 3000);
                    return false;
                }
                if (new Date(order.startDate).getDate() != new Date(order.endDate).getDate()) {
                    e.cancel = true;
                    DevExpress.ui.notify("Запись находится между двумя днями. Изменения разрешены только на странице редактирования", "warning", 3000);
                    return false;
                }
                if (checkAlreadyOrdered(e.newData.id, e.newData.postId, e.newData.startDate, e.newData.endDate)) {
                    console.log(123123);
                    e.cancel = true;
                    DevExpress.ui.notify("Невозможно передвинуть запись из-за пересечения с соседней записью", "warning", 3000);
                    return false;
                }
                let post = e.newData.postId;
                let startAt = new Date(e.newData.startDate).toLocaleString();
                let endAt = new Date(e.newData.endDate).toLocaleString();
                updateOrder(orderId, post, startAt, endAt);
            },
        }).dxScheduler("instance");
        scheduler.on('appointmentUpdating', function (e) {
            updateShortOrdersResize();
        });
        scheduler.on('contentReady', function (e) {
            if (!isFirstContentDraw) {
                var currentHour = new Date().getHours();
                e.component.scrollToTime(currentHour, 30, new Date());

                isFirstContentDraw = true; // Указываем, что произошла первая отрисовка контента
            }
        });

        scheduler.option("indicatorUpdateInterval", 10 * 1000);
    }

    function updateOrder(id, post, startAt, endAt) {
        $.ajax({
            type: 'POST',
            url: '/ajax/orders/update-order-datetime',
            data: {
                cwId: cwId, pId: pId,
                orderId: id, post: post, startAt: startAt, endAt: endAt
            },
            success: function (data) {
                console.log(data);
                if (data.result) {
                    toastr.success("Заказ обновлен");
                } else {
                    toastr.error("Не удалось обновить заказ");
                }
            }
        });
    }

    function getScaleText(scale) {
        switch (scale) {
            case 1:
                return "1 час";
            case 2:
                return "30 минут";
            case 3:
                return "15 минут";
            case 4:
                return "5 минут";
            default:
                return "Не определено";
        }
    }

    function updateCurrentJournalSettings() {
        switch (currentScale) {
            case 1: {
                Settings.cellMinutes = 60;
                break;
            }
            case 2: {
                Settings.cellMinutes = 30;
                break;
            }
            case 3: {
                Settings.cellMinutes = 15;
                break;
            }
            case 4: {
                Settings.cellMinutes = 5;
                break;
            }
        }

        return Settings;
    }

    function switchCurrentScaleByInterval(minutes) {
        switch (minutes) {
            case 60:
                currentScale = 1;
                break;
            case 30:
                currentScale = 2;
                break;
            case 15:
                currentScale = 3;
                break;
            case 5:
                currentScale = 4;
                break;
            default:
                currentScale = 1;
        }
        updateCurrentJournalSettings();
        updateShortOrdersResize();
    }

    function getStatusClass(statusId) {
        let statusClass = '';
        switch (statusId) {
            case 10:
                statusClass = 'jo-status-new-wash';
                break;
            case 11:
                statusClass = 'jo-status-new-client';
                break;
            case 20:
                statusClass = 'jo-status-work';
                break;
            case 30:
                statusClass = 'jo-status-archive';
                break;
            case 40:
                statusClass = 'jo-status-block';
                break;
        }
        return statusClass;
    }

    /**
     * Смотреть Clients/getReputationStatus()
     * @param reputation
     * @returns {string}
     */
    function getReputationClass(reputation) {
        let reputationClass = '';
        console.log(reputation);
        if (reputation <= -3) {
            reputationClass = 'fa-frown';
        } else if (reputation >= 3) {
            reputationClass = 'fa-smile';
        }
        return reputationClass;
    }

    /**
     * Подготовка заказа для отрисовки в ячейке журнала
     * @param order
     * @returns {string}
     */
    function prepareOrderHtml(order) {
        let wrapClass = 'journal-order';
        let statusClass = getStatusClass(order.status);
        let reputationClass = getReputationClass(order.reputation);
        let orderCalcTimeMinutes = ((new Date(order.endDate) - new Date(order.startDate)) / 1000) / 60;
        if (orderCalcTimeMinutes < Settings.cellMinutes) {
            wrapClass = 'journal-order-short'
            return '<div style="height: inherit" class="jo ' + wrapClass + ' ' + statusClass + '" data-jo-id="' + order.id + '"></div>';
        }
        if (new Date(order.startDate).getDate() != new Date(order.endDate).getDate()) {
            wrapClass = 'journal-order jo-order-two-dates';
        }
        let carNumber = order.carNumber;
        let clientId = order.clientId;
        let carRegion = order.carRegion;
        let totalPrice = order.totalPrice;
        let personalName = order.personalFullname;
        let adminComment = order.adminComment;
        let clientComment = order.clientComment;
        let orderTextArray = order.textArray;
        let orderTextHtml = '';
        $.each(orderTextArray, function (index, item) {
            orderTextHtml += '<li>' + item + '</li>';
        });
        let reputationBlock = '';
        if (clientId) {
            reputationBlock = '<i class="far ' + reputationClass + ' fa-lg"></i>';
        }

        let innerData = '<div class="d-flex justify-content-between jo-main-info">' +
            '<div class="jo-status-prefix ' + statusClass + '"></div>' +
            '<div class="d-flex font-size-lg font-weight-bold align-items-center"><div class="jo-car-number">' + carNumber + '</div>' +
            '<div class="car-info-divider"></div><div class="jo-car-region">' + carRegion + '</div></div>' +
            '<div class="d-flex align-items-center jo-reputation mr-6">' + reputationBlock + '</div>' +
            '</div>' +
            '<div class="jo-additional-info">' +
            '<ul>' + orderTextHtml + '</ul>' +
            '<div class="jo-order-work-info d-flex justify-content-between px-4 mt-6">' +
            '<div class="d-block jo-order-price">' +
            '<div class="text-muted">ЦЕНА</div><div class="font-weight-bold">' + totalPrice + ' Р.</div>' +
            '</div>';

        if (personalName != NaN && personalName != null) {
            innerData += '<div class="d-block jo-order-assigned-manager">' +
                '<div class="text-muted">ИСПОЛНИТЕЛЬ</div><div class="font-weight-bold">' + personalName + '</div>' +
                '</div>';
        }

        innerData += '</div></div>';


        return '<div class="jo ' + wrapClass + ' jo-wrapper ' + statusClass + '" data-jo-id="' + order.id + '">' + innerData + '</div>';
    }

    /**
     * Подготовка информации о заказе для всплывающего окна
     * @param orderId
     * @param model
     * @returns {string}
     */
    function prepareOrderTooltipInfo(orderId, model) {
        let order = Orders.find(x => x.id === orderId);
        let carNumber = order.carNumber;
        let carRegion = order.carRegion ?? '';
        let totalPrice = order.totalPrice;
        let personalName = order.personalFullname;
        let adminComment = order.adminComment;
        let clientComment = order.clientComment;
        let orderTextArray = order.textArray;
        let orderTextHtml = '';

        $.each(orderTextArray, function (index, item) {
            orderTextHtml += '<li>' + item + '</li>';
        });
        let statusClass = getStatusClass(order.status);

        let startTime = new Date(order.startDate).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'})
        let endTime = new Date(order.endDate).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'})
        let timeRangeString = startTime + ' - ' + endTime;

        let headTooltip = '<div class="d-flex flex-wrap justify-content-between align-items-center">' +
            '<div class="jo-tooltip-content d-flex align-items-center">' +
            '<div class="jo-tooltip-order-status">' +
            '<div class="jo-tooltip-status ' + statusClass + '"></div>' +
            '</div>' +
            '<div class="d-block">' +
            '<div class="jo-tooltip-title-car-number">' + carNumber + ' ' + carRegion + '</div>' +
            '<div class="font-size-sm">' + timeRangeString + '</div>' +
            '</div>' +
            '</div>' +
            '<div class="d-flex">';
        if (order.status === 40 || order.status === 30 || !userCanEdit) {
            headTooltip += '<div class="jo-show-button mr-2" data-order-id="' + orderId + '"><i class="fa fa-eye text-light-primary text-hover-primary"></i></div>';
        } else {
            if (userCanEdit) {
                headTooltip += '<div class="jo-edit-button mr-2" data-order-id="' + orderId + '"><i class="fa fa-pen-alt text-light-primary text-hover-primary"></i></div>';
            }
        }
        if (userCanClose && (order.status === 10 || order.status === 11 || order.status === 20)){
            headTooltip += '<div class="jo-close-button mr-2" data-order-id="' + orderId + '"><i class="far fa-window-close text-dark-25 text-hover-dark"></i></div>';
        }
        if(userCanRemove) {
            headTooltip += '<div class="jo-delete-button" data-order-id="' + orderId + '"><i class="fa fa-trash text-light-danger text-hover-danger"></i></div>';
        }
        headTooltip += '</div></div>';

        let bodyTooltip = '<div class="jo-tooltip-additional-info">' +
            '<ul>' + orderTextHtml + '</ul>' +
            '<div class="jo-order-work-info d-flex justify-content-between px-4 mt-6">' +
            '<div class="d-block jo-order-price w-100">' +
            '<div class="text-muted">ЦЕНА</div><div class="font-weight-bold">' + totalPrice + ' Р.</div>' +
            '</div>';

        if (personalName != NaN && personalName != null) {
            bodyTooltip += '<div class="d-block jo-order-assigned-manager w-100">' +
                '<div class="text-muted">ИСПОЛНИТЕЛЬ</div><div class="font-weight-bold">' + personalName + '</div>' +
                '</div>';
        }
        bodyTooltip += '</div>';

        if (adminComment != NaN && adminComment != null && adminComment != '') {
            bodyTooltip += '<div class="d-block jo-order-admin-comment">' +
                '<div class="text-muted">Комментарий администратора</div><div class="">' + adminComment + '</div>' +
                '</div>';
        }
        if (clientComment != NaN && clientComment != null && clientComment != '') {
            bodyTooltip += '<div class="d-block jo-order-client-comment">' +
                '<div class="text-muted">Комментарий клиента</div><div class="">' + clientComment + '</div>' +
                '</div>';
        }
        bodyTooltip += '</div>';

        return '<div class="jo-tooltip-order d-block">' + headTooltip + bodyTooltip + '</div>';
    }


    var dateCellTemplate = function (cellData, index, container) {

        container.append(
            $("<div />")
                .addClass("name")
                .text(dayOfWeekNames[cellData.date.getDay()]),
            $("<div />")
                .addClass("number")
                .text(cellData.date.getDate())
        );
    };

    /**
     *
     * @param orderId
     * @param postId
     * @param startDate
     * @param endDate
     * @returns {boolean}
     */
    function checkAlreadyOrdered(orderId, postId, startDate, endDate) {
        let ordersOnPost = Orders.filter(function (elem) {
            if (elem.id == orderId) {
                return false;
            }

            if (elem.postId == postId) {
                return true;
            }
            return false;
        });

        startDate = new Date(startDate);
        endDate = new Date(endDate);
        let resultCrossing = false;
        $.each(ordersOnPost, function (i, elem) {
            let elemStartDate = new Date(elem.startDate);
            let elemEndDate = new Date(elem.endDate);
            if (startDate < elemEndDate && endDate > elemStartDate) {
                console.log('Пересекаемся с элементом');
                console.log(elemStartDate);
                console.log(elemEndDate);
                resultCrossing = true;
            }
        });

        return resultCrossing;
    }

    $(document).on('click', '.jo-edit-button', function () {
        let orderId = $(this).data('orderId');

        window.open("/orders/edit?orderId=" + orderId, "_blank");
    });

    $(document).on('click', '.jo-close-button', function () {
        let orderId = $(this).data('orderId');
        let closeStatus = 30;

        $.ajax({
            type: 'POST',
            url: '/ajax/orders/change-status',
            data: {
                'orderId': id,
                'pId': pId,
                'status': closeStatus,
            },
            success: function (data) {
                if (data.result) {
                    toastr.success('Заказ успешно закрыт');
                    $('.changeOrderStatusButton[data-status="' + status + '"]').hide();
                } else {
                    toastr.error("Ошибка при закрытии заказа");
                }
            }
        });
    });

    $(document).on('click', '.jo-show-button', function () {
        let orderId = $(this).data('orderId');

        window.open("/orders/show?id=" + orderId, "_blank");
    });

    $(document).on('click', '.jo-delete-button', function () {
        let orderId = $(this).data('orderId');
        Swal.fire({
            title: "Вы действительно хотите удалить данный заказ",
            text: "Это действие невозможно отменить",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            cancelButtonText: 'Отменить',
            confirmButtonText: "Да, удалить!",
            reverseButtons: true,
            customClass: {
                confirmButton: "btn btn-danger btn-lg",
                cancelButton: "btn btn-outline-secondary btn-lg"
            }
        }).then(function (result) {
            if (result.value) {
                $.ajax({
                    type: 'POST',
                    url: '/ajax/orders/delete',
                    data: {
                        pId: pId,
                        orderId: orderId
                    },
                    success: function (data) {
                        if (data.result) {
                            toastr.success("Заказ удален");
                        } else {
                            toastr.error("Не удалось удалить заказ");
                        }
                    }
                });

                setTimeout(function () {
                    updateOrders(true);
                }, 2000);
            }
        });

    });

    // Изменение масштаба журнала
    $('.changeJournalDataCell').on('click', function () {
        let cellDuration = $(this).data('duration');
        let intervalText = $(this).text();
        var scheduler = $("#tableJs").dxScheduler("instance");
        $('.jo-current-interval').text(intervalText);
        switchCurrentScaleByInterval(cellDuration);

        Cookies.set('jo-Scale', currentScale);

        scheduler.option('views[0].cellDuration', Settings.cellMinutes);
        scheduler.option('dataSource', Orders);
    });

    $('#joDatepickerInput').datepicker({
        todayHighlight: true,
        orientation: "bottom right",
        format: "yyyy-mm-dd",
        language: 'ru',
        weekStart: 1,
        maxDate: "+30D",
        autoclose: true,
    });

    /**
     * Загружаем журнал при изменении дня в календаре
     */
    $('#joDatepickerInput').datepicker().on('changeDate', function (e) {
        KTApp.block('#tableWrapperBlockUi', {
            overlayColor: '#000000',
            state: 'primary',
            message: 'Загружаем...'
        });

        currentDate = e.date;
        changeCalendarCurrentDay(currentDate);
        scheduler.option('currentDate', currentDate);
        updateShortOrdersResize();
        let localeDate = currentDate.toLocaleDateString(); // format: 25.10.2021
        Promise.all([getSettings(localeDate), getOrders(localeDate)]).then(() => {
            console.log('Все замечательно! Рад что вы тут!');

            let savedScale = Cookies.get('jo-Scale');
            if (savedScale && savedScale != 1) {
                currentScale = parseInt(savedScale);
                let scaleText = getScaleText(currentScale);
                $('.jo-current-interval').text(scaleText);
            }
            updateCurrentJournalSettings();

            initCalendar();
            setTimeout(function () {
                KTApp.unblock('#tableWrapperBlockUi');
            }, 100);
        }).catch(() => {
            toastr.error('Проблемы с получением данных журнала');
            KTApp.unblock('#tableWrapperBlockUi');
        })
    });


    $('.jo-calendar-current-day, .jo-calendar-open').on('click', function () {
        jQuery('#joDatepickerInput').datepicker("show");
    });

    /**
     * Переключаем календарь на следующий день
     */
    $('.jo-calendar--next-day').click(function () {
        let date = $('#joDatepickerInput').datepicker('getDate');
        if (date == null) {
            date = new Date();
        }
        date.setDate(date.getDate() + 1);
        currentDate = date;

        $('#joDatepickerInput').datepicker('setDate', date);
    })

    /**
     * Переключаем календарь на предыдущий день
     */
    $('.jo-calendar--prev-day').click(function () {
        let date = $('#joDatepickerInput').datepicker('getDate');
        if (date == null) {
            date = new Date();
        }
        date.setDate(date.getDate() - 1);
        currentDate = date;

        $('#joDatepickerInput').datepicker('setDate', date);
    })

    /**
     * Меняем день в виджете календаря с учетом переводов
     * @param date - object Date
     */
    function changeCalendarCurrentDay(date) {
        let day = date.getDay();
        let month = date.getMonth();
        let monthDate = date.getDate();

        let dateString = translateShortDaysOfWeek(day) + ', ' + monthDate + ' ' + translateShortMonth(month);
        $('.jo-calendar-current-day').text(dateString);
    }

    /**
     * Переводы дней недели для виджета календаря
     * @param day
     * @returns {string}
     */
    function translateShortDaysOfWeek(day) {
        let dayTranslate = {
            0: 'Вс',
            1: 'Пн',
            2: 'Вт',
            3: 'Ср',
            4: 'Чт',
            5: 'Пт',
            6: 'Сб',
        };
        return dayTranslate[day] ?? '';
    }

    /**
     * Переводы месяцев для виджета календаря
     * @param month
     * @returns {string}
     */
    function translateShortMonth(month) {
        let monthTranslate = {
            0: 'янв',
            1: 'фев',
            2: 'мар',
            3: 'апр',
            4: 'мая',
            5: 'июня',
            6: 'июля',
            7: 'авг',
            8: 'сен',
            9: 'окт',
            10: 'ноя',
            11: 'дек',
        };
        return monthTranslate[month] ?? '';
    }

    /**
     * Убираем возможность изменения размера с short-записей
     */
    function updateShortOrdersResize() {
        $('.jo.journal-order-short').each(function (i, item) {
            let wrapper = $(item).closest('.dx-item.dx-scheduler-appointment');

            if (wrapper) {
                console.log('Удалили ресайз на short-записи (меньше размера ячейки)');
                wrapper.removeClass('dx-resizable');
                wrapper.find('.dx-resizable-handle').remove();
            }
        });

        $('.jo.jo-order-two-dates').each(function (i, item) {
            let wrapper = $(item).closest('.dx-item.dx-scheduler-appointment');

            if (wrapper) {
                console.log('Удалили ресайз на записи перенесенной на день (2 дня запись)');
                wrapper.removeClass('dx-resizable');
                wrapper.find('.dx-resizable-handle').remove();
            }
        });
    }
})