<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class JournalAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '/css/journal.css'
    ];
    public $js = [
        'https://unpkg.com/react@17/umd/react.development.js',
        'https://unpkg.com/react-dom@17/umd/react-dom.development.js',
        'https://unpkg.com/@babel/standalone/babel.min.js',
//        '/js/react-grid-layout.js',
//        '/js/journal.js',
    ];
    public $depends = [
        'app\assets\BaseAppAsset',
    ];
}
