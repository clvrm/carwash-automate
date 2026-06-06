<?php


namespace app\commons\helpers;

/**
 * Class GlobalTranslates
 * @package app\commons\helpers
 */
class GlobalTranslates
{
    /**
     * @param $month
     * @param bool $short
     * @return string
     */
    public static function getMonthLabel($month, bool $short = false) : string
    {
        $label = [
            'январь',
            'февраль',
            'март',
            'апрель',
            'май',
            'июнь',
            'июль',
            'август',
            'сентябрь',
            'октябрь',
            'ноябрь',
            'декабрь'
        ];
        return $label[$month - 1] ?? '';
    }
}