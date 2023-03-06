<?php

namespace common\modules\faq\migrations;

use console\components\Migration;

class m191112_150411_insert_faq_category extends Migration
{
    public $table = '{{%faq_category}}';
    public $langTable = '{{%faq_category_lang}}';

    public function safeUp()
    {
        $time = time();
        $this->insert($this->table, [
            'id' => 1,
            'alias' => 'common',
            'published' => true,
            'position' => 0,
            'created_at' => $time,
            'updated_at' => $time,
        ]);
        $this->insert($this->langTable, [
            'model_id' => 1,
            'language' => 'uk',
            'label' => 'Загальні питання'
        ]);
    }

    public function safeDown()
    {
        $this->delete($this->table, ['id' => 1]);
    }
}
