<?php


namespace app\commons\models\analytics\factory;


/**
 * Class SpreadsheetCreator
 * @package app\commons\models\analytics\factory
 */
class SpreadsheetCreator
{
    protected const DEFAULT_TEMPLATE_ALIAS = 'templates/';
    protected const TEMPLATE_NAME = '';
    protected const START_ROW = 4;

    /**
     * @return false
     */
    public function printDocument()
    {
        return false;
    }

    /**
     * @return string
     */
    protected function getTemplateFile()
    {
        $path = \Yii::getAlias('@analytics') . self::DEFAULT_TEMPLATE_ALIAS . static::TEMPLATE_NAME;

        return $path;
    }
}