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
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
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