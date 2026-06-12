<?php


namespace app\commons\helpers;

/**
 * Class TimeHelper
 * @package app\commons\helpers
 */
class TimeHelper
{
    public const MINUTES_PER_DAY = 1440;

    /**
     * Конвертация времени в формате H:i (14:15) в количество минут
     * @param $time
     * @return int
     */
    public static function convertTimeToMin($time)
    {
        [$hours, $minutes] = explode(":", $time);

        $totalMinutes = ($hours * 60) + $minutes;
        return $totalMinutes;
    }

    /**
     * Конвертация минут в формат H:i
     * @param $minutes
     * @return false|string
     */
    public static function convertMinToTime($minutes)
    {
        return date('H:i', mktime(0, $minutes));
    }

    /**
     * @param $date
     * @return bool
     */
    public static function isValidDate($date): bool
    {
        if (!is_string($date) || $date === '') {
            return false;
        }

        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Диапазон дат из GET-параметров или значения по умолчанию.
     *
     * @return array{0: string, 1: string}
     */
    public static function resolveDateRange(string $defaultFrom, string $defaultTo, $dateFrom = null, $dateTo = null): array
    {
        if ($dateFrom === null) {
            $dateFrom = \Yii::$app->request->get('dateFrom');
        }
        if ($dateTo === null) {
            $dateTo = \Yii::$app->request->get('dateTo');
        }

        if (self::isValidDate($dateFrom) && self::isValidDate($dateTo)) {
            return [$dateFrom, $dateTo];
        }

        return [$defaultFrom, $defaultTo];
    }

    /**
     * @param string $dateFormat
     * @param int $timezone - отклонение от нуля
     * @return false|string
     */
    public static function getCurrentDateBasedOnTimezone(string $dateFormat, int $timezone)
    {
        return date($dateFormat, strtotime(date('Y-m-d H:i:s') . '+ ' . $timezone . ' hour'));
    }
}