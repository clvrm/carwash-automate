<?php

namespace app\commons\models;

use app\commons\logs\db\EventLogger;
use app\models\ar\logs\EventLog;
use Yii;

class MailSender
{
    private const DEFAULT_EMAIL_FROM = 'noreply@localhost';
    private const DEFAULT_EMAIL_FROM_NAME = 'Carwash';

    private function baseUrl(): string
    {
        return rtrim(Yii::$app->params['app.baseUrl'] ?? '', '/');
    }

    public function sendRegisterEmail($email, $userId, $personalId)
    {
        $registerCode = $this->generateCode();
        EventLogger::system(EventLog::SYS_USER_REGISTER_EMAIL, "$registerCode", $userId, $personalId);

        $url = $this->baseUrl() . '/auth/register-submit?code=' . $registerCode;
        $text = 'Вы зарегистрировались в Carwash. <br> Для подтверждения регистрации перейдите по ссылке: <a href="' . $url . '">' . $url . '</a>';

        Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom([self::DEFAULT_EMAIL_FROM => self::DEFAULT_EMAIL_FROM_NAME])
            ->setSubject('Регистрация в Carwash')
            ->setHtmlBody($text)
            ->send();

        return true;
    }

    public function sendResetEmail($email, $userId, $token)
    {
        EventLogger::system(EventLog::SYS_USER_RESET_EMAIL, "$token", $userId);

        $url = $this->baseUrl() . '/auth/change-password?resetCode=' . $token;
        $text = 'Для изменения пароля используйте ссылку: <a href="' . $url . '">' . $url . '</a>';

        Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom([self::DEFAULT_EMAIL_FROM => self::DEFAULT_EMAIL_FROM_NAME])
            ->setSubject('Сброс пароля в Carwash')
            ->setHtmlBody($text)
            ->send();

        return true;
    }

    public function sendInviteEmail($email, $userId, $carwashId, $personalId)
    {
        EventLogger::system(EventLog::SYS_USER_INVITE_EMAIL, "$carwashId", $userId, $personalId);

        $url = $this->baseUrl() . '/auth/accept-invite?pid=' . $personalId;
        $text = 'Ссылка для приглашения пользователя: <a href="' . $url . '">' . $url . '</a>';

        Yii::$app->mailer->compose()
            ->setTo($email)
            ->setFrom([self::DEFAULT_EMAIL_FROM => self::DEFAULT_EMAIL_FROM_NAME])
            ->setSubject('Приглашение в Carwash')
            ->setHtmlBody($text)
            ->send();

        return true;
    }

    private function generateCode($length = 10)
    {
        return Yii::$app->security->generateRandomString($length);
    }

    public function sendAssignPersonalEmail()
    {
    }
}
