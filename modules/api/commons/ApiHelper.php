<?php

namespace app\modules\api\commons;

use app\models\ar\personal\Personal;
use app\models\ar\Users;

class ApiHelper
{

    /**
     * @param $token
     * @param $personalId
     * @return bool
     */
    public static function tokenValidator($token, $personalId): bool
    {
        $user = Users::findOne(['auth_token' => $token]);
        if (!$user){
            return false;
        }
        $personal = Personal::findOne(['id' => $personalId, 'user_id' => $user->id]);
        if (!$personal){
            return false;
        }

        return true;
    }
}