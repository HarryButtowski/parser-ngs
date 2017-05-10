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

        $this->insert('section', ['name' => 'Дом, стройка, ремонт']);
        $this->insert('section', ['name' => 'Услуги']);
        $this->insert('section', ['name' => 'Мебель']);
        $this->insert('section', ['name' => 'Электроника и бытовая техника']);
        $this->insert('section', ['name' => 'Одежда, обувь']);
        $this->insert('section', ['name' => 'Спорт, туризм, хобби']);
        $this->insert('section', ['name' => 'Детские товары']);
        $this->insert('section', ['name' => 'Деловые предложения']);
        $this->insert('section', ['name' => 'Красота, здоровье']);
        $this->insert('section', ['name' => 'Бюро находок']);
        $this->insert('section', ['name' => 'Требуется помощь']);
        $this->insert('section', ['name' => 'Обучение']);
        $this->insert('section', ['name' => 'Подработка']);
        $this->insert('section', ['name' => 'Средства связи']);
        $this->insert('section', ['name' => 'Праздники, свадьбы']);
        $this->insert('section', ['name' => 'Собаки и щенки']);
        $this->insert('section', ['name' => 'Котята и кошки']);
        $this->insert('section', ['name' => 'Грызуны']);
        $this->insert('section', ['name' => 'Хорьки']);
        $this->insert('section', ['name' => 'Сельскохозяйственные животные']);
        $this->insert('section', ['name' => 'Птицы']);

        $this->createTable('promotion', [
            'id'           => 'BIGSERIAL NOT NULL PRIMARY KEY',
            'section_id'   => 'BIGINT',
            'promotion_id' => 'VARCHAR(255)',
            'title'        => 'VARCHAR(255)',
            'description'  => 'TEXT',
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
