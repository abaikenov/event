<?php

use yii\db\Migration;

class m130526_062349_user extends Migration
{
    public function up()
    {

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey()->notNull(),
            'username' => $this->string()->notNull(),
            'password_hash' => $this->string()->notNull(),
            'email' => $this->string()->notNull(),
            'status' => $this->smallInteger(2)->notNull(),
            'auth_key' => $this->string(),
            'password_reset_token' => $this->string(),
        ]);

        $this->createIndex('id', '{{%user}}', 'id', true);

        $this->batchInsert('{{%user}}', ['username', 'password_hash', 'email', 'status'], [
            ['admin', '$2y$13$cl3Q263yypMYtLKY/H3S5uoUA1llJjOX4KWZHJJ4wiO.TikrEV3OO', 'admin@mail.ru', 10],
            ['user', '$2y$13$UIamz6FSX8fLgSf2V35Hde4DS/h1LVwizPC/eyP4x69s6JZFm7HCO', 'user@mail.ru', 10],
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%user}}');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
