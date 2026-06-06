<?php


namespace app\controllers\ajax;


use app\commons\exceptions\LogicException;
use app\models\ar\carwash\Carwash;
use app\models\ar\carwash\CarwashSales;
use app\models\ar\personal\Personal;
use app\models\ar\personal\PersonalLog;
use yii\web\Controller;
use Yii;
use yii\web\NotAcceptableHttpException;

/**
 * Class PersonalController
 * @package app\controllers\ajax
 */
class PersonalController extends Controller
{

    public $enableCsrfValidation = false;

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws \yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        return parent::beforeAction($action);
    }

    /**
     * Логирование действий персонала
     * @return bool
     * @throws LogicException
     */
    public function actionLogEvent(): bool
    {
        $personalId = Yii::$app->request->post('pId');
        $event = Yii::$app->request->post('event') ?? '';
        $data = Yii::$app->request->post('data') ?? '';
        $url = Yii::$app->request->post('url') ?? '';
        if (!$personalId) {
            throw new LogicException('Не указан персонал для логирования');
        }

        $log = new PersonalLog();
        $log->createLog($personalId, $url, $event, $data);

        return true;
    }

    /**
     * @return bool[]|false[]
     * @throws NotAcceptableHttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDeletePersonal()
    {
        if (!Yii::$app->request->isPost) {
            throw new NotAcceptableHttpException('Запрещен доступ');
        }
        $id = Yii::$app->request->post('id');
        // Для дополнительной проверки, является ли связанным с данной мойкой
        $cwId = Yii::$app->request->post('cwId');

        $personal = Personal::findOne(['id' => $id, 'carwash_id' => $cwId]);
        if ($personal) {
            // TODO: Удаление связанных данных
            $carwash = Carwash::findOne($cwId);
            if ($carwash && $carwash->owner_id == $personal->id) {
                return ['result' => false]; // Нельзя удалить владельца
            }
            try {
                $log = new PersonalLog();
                $log->createLog($personal->id, '', PersonalLog::DELETE_PERSONAL, 'Удален сотрудник ' . $personal->user_id);
            } catch (\Exception $exception) {
            }
            $personal->delete();

            return ['result' => true];
        }
        return ['result' => false];
    }


}