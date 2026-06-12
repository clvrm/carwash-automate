<?php

use app\migrations\demo\DemoCarwashSeeder;
use yii\db\Migration;

/**
 * Демо-данные для автомойки «ЧистоИТочка».
 *
 * Перед запуском: php yii utility/init-rbac
 */
class m250611_120000_seed_demo_carwash extends Migration
{
    public function safeUp()
    {
        if (DemoCarwashSeeder::exists()) {
            echo "    > Демо-автомойка «ЧистоИТочка» уже существует, пропуск.\n";

            return true;
        }

        $seeder = new DemoCarwashSeeder();
        $seeder->seed();

        echo "    > Демо-данные загружены.\n";

        return true;
    }

    public function safeDown()
    {
        $seeder = new DemoCarwashSeeder();
        $seeder->remove();

        echo "    > Демо-данные удалены.\n";

        return true;
    }
}
