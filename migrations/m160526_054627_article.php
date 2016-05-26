<?php

use yii\db\Migration;

class m160526_054627_article extends Migration
{
    public function up()
    {
        $this->createTable('article', [
            'id' => $this->primaryKey()->notNull(),
            'title' => $this->string()->notNull(),
            'announce' => $this->text()->notNull(),
            'text' => $this->text()->notNull(),
            'date' => $this->timestamp()->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    public function down()
    {
        $this->dropTable('{{%article}}');
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
