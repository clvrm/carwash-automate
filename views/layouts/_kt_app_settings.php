<?php
/**
 * Глобальная конфигурация Metronic (KTAppSettings).
 * Цвета читаются из CSS-переменных — единый источник в theme-variables.css.
 */
?>
<script>var HOST_URL = "https://preview.keenthemes.com/metronic/theme/html/tools/preview";</script>
<script>
(function () {
    var s = getComputedStyle(document.documentElement);
    function v(name) {
        return s.getPropertyValue(name).trim();
    }

    window.KTAppSettings = {
        "breakpoints": {"sm": 576, "md": 768, "lg": 992, "xl": 1200, "xxl": 1400},
        "colors": {
            "theme": {
                "base": {
                    "white": "#ffffff",
                    "primary": v('--sc-primary'),
                    "secondary": v('--sc-secondary'),
                    "success": v('--sc-success'),
                    "info": v('--sc-info'),
                    "warning": v('--sc-warning'),
                    "danger": v('--sc-danger'),
                    "light": v('--sc-light'),
                    "dark": v('--sc-dark')
                },
                "light": {
                    "white": "#ffffff",
                    "primary": v('--sc-primary-light'),
                    "secondary": v('--sc-gray-200'),
                    "success": v('--sc-success-light'),
                    "info": v('--sc-info-light'),
                    "warning": v('--sc-warning-light'),
                    "danger": v('--sc-danger-light'),
                    "light": v('--sc-gray-100'),
                    "dark": v('--sc-gray-300')
                },
                "inverse": {
                    "white": "#ffffff",
                    "primary": "#ffffff",
                    "secondary": v('--sc-gray-800'),
                    "success": "#ffffff",
                    "info": "#ffffff",
                    "warning": "#ffffff",
                    "danger": "#ffffff",
                    "light": v('--sc-gray-600'),
                    "dark": "#ffffff"
                }
            },
            "gray": {
                "gray-100": v('--sc-gray-100'),
                "gray-200": v('--sc-gray-200'),
                "gray-300": v('--sc-gray-300'),
                "gray-400": v('--sc-gray-400'),
                "gray-500": v('--sc-gray-500'),
                "gray-600": v('--sc-gray-600'),
                "gray-700": v('--sc-gray-700'),
                "gray-800": v('--sc-gray-800'),
                "gray-900": v('--sc-gray-900')
            }
        },
        "font-family": "Poppins"
    };
})();
</script>
