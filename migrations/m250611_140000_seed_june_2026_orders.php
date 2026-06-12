<?php

use app\migrations\demo\DemoOrdersBulkSeeder;
use yii\db\Migration;

/**
 * ~1000 заказов за 10–30 июня 2026 (Faker).
 */
class m250611_140000_seed_june_2026_orders extends Migration
{
    public function safeUp()
    {
        if (DemoOrdersBulkSeeder::exists()) {
            echo "    > Заказы за июнь 2026 уже есть, пропуск.\n";

            return true;
        }

        $seeder = new DemoOrdersBulkSeeder();
        $count = $seeder->seed();

        echo "    > Создано заказов: {$count} (" . DemoOrdersBulkSeeder::DATE_FROM
            . ' — ' . DemoOrdersBulkSeeder::DATE_TO . ").\n";

        return true;
    }

    public function safeDown()
    {
        $seeder = new DemoOrdersBulkSeeder();
        $count = $seeder->remove();

        echo "    > Удалено заказов: {$count}.\n";

        return true;
    }
}
