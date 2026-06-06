<?php

namespace app\commons\notification;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Yii;

class FirebaseHelper
{
    protected $messaging;

    public function __construct()
    {
        $credentialsPath = Yii::$app->params['firebase']['credentials'] ?? null;
        if ($credentialsPath === null) {
            $credentialsPath = __DIR__ . '/firebase/credentials.json';
        }

        if (!is_file($credentialsPath)) {
            return;
        }

        $factory = (new Factory())->withServiceAccount($credentialsPath);
        $this->messaging = $factory->createMessaging();
    }

    public function sendMessage($token, $title, $body = '', $data = [])
    {
        if ($this->messaging === null) {
            Yii::warning('Firebase credentials not configured, push skipped', 'firebase');
            return;
        }

        $message = CloudMessage::withTarget('token', $token)
            ->withNotification(Notification::create($title, $body));
        if ($data) {
            $message->withData($data);
        }

        $this->messaging->send($message);
    }
}
