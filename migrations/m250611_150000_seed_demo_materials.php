<?php

use app\migrations\demo\DemoCarwashSeeder;
use yii\db\Migration;

/**
 * Материалы и привязки к услугам/комплексам для демо-автомойки «ЧистоИТочка».
 *
 * Для уже развёрнутых БД, где основной демо-сидер был выполнен ранее.
 */
class m250611_150000_seed_demo_materials extends Migration
{
    public function safeUp()
    {
        if (DemoCarwashSeeder::materialsExist()) {
            echo "    > Материалы демо-автомойки уже существуют, пропуск.\n";

            return true;
        }

        if (!DemoCarwashSeeder::exists()) {
            echo "    > Демо-автомойка не найдена, пропуск.\n";

            return true;
        }

        $seeder = new DemoCarwashSeeder();
        $seeder->seedMaterials();

        echo "    > Материалы демо-автомойки загружены.\n";

        return true;
    }

    public function safeDown()
    {
        $seeder = new DemoCarwashSeeder();
        $seeder->removeMaterials();

        echo "    > Материалы демо-автомойки удалены.\n";

        return true;
    }
}
