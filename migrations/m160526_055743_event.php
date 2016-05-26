<?php

use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\rbac\DbManager;

class m160526_055743_event extends Migration
{
    public function up()
    {
        $this->createTable('{{%notification}}', [
            'id' => $this->primaryKey()->notNull(),
            'from' => $this->integer()->notNull(),
            'to' => $this->integer()->notNull(),
            'title' => $this->string()->notNull(),
            'text' => $this->text(),
            'date' => $this->timestamp()->notNull(),
            'view' => $this->smallInteger(1)->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
        $this->createIndex('from', '{{%notification}}', 'from', false);
        $this->addForeignKey("notification_from_fk", "{{%notification}}", "from", "{{%user}}", "id", 'CASCADE');
        $this->createIndex('to', '{{%notification}}', 'to', false);
        $this->addForeignKey('notification_to_fk', '{{%notification}}', 'to', '{{%user}}', 'id', 'CASCADE');

        $this->createTable('{{%trigger}}', [
            'id' => $this->primaryKey()->notNull(),
            'title' => $this->string()->notNull(),
            'type' => $this->string()->notNull(),
            'model' => $this->string()->notNull(),
            'attribute' => $this->string()->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->createTable('{{%event}}', [
            'id' => $this->primaryKey()->notNull(),
            'name' => $this->string(50)->notNull(),
            'from' => $this->integer()->notNull(),
            'to' => $this->integer()->notNull(),
            'title' => $this->string()->notNull(),
            'text' => $this->text(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->createIndex('from', '{{%event}}', 'from', false);
        $this->addForeignKey('event_from_fk', '{{%event}}', 'from', '{{%user}}', 'id', 'CASCADE');

        $this->createTable('{{%type}}', [
            'id' => $this->primaryKey()->notNull(),
            'name' => $this->string(50)->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->createTable('{{%lnk_event_to_trigger}}', [
            'trigger_id' => $this->integer()->notNull(),
            'event_id' => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
        $this->addPrimaryKey('trigger_event_pk', '{{%lnk_event_to_trigger}}', ['trigger_id', 'event_id']);
        $this->createIndex('trigger_id', '{{%lnk_event_to_trigger}}', 'trigger_id', false);
        $this->createIndex('event_id', '{{%lnk_event_to_trigger}}', 'event_id', false);
        $this->addForeignKey('lnk_event_to_trigger_trigger_id_fk', '{{%lnk_event_to_trigger}}', 'trigger_id', '{{%trigger}}', 'id', 'CASCADE');
        $this->addForeignKey('lnk_event_to_trigger_event_id_fk', '{{%lnk_event_to_trigger}}', 'event_id', '{{%event}}', 'id', 'CASCADE');

        $this->createTable('{{%lnk_event_to_type}}', [
            'event_id' => $this->integer()->notNull(),
            'type_id' => $this->integer()->notNull(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
        $this->addPrimaryKey('event_type_pk', '{{%lnk_event_to_type}}', ['event_id', 'type_id']);
        $this->createIndex('event_id', '{{%lnk_event_to_type}}', 'event_id', false);
        $this->createIndex('type_id', '{{%lnk_event_to_type}}', 'type_id', false);
        $this->addForeignKey('lnk_event_to_type_type_id_fk', '{{%lnk_event_to_type}}', 'type_id', '{{%type}}', 'id', 'CASCADE');
        $this->addForeignKey('lnk_event_to_type_event_type_id_fk', '{{%lnk_event_to_type}}', 'event_id', '{{%event}}', 'id', 'CASCADE');

        $this->batchInsert('{{%trigger}}', ['title', 'type', 'model', 'attribute'], [
            ['Триггер на создание статьи', 'afterInsert', 'app\models\Article', ''],
            ['Триггер на регистрацию пользователя', 'afterInsert', 'app\models\User', ''],
            ['Триггер на смену статуса у пользователя', 'afterUpdate', 'app\models\User', 'status'],
        ]);

        $this->batchInsert('{{%event}}', ['name', 'from', 'to', 'title', 'text'], [
            ['Уведомление о создание статьи', 1, 0, 'Уважаемый {recipient}. На сайте добавлена новая статья', '<p>Уважаемый {recipient}. На сайте добавлена новая статья "{title}".</p>{announce}  {hyperlink()}'],
            ['Уведомление о регистрации', 1, 2, 'Уважаемый {recipient}, Вы успешно зарегистрированы', 'Уважаемый {recipient}, Вы успешно зарегистрированы на сайте {sitename}.'],
            ['Уведомление о сменен статуса', 1, 2, 'Уважаемый {recipient}. Ваш статус изменен', 'Уважаемый {recipient}. Ваш статус изменен на сайте {sitename} на {statusNames}'],
        ]);

        $this->batchInsert('{{%type}}', ['name'], [
            ['email'],
            ['browser'],
        ]);

        $this->batchInsert('{{%lnk_event_to_trigger}}', ['trigger_id', 'event_id'], [
            [1, 1],
            [2, 2],
            [3, 3],
        ]);

        $this->batchInsert('{{%lnk_event_to_type}}', ['event_id', 'type_id'], [
            [1, 1],
            [1, 2],
            [2, 1],
            [2, 2],
            [3, 1],
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%notification}}');
        $this->dropTable('{{%trigger}}');
        $this->dropTable('{{%event}}');
        $this->dropTable('{{%type}}');
        $this->dropTable('{{%lnk_event_to_trigger}}');
        $this->dropTable('{{%lnk_event_to_type}}');
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
