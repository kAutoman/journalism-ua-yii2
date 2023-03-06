<?php

namespace common\modules\seo\migrations;

use console\components\Migration;

/**
 * Class m191119_111536_create_meta_tags_content_table migration
 */
class m191119_111536_create_meta_tags_content_table extends Migration
{
    /**
     * migration table name
     */
    public $tableName = '{{%meta_tags_content}}';

    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable(
            $this->tableName,
            [
                'id' => $this->primaryKey(),
                'entity_class' => $this->string()->notNull()->comment('Class'),
                'entity_id' => $this->integer()->notNull()->comment('Entity id'),
                'language' => $this->string()->notNull()->comment('Language'),
                'tag_name' => $this->string()->notNull()->comment('Tag name'),
                'value' => $this->text()->null()->comment('Value'),
            ],
            $this->tableOptions
        );
        $this->createIndex('idx-meta_tags_content-entity_class', 'meta_tags_content', 'entity_class', false);
        $this->createIndex('idx-meta_tags_content-entity_id', 'meta_tags_content', 'entity_id', false);
        $this->createIndex('idx-meta_tags_content-language', 'meta_tags_content', 'language', false);
        $this->createIndex('idx-meta_tags_content-tag_name', 'meta_tags_content', 'tag_name', false);
        $this->addForeignKey(
            'fk-meta_tags_content-tag_name-meta_tags-name',
            $this->tableName,
            'tag_name',
            '{{%meta_tags}}',
            'name',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-meta_tags_content-tag_id-meta_tags-id', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
