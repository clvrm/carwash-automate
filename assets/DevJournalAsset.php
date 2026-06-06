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
class DevJournalAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        '/css/dx.light.css',
        '/css/journal.css',
    ];
    public $js = [
        '/js/dx.all.js',
        '/js/journal.js',
    ];
    public $depends = [
        'app\assets\BaseAppAsset',
    ];
}
