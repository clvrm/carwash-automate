<?php

/**
 * @var CarwashSchedule $schedule
 */

use app\models\ar\carwash\CarwashSchedule;
use yii\helpers\Html;

?>
<div id="carwashClientSchedule" class="card card-custom  gutter-b">
    <div class="card-body">
        <h4 class="text-muted">РЕЖИМ РАБОТЫ</h4>

        <div class="table-responsive mt-4">
            <table class="table price-table table-borderless table-vertical-center">
                <thead>
                <tr class="text-muted font-weight-normal">
                    <th class="py-0 min-w-100px">День недели</th>
                    <th class="py-0 min-w-50px font-weight-normal">Открытие</th>
                    <th class="py-0 min-w-50px font-weight-normal">Закрытие</th>
                    <th class="py-0 min-w-60px font-weight-normal">Круглосуточно</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($schedule::workdayAttributes() as $dayNumber => $workdayAttribute) : ?>
                    <tr class="table-schedule--row-item">
                        <td>
                            <div class="checkbox-inline">
                                <?= Html::activeCheckbox($schedule, 'is_work_' . $workdayAttribute, [
                                    'uncheck' => 0,
                                    'label' => '<span></span>' . $schedule::workdayLabels($dayNumber),
                                    'labelOptions' => ['class' => 'checkbox']
                                ]) ?>
                            </div>
                        </td>
                        <td>
                            <div class="mt-2 mb-2 form-group schedule-time-start">
                                <?= Html::activeInput('text', $schedule, $workdayAttribute . '_start', ['class' => 'text-center form-control',
                                    'placeholder' => '06:00', 'value' => date('H:i', strtotime($schedule->{$workdayAttribute . '_start'})) ?? '']) ?>
                            </div>
                        </td>
                        <td>
                            <div class="mt-2 mb-2 form-group schedule-time-end">
                                <?= Html::activeInput('text', $schedule, $workdayAttribute . '_end', ['class' => 'text-center form-control',
                                    'placeholder' => '23:50', 'value' => date('H:i', strtotime($schedule->{$workdayAttribute . '_end'})) ?? '']) ?>
                            </div>
                        </td>
                        <td>
                        <span class="switch switch-sm">
                            <label>
                                <input type="checkbox" class="workFulldaySwitcher" value="1"
                                       <?php if ($schedule->checkWorkFullDay($dayNumber)): ?> checked="checked" <?php endif; ?>/>
                                <span></span>
                            </label>
                        </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
