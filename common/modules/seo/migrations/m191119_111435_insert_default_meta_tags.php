<?php

namespace common\modules\seo\migrations;

use common\modules\seo\models\MetaTags;
use console\components\Migration;

class m191119_111435_insert_default_meta_tags extends Migration
{
    public $table = '{{%meta_tags}}';

    public function safeUp()
    {
        $time = time();
        $this->batchInsert(
            $this->table,
            [
                'id',
                'label',
                'name',
                'type',
                'position',
            ],
            [
                [
                    'id' => 1,
                    'label' => 'Title',
                    'name' => 'title',
                    'type' => MetaTags::TYPE_TEXT,
                    'position' => 1,
                ],
                [
                    'id' => 2,
                    'label' => 'Description',
                    'name' => 'description',
                    'type' => MetaTags::TYPE_TEXTAREA,
                    'position' => 2,
                ],
                [
                    'id' => 3,
                    'label' => 'OpenGraph title',
                    'name' => 'og_title',
                    'type' => MetaTags::TYPE_TEXT,
                    'position' => 3,
                ],
                [
                    'id' => 4,
                    'label' => 'OpenGraph description',
                    'name' => 'og_description',
                    'type' => MetaTags::TYPE_TEXTAREA,
                    'position' => 4,
                ],
                [
                    'id' => 5,
                    'label' => 'OpenGraph image',
                    'name' => 'og_image',
                    'type' => MetaTags::TYPE_IMAGE,
                    'position' => 5,
                ],
                [
                    'id' => 6,
                    'label' => 'Microdata',
                    'name' => 'microdata',
                    'type' => MetaTags::TYPE_CODE,
                    'position' => 6,
                ],
                [
                    'id' => 7,
                    'label' => 'Noindex',
                    'name' => 'noindex',
                    'type' => MetaTags::TYPE_CHECKBOX,
                    'position' => 7,
                ],
                [
                    'id' => 8,
                    'label' => 'Nofollow',
                    'name' => 'nofollow',
                    'type' => MetaTags::TYPE_CHECKBOX,
                    'position' => 8,
                ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete($this->table, ['id' => range(1,8)]);
    }
}
