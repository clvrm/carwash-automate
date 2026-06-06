<?php

namespace app\assets;

use yii\web\AssetBundle;

class BaseAppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap',
        '/plugins/global/plugins.bundle.css',
        '/plugins/custom/prismjs/prismjs.bundle.css',
        'css/style.bundle.css',
        '/css/themes/layout/header/base/light.css',
        '/css/themes/layout/header/menu/light.css',
        '/css/themes/layout/brand/light.css',
        '/css/themes/layout/aside/light.css',
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];

    public $js = [
        '/plugins/global/plugins.bundle.js',
        '/plugins/custom/prismjs/prismjs.bundle.js',
        '/js/pages/crud/forms/widgets/bootstrap-maxlength.js',
        '/js/js.cookie-2.2.1.min.js',
        '/js/scripts.bundle.js',
        '/js/main.js',
        '/js/pages/widgets.js',
        'https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700',

    ];
    public $depends = [
        'yii\web\YiiAsset',
//        'yii\bootstrap\BootstrapAsset',
    ];
}
