<?php

namespace app\models\ar\carwash;

use app\commons\exceptions\ErrorCalculateSchedule;
use Yii;

/**
 * This is the model class for table "carwash_schedule".
 *
 * @property int $id
 * @property int|null $carwash_id
 * @property string|null $monday_start
 * @property string|null $monday_end
 * @property string|null $tuesday_start
 * @property string|null $tuesday_end
 * @property string|null $wednesday_start
 * @property string|null $wednesday_end
 * @property string|null $thursday_start
 * @property string|null $thursday_end
 * @property string|null $friday_start
 * @property string|null $friday_end
 * @property string|null $saturday_start
 * @property string|null $saturday_end
 * @property string|null $sunday_start
 * @property string|null $sunday_end
 * @property bool|null $is_work_monday
 * @property bool|null $is_work_tuesday
 * @property bool|null $is_work_wednesday
 * @property bool|null $is_work_thursday
 * @property bool|null $is_work_friday
 * @property bool|null $is_work_saturday
 * @property bool|null $is_work_sunday
 * @property string|null $updated_at
 * @property string|null $created_at
 *
 * @property Carwash $carwash
 */
class CarwashSchedule extends \yii\db\ActiveRecord
{
    /**
     *
     */
    public const MIN_START_TIME = '00:00';
    /**
     *
     */
    public const MAX_END_TIME = '23:59';


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carwash_schedule';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['carwash_id'], 'integer'],
            [['monday_start', 'monday_end', 'tuesday_start', 'tuesday_end', 'wednesday_start', 'wednesday_end', 'thursday_start', 'thursday_end', 'friday_start', 'friday_end', 'saturday_start', 'saturday_end', 'sunday_start', 'sunday_end', 'updated_at', 'created_at'], 'safe'],
            [['is_work_monday', 'is_work_tuesday', 'is_work_wednesday', 'is_work_thursday', 'is_work_friday', 'is_work_saturday', 'is_work_sunday'], 'boolean'],
            [['carwash_id'], 'exist', 'skipOnError' => true, 'targetClass' => Carwash::className(), 'targetAttribute' => ['carwash_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'carwash_id' => 'ID автомойки',
            'monday_start' => 'Начало - понедельник',
            'monday_end' => 'Конец - понедельник',
            'tuesday_start' => 'Начало - вторник',
            'tuesday_end' => 'Конец - вторник',
            'wednesday_start' => 'Начало - среда',
            'wednesday_end' => 'Конец - среда',
            'thursday_start' => 'Начало - четверг',
            'thursday_end' => 'Конец - четверг',
            'friday_start' => 'Начало - пятница',
            'friday_end' => 'Конец - пятница',
            'saturday_start' => 'Начало - суббота',
            'saturday_end' => 'Конец - суббота',
            'sunday_start' => 'Начало - воскресенье',
            'sunday_end' => 'Конец - воскресенье',
            'is_work_monday' => 'Рабочий понедельник?',
            'is_work_tuesday' => 'Рабочий вторник?',
            'is_work_wednesday' => 'Рабочая среда?',
            'is_work_thursday' => 'Рабочий четверг?',
            'is_work_friday' => 'Рабочая пятница?',
            'is_work_saturday' => 'Рабочая суббота?',
            'is_work_sunday' => 'Рабочее воскресенье?',
            'updated_at' => 'Дата обновления',
            'created_at' => 'Дата создания',
        ];
    }

    /**
     * @param $dayNumber
     * @return string|string[]
     */
    public static function workdayAttributes($dayNumber = false)
    {
        $daysAttribute = [
            1 => 'monday',
            2 => 'tuesday',
            3 => 'wednesday',
            4 => 'thursday',
            5 => 'friday',
            6 => 'saturday',
            7 => 'sunday',
        ];
        if ($dayNumber) {
            return $daysAttribute[$dayNumber] ?? '';
        }
        return $daysAttribute;
    }

    /**
     * @param $dayNumber
     * @return string|string[]
     */
    public static function workdayLabels($dayNumber = false)
    {
        $daysAttribute = [
            1 => 'Понедельник',
            2 => 'Вторник',
            3 => 'Среда',
            4 => 'Четверг',
            5 => 'Пятница',
            6 => 'Суббота',
            7 => 'Воскресенье',
        ];
        if ($dayNumber) {
            return $daysAttribute[$dayNumber] ?? '';
        }
        return $daysAttribute;
    }

    /**
     * @param $dayOfWeek - 0 - понедельник, ...,  6 - воскресенье. F.: date('w');
     * @return string|null
     */
    public function getStartDayTimeByDay($dayOfWeek): ?string
    {
        switch ($dayOfWeek) {
            case 0:
                $startDayHour = $this->sunday_start;
                break;
            case 1:
                $startDayHour = $this->monday_start;
                break;
            case 2:
                $startDayHour = $this->tuesday_start;
                break;
            case 3:
                $startDayHour = $this->wednesday_start;
                break;
            case 4:
                $startDayHour = $this->thursday_start;
                break;
            case 5:
                $startDayHour = $this->friday_start;
                break;
            case 6:
                $startDayHour = $this->saturday_start;
                break;
        }

        return $startDayHour;
    }

    /**
     * @param $dayOfWeek - 0 - понедельник, ...,  6 - воскресенье. F.: date('w');
     * @return string|null
     */
    public function getEndDayTimeByDay($dayOfWeek): ?string
    {
        switch ($dayOfWeek) {
            case 0:
                $endDayHour = $this->sunday_end;
                break;
            case 1:
                $endDayHour = $this->monday_end;
                break;
            case 2:
                $endDayHour = $this->tuesday_end;
                break;
            case 3:
                $endDayHour = $this->wednesday_end;
                break;
            case 4:
                $endDayHour = $this->thursday_end;
                break;
            case 5:
                $endDayHour = $this->friday_end;
                break;
            case 6:
                $endDayHour = $this->saturday_end;
                break;
        }
        return $endDayHour;
    }

    public function isWorkDay($dayOfWeek)
    {
        switch ($dayOfWeek) {
            case 0:
                $isWork = $this->is_work_sunday;
                break;
            case 1:
                $isWork = $this->is_work_monday;
                break;
            case 2:
                $isWork = $this->is_work_tuesday;
                break;
            case 3:
                $isWork = $this->is_work_wednesday;
                break;
            case 4:
                $isWork = $this->is_work_thursday;
                break;
            case 5:
                $isWork = $this->is_work_friday;
                break;
            case 6:
                $isWork = $this->is_work_saturday;
                break;
        }

        return $isWork;
    }

    /**
     * @param $dayNumber
     * @return bool
     * @throws ErrorCalculateSchedule
     */
    public function checkWorkFullDay($dayNumber): bool
    {
        $attribute = self::workdayAttributes($dayNumber);
        if (empty($attribute)) {
            throw new ErrorCalculateSchedule('Не удалось получить нужный день для проверки круглосуточной работы');
        }
        $startTime = date('H:i', strtotime($this->{$attribute . '_start'}));
        $endTime = date('H:i', strtotime($this->{$attribute . '_end'}));

        if ($startTime == self::MIN_START_TIME && $endTime == self::MAX_END_TIME) {
            return true;
        }
        return false;
    }

    /**
     * Gets query for [[Carwash]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarwash()
    {
        return $this->hasOne(Carwash::className(), ['id' => 'carwash_id']);
    }
}
