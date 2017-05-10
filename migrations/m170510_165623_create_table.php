<?php

use yii\db\Migration;

class m170510_165623_create_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('section', [
            'id'   => 'BIGSERIAL NOT NULL PRIMARY KEY',
            'name' => 'VARCHAR(255)',
        ]);

        $this->createTable('promotion', [
            'id'          => 'BIGSERIAL NOT NULL PRIMARY KEY',
            'section_id'  => 'BIGINT',
            'title'       => 'VARCHAR(255)',
            'description' => 'TEXT',
        ]);

        $this->addForeignKey('fk_promotion_section', 'promotion', 'section_id', 'section', 'id', "SET DEFAULT", "SET DEFAULT");
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_promotion_section', 'promotion');

        $this->dropTable('promotion');
        $this->dropTable('section');
    }
}
