<?php

namespace app\modules\api\commons\v1;


/**
 * Класс-помощник, для определения кодов возврата от API
 */
class ReturnCodes
{
    public const ATTRIBUTES_ERROR = 400;
    public const ACCESS_FORBIDDEN = 403;
    public const NOT_FOUND = 404;
    public const CANT_CREATE = 405;
    public const INVALID_TOKEN = 420;
    public const SERVER_ERROR = 500;
    public const EXTERNAL_SERVER_ERROR = 503;
}