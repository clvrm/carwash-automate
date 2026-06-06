let currentDevice = false; // Тип устройства mobile / desktop

$(document).ready(function () {
    if (currentDevice === false) {
        if (window.innerWidth <= 992) {
            currentDevice = 'mobile';

            // В случае наличия подменю на странице, отображаем его вместо контента
            if ($('#fixed_header_mobile').length) {
                let backButton = $('.mobile-fixed-menu-back');
                if (backButton.data('parent') != undefined) {
                    $('#kt_header_mobile').hide();
                }
                let defaultOpenedId = $('#fixed_header_mobile').data('defaultId');
                if (defaultOpenedId == 'not-set') {
                    $('#fixed_header_mobile').find('.mobile-fixed-menu').first().show(100);
                }
                let isOnlyBack = $('#fixed_header_mobile').data('onlyBack');
                // Если передан только урл для возврата - устанавливаем заголовок текущей страницы
                if (isOnlyBack !== 0) {
                    $('#kt_header_mobile').hide();
                    $('#fixed_header_mobile .mobile-fixed-menu--title').text($('#kt_header_menu .header-page-title').text());
                } else if (defaultOpenedId !== 'not-set' && $('.mobile-fixed-menu[data-parent="' + defaultOpenedId + '"]').length == 0) {
                    $('.mobile-fixed-menu').hide();
                    $('#kt_header_mobile').hide();
                    backButton.data('parent', defaultOpenedId);
                    $('#fixed_header_mobile .mobile-fixed-menu--title').text($('#kt_header_menu .header-page-title').text());

                } else {
                    $('#contentWrapper').hide();
                }
            }
        } else {
            currentDevice = 'desktop';
        }
    }


    $('.mobile-fixed-menu-item').on('click', function (e) {
        var currentId = $(this).data('id');
        var currentTitle = $(this).text();
        $(this).closest('.mobile-fixed-menu').hide('100');

        $('.mobile-fixed-menu-back').data('parent', currentId);
        $('#kt_header_mobile').hide();
        $('#fixed_header_mobile .mobile-fixed-menu--title').text(currentTitle);

        var currentUrl = $(this).data('url');

        // В случае, если меню вложенное
        if ($('.mobile-fixed-menu[data-parent="' + currentId + '"]').length) {
            $('.mobile-fixed-menu[data-parent="' + currentId + '"]').show(300);
        } else if (currentUrl.length > 1 && currentUrl.substr(0, 1) == '#') {
            // Кейс, при котором у нас в ссылке указан ID элемента на странице (единая страница разбитая на блоки)
            hideAllMobileFixed();
            $(currentUrl).show(300);
            $('#contentWrapper').show()
        } else if(currentUrl.length > 1 && currentUrl.substr(0, 1) == '/'){
            window.location.href = currentUrl.toString();
        }
    });

    $('.mobile-fixed-menu-back').on('click', function (e) {
        hideAllMobileFixed();
        $('#contentWrapper').hide();
        var parentId = $(this).data('parent');
        $('.mobile-fixed-menu').hide();
        var currentParent = $('.mobile-fixed-menu-item[data-id="' + parentId + '"]').closest('.mobile-fixed-menu');
        var currentParentId = currentParent.data('parent');

        var parentTitle = $('.mobile-fixed-menu-item[data-id="' + currentParentId + '"]').text();
        $('#fixed_header_mobile .mobile-fixed-menu--title').text(parentTitle);

        if (currentParentId == undefined) {
            $('#kt_header_mobile').show();
        } else {
            $('.mobile-fixed-menu-back').data('parent', currentParentId);
        }
        currentParent.show(300);
    });

    function hideAllMobileFixed() {
        $('.mobile-fixed-menu-item').each(function (e) {
            var url = $(this).data('url');

            // Если урл это якорь на странице
            if (url.length > 1 && url[0] == '#' && $(url)) {
                $(url).hide();
            }

        });
    }

    function showAllMobileFixed() {
        $('.mobile-fixed-menu-item').each(function (e) {
            var url = $(this).data('url');

            // Если урл это якорь на странице
            if (url.length > 1 && url[0] == '#' && $(url)) {
                $(url).show();
            }
        });
    }

    $(window).on('resize', function () {
        if (window.innerWidth <= 991 && currentDevice === 'desktop') {
            currentDevice = 'mobile';
            if ($('#fixed_header_mobile').length) {
                $('.mobile-fixed-menu').hide();
                hideAllMobileFixed();
                let isOnlyBack = $('#fixed_header_mobile').data('onlyBack');
                // Если передан только урл для возврата - устанавливаем заголовок текущей страницы
                if (isOnlyBack !== 0) {
                    $('#kt_header_mobile').hide();
                } else {
                    $('#contentWrapper').hide();
                }
                $('#fixed_header_mobile .mobile-fixed-menu--title').text($('#kt_header_menu .header-page-title').text());

                $('#fixed_header_mobile').show();
                $('#fixed_header_mobile').find('.mobile-fixed-menu').first().show(100);
            }

        } else if (currentDevice === 'mobile' && window.innerWidth >= 992) {
            currentDevice = 'desktop';
            if ($('#fixed_header_mobile').length) {
                showAllMobileFixed();
                $('#contentWrapper').show();
                $('#fixed_header_mobile').hide();
            }

        }
    });

    // Переключение вложенных вкладок в карточке
    $('.card-tab-ballon').on('click', function () {
        var tabId = $(this).data('switchTab');
        var card = $(this).closest('.card');
        card.find('.card-tab-ballon').removeClass('card-tab-active');
        $(this).addClass('card-tab-active');

        card.find('.card-switch-tab').hide(200);
        card.find('.card-switch-tab[data-tab="' + tabId + '"]').show(300);
    });

    // Datepicker - базовая инициализация
    $('.date-picker input').datepicker({
        format: "yyyy-mm-dd",
        rtl: KTUtil.isRTL(),
        language: 'ru',
        todayHighlight: true,
        orientation: "bottom left",
        weekStart: 1
    });

    $('.date-picker-range input').daterangepicker({
        locale: {
            direction: 'ltr',
            format: 'YYYY-MM-DD',
            separator: ' - ',
            applyLabel: 'Применить',
            cancelLabel: 'Отменить',
            weekLabel: 'W',
            customRangeLabel: 'Интервал',
            daysOfWeek: ['Вск', 'Пон', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб'],
            monthNames: ['Янв', 'Фев', 'Март', 'Апр', 'Май', 'Июнь', 'Июль', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'],
            firstDay: 1
        },
        maxDate: new Date(),
        format: "yyyy-mm-dd",
        buttonClasses: ' btn',
        applyClass: 'btn-primary',
        cancelClass: 'btn-secondary'
    });

    // Валидашка номеров автомобилей
    let carNumberOldValue = '';
    $('input.car-number-validator').on('input', function (e) {
        let val = $(this).val().toUpperCase();
        $(this).val(val);
        if (val.match(/[^a-zA-Zа-яА-Я0-9]/g)) {
            $(this).val(carNumberOldValue);
            return;
        }
        carNumberOldValue = val;

        let error = false;
        if (!validateCarNumberType1(val)) {
            error = true;
        }

        if (error == true) {
            $(this).css('color', 'darkred');
        } else {
            $(this).css('color', 'green');
        }
    });

    // Валидируем номер автомобиля по первому типу авто
    function validateCarNumberType1(val) {
        if (val.length === 1 && !val.match(/^(а|в|е|к|м|н|о|р|с|т|у|х|a|b|e|k|m|h|o|p|c|t|y|x){1}$/gi)) {
            return false;
        }
        if (val.length === 2 && !val.match(/^(а|в|е|к|м|н|о|р|с|т|у|х|a|b|e|k|m|h|o|p|c|t|y|x){1}[0-9]{1}$/gi)) {
            return false;
        }
        if (val.length === 3 && !val.match(/^(а|в|е|к|м|н|о|р|с|т|у|х|a|b|e|k|m|h|o|p|c|t|y|x){1}[0-9]{2}$/gi)) {
            return false;
        }
        if (val.length === 4 && !val.match(/^(а|в|е|к|м|н|о|р|с|т|у|х|a|b|e|k|m|h|o|p|c|t|y|x){1}[0-9]{3}$/gi)) {
            return false;
        }
        if (val.length === 5 && !val.match(/^(а|в|е|к|м|н|о|р|с|т|у|х|a|b|e|k|m|h|o|p|c|t|y|x){1}[0-9]{3}(а|в|е|к|м|н|о|р|с|т|у|х|a|b|e|k|m|h|o|p|c|t|y|x){1}$/gi)) {
            return false;
        }
        if (val.length === 6 && !val.match(/^(а|в|е|к|м|н|о|р|с|т|у|х|a|b|e|k|m|h|o|p|c|t|y|x){1}[0-9]{3}(а|в|е|к|м|н|о|р|с|т|у|х|a|b|e|k|m|h|o|p|c|t|y|x){2}$/gi)) {
            return false;
        }

        return true;
    }


    // function validateCarNumber(value){
    //     let match = value.match(/(([АВЕКМНОРСТУХ][0-9]{3}[АВЕКМНОРСТУХ]{2})|([АВЕКМНОРСТУХ]{2}[0-9]{4}))$/);
    //     if (match != null){
    //         return true;
    //     }
    //     return false;
    // }

    $('.form-selectpicker').selectpicker();

    $('.default-select2').select2({})
    $('.default-select2-dropdown').select2({minimumResultsForSearch: -1})

    var avatar1 = new KTImageInput('profile_avatar_uploader');

    logPersonalAction();

    function logPersonalAction() {
        console.log('start-log');
        let mainInfoBlock = $('#mainInfoBlock');
        let pId = mainInfoBlock.data('p-id');
        let logEvent = mainInfoBlock.data('log-event');
        let logData = mainInfoBlock.data('log-data');
        let logUrl = mainInfoBlock.data('log-url') ?? window.location.pathname;
        console.log('url:' + logUrl);
        $.ajax({
            type: 'POST',
            url: '/ajax/personal/log-event',
            data: {pId: pId, event: logEvent, data: logData, url: logUrl},
            success: function (data) {
                if (data) {
                    console.log('Залогировали действие');
                } else {
                    console.log(data);
                    console.log('Ошибка при логировании действие');
                }
            }
        });
        console.log('end-log');
    }
})


// Профиль


