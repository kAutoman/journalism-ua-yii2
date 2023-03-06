<?php

namespace console\migrations;

use common\models\Page;
use console\components\Migration;

class m191105_190330_insert_home_page extends Migration
{
    public $table = '{{%page}}';
    public $langTable = '{{%page_lang}}';

    public function safeUp()
    {
        $time = time();

        $this->insert($this->table, [
            'id' => 1,
            'type' => Page::TYPE_DYNAMIC_BUILDER,
            'root' => 1,
            'lft' => 1,
            'rgt' => 6,
            'depth' => 0,
            'entity_id' => 'HomePage',
            'alias' => 'home',
            'published' => 1,
            'removable' => 0,
            'child_allowed' => 1,
            'lock' => 0,
            'deleted' => 0,
            'created_at' => $time,
            'updated_at' => $time,
            'deleted_at' => null,
        ]);
        $this->insert($this->langTable, [
            'model_id' => 1,
            'language' => 'uk',
            'label' => 'Головна сторінка',
            'content' => null
        ]);


        $this->insert($this->table, [
            'id' => 2,
            'type' => Page::TYPE_BASIC,
            'root' => 1,
            'lft' => 2,
            'rgt' => 3,
            'depth' => 1,
            'entity_id' => 'PrivacyPolicyPage',
            'alias' => 'privacy',
            'published' => 1,
            'removable' => 0,
            'child_allowed' => 0,
            'lock' => 0,
            'deleted' => 0,
            'created_at' => $time,
            'updated_at' => $time,
            'deleted_at' => null,
        ]);
        $this->insert($this->langTable, [
            'model_id' => 2,
            'language' => 'uk',
            'label' => 'Політика конфіденційності',
            'content' => null
        ]);

        $this->insert($this->table, [
            'id' => 3,
            'type' => Page::TYPE_BASIC,
            'root' => 1,
            'lft' => 4,
            'rgt' => 5,
            'depth' => 1,
            'entity_id' => 'ErrorPage',
            'alias' => '404',
            'published' => 1,
            'removable' => 0,
            'child_allowed' => 0,
            'lock' => 0,
            'deleted' => 0,
            'created_at' => $time,
            'updated_at' => $time,
            'deleted_at' => null,
        ]);
        $this->insert($this->langTable, [
            'model_id' => 3,
            'language' => 'uk',
            'label' => 'Error page',
            'content' => null
        ]);
    }

    public function safeDown()
    {
        $this->delete($this->table);
    }
}
