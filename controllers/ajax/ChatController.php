<?php

namespace app\controllers\ajax;

use app\models\ar\carwash\Carwash;
use app\models\ar\chat\Chat;
use app\models\ar\chat\ChatMessages;
use app\models\ar\complex\Complexes;
use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalLog;
use app\models\ar\service\Services;
use app\models\ar\ticket\TicketMessages;
use app\models\ar\ticket\Tickets;
use yii\base\BaseObject;
use yii\web\Controller;
use Yii;

/**
 * Class ChatController
 * @package app\controllers\ajax
 */
class ChatController extends Controller
{
    public $enableCsrfValidation = false;

    public function beforeAction($action)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    public function actionGetChat($id, $pId)
    {
        $chat = Chat::findOne($id);

        if (!$chat) {
            return ['result' => false, 'message' => 'Чат не найден'];
        }
        $personal = Personal::find()->where(['carwash_id' => $chat->carwash_id, 'id' => $pId])->one();
        if (!$personal) {
            return ['result' => false, 'message' => 'Чат не закреплен за данной автомойкой'];
        }
        $chatMessages = ChatMessages::find()->where(['chat_id' => $chat->id])->orderBy('created_at ASC')->all();
        foreach ($chatMessages as $chatMessage) {
            // Если это клиентские, непросмотренные сообщения - логируем что сотрудник их просмотрел
            if ($chatMessage->is_viewed == ChatMessages::NOT_VIEWED && isset($chatMessage->client_id)) {
                $chatMessage->is_viewed = ChatMessages::IS_VIEWED;
                $chatMessage->save();
            }
        }
        return ['result' => true, 'chat' => $chat, 'messages' => $chatMessages];
    }


    public function actionSendMessage()
    {
        $text = Yii::$app->request->post('text');
        $chatId = Yii::$app->request->post('chatId');
        $pId = Yii::$app->request->post('pId');

        $chat = Chat::findOne($chatId);
        if (!$chat) {
            return ['result' => false, 'message' => 'Чат не найден'];
        }
        $personal = Personal::find()->where(['carwash_id' => $chat->carwash_id, 'id' => $pId])->one();
        if (!$personal) {
            return ['result' => false, 'message' => 'Чат не закреплен за данной автомойкой'];
        }
        if ($chat->carwash_id !== $personal->carwash_id) {
            return ['result' => false, 'message' => 'Чат не принадлежит данной автомойке'];
        }
        $model = new ChatMessages();
        $model->chat_id = $chatId;
        $model->personal_id = $pId;
        $model->text = $text;
        if ($model->save()) {
            try {
                $log = new PersonalLog();
                $log->createLog($pId, '', PersonalLog::SEND_MESSAGE_CHAT);
            } catch (\Exception $exception) {
            }
            return ['result' => true, 'message' => 'Сообщение отправлено'];
        }
        return ['result' => false, 'message' => 'Внутренняя ошибка'];
    }

    public function actionGetTicket($id, $pId)
    {
        $ticket = Tickets::findOne($id);

        if (!$ticket) {
            return ['result' => false, 'message' => 'Тикет не найден'];
        }
        $personal = Personal::find()->where(['carwash_id' => $ticket->carwash_id, 'id' => $pId])->one();
        if (!$personal) {
            return ['result' => false, 'message' => 'Тикет не закреплен за данной автомойкой'];
        }
        $ticketMessages = TicketMessages::find()->where(['ticket_id' => $ticket->id])->orderBy('created_at ASC')->all();

        return ['result' => true, 'ticket' => $ticket, 'messages' => $ticketMessages];
    }

    public function actionSendTicketMessage()
    {
        $text = Yii::$app->request->post('text');
        $ticketId = Yii::$app->request->post('ticketId');
        $pId = Yii::$app->request->post('pId');

        $ticket = Tickets::findOne($ticketId);
        if (!$ticket) {
            return ['result' => false, 'message' => 'Тикет не найден'];
        }
        $personal = Personal::find()->where(['carwash_id' => $ticket->carwash_id, 'id' => $pId])->one();
        if (!$personal) {
            return ['result' => false, 'message' => 'Тикет не закреплен за данной автомойкой'];
        }
        if ($ticket->carwash_id !== $personal->carwash_id) {
            return ['result' => false, 'message' => 'Тикет не принадлежит данной автомойке'];
        }
        $model = new TicketMessages();
        $model->ticket_id = $ticketId;
        $model->personal_id = $pId;
        $model->text = $text;
        if ($model->save()) {
            try {
                $log = new PersonalLog();
                $log->createLog($pId, '', PersonalLog::SEND_MESSAGE_CHAT_SUPPORT);
            } catch (\Exception $exception) {
            }
            return ['result' => true, 'message' => 'Сообщение отправлено'];
        }
        return ['result' => false, 'message' => 'Внутренняя ошибка'];
    }

    public function actionCreateTicket()
    {
        $title = Yii::$app->request->post('title');
        $pId = Yii::$app->request->post('pId');

        $personal = Personal::find()->where(['id' => $pId])->one();
        if (!$personal) {
            return ['result' => false, 'message' => 'Тикет не закреплен за данной автомойкой'];
        }
        $ticket = new Tickets();
        $ticket->text = $title;
        $ticket->personal_id = $pId;
        $ticket->carwash_id = $personal->carwash_id;
        if($ticket->save()){
            return ['result' => true];
        }
        return ['result' => false, 'message' => 'Не удалось создать новый тикет. Повторите попытку позднее'];
    }

}