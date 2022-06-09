<?php

use yii\db\Migration;

/**
 * Class m220608_073217_create_suppliers
 */
class m220608_073217_create_suppliers extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('suppliers', [
            'id' => $this->primaryKey(),
            'name' => $this->string(50)->notNull()->defaultValue(''),
            'code' => $this->char(3)->unique(),
            't_status' => 'enum(\'ok\',\'hold\') CHARACTER SET ascii COLLATE ascii_general_ci NOT NULL DEFAULT \'ok\''
        ]);
        $faker = \Faker\Factory::create();
        $rows = [];
        while (true) {
            $rows[] = [
                'name' => $faker->name,
                'code' => $faker->unique()->regexify('[A-Z0-9]{3}'),
                't_status' => $faker->randomElement(['ok', 'hold']),
            ];
            if (count($rows) >= 1000) {
                break;
            }
        }
        $this->batchInsert('suppliers', ['name', 'code', 't_status'], $rows);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220608_073217_create_suppliers cannot be reverted.\n";

        return false;
    }
}
