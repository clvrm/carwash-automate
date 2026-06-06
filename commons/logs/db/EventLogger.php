<?php


namespace app\commons\logs\db;


use app\models\ar\logs\EventLog;

/**
 * Class EventLogger
 * @package app\commons\logs\db
 */
class EventLogger
{
    /**
     * @param $event
     * @param string $data
     * @param null $userId
     * @param null $personalId
     * @return bool
     */
    public static function error($event, $data = '', $userId = null, $personalId = null): bool
    {
        $log = new EventLog();
        $log->type = EventLog::TYPE_ERROR;
        $log->event = $event;
        $log->data = $data;
        $log->user_id = $userId;
        $log->personal_id = $personalId;

        return $log->save();
    }

    /**
     * @param $event
     * @param string $data
     * @param null $userId
     * @param null $personalId
     * @return bool
     */
    public static function debug($event, $data = '', $userId = null, $personalId = null): bool
    {
        $log = new EventLog();
        $log->type = EventLog::TYPE_DEBUG;
        $log->event = $event;
        $log->data = $data;
        $log->user_id = $userId;
        $log->personal_id = $personalId;

        return $log->save();
    }

    /**
     * @param $event
     * @param string $data
     * @param null $userId
     * @param null $personalId
     * @return bool
     */
    public static function info($event, $data = '', $userId = null, $personalId = null): bool
    {
        $log = new EventLog();
        $log->type = EventLog::TYPE_INFO;
        $log->event = $event;
        $log->data = $data;
        $log->user_id = $userId;
        $log->personal_id = $personalId;

        return $log->save();
    }

    /**
     * @param $event
     * @param string $data
     * @param null $userId
     * @param null $personalId
     * @return bool
     */
    public static function system($event, $data = '', $userId = null, $personalId = null): bool
    {
        $log = new EventLog();
        $log->type = EventLog::TYPE_SYSTEM;
        $log->event = $event;
        $log->data = $data;
        $log->user_id = $userId;
        $log->personal_id = $personalId;

        return $log->save();
    }
}