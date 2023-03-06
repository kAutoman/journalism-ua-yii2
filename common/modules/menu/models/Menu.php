<?php

namespace common\modules\menu\models;

use common\helpers\UrlHelper;
use common\models\Page;
use yii\behaviors\TimestampBehavior;
use lav45\translate\TranslatedTrait;
use common\interfaces\Translatable;
use common\behaviors\TranslatedBehavior;
use common\components\model\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%menu}}".
 *
 * @property integer $id
 * @property integer $location
 * @property integer $page_id
 * @property integer $module
 * @property string $link
 * @property integer $published
 * @property integer $position
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Page $page
 */
class Menu extends ActiveRecord implements Translatable
{
    use TranslatedTrait;

    const LOCATION_HEADER = 0;
    const LOCATION_FOOTER = 1;

    public $langModelClass = MenuLang::class;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
            ],
            'translated' => [
                'class' => TranslatedBehavior::class,
                'translateAttributes' => $this->getLangAttributes()
            ],
        ];
    }

    /**
     * List of all translatable attributes from
     *
     * @return array
     */
    public function getLangAttributes(): array
    {
        return ['label'];
    }

    /**
     * @return array
     */
    public static function getLocationsList(): array
    {
        return [
            self::LOCATION_HEADER => bt('Header', 'menu-module'),
            self::LOCATION_FOOTER => bt('Footer', 'menu-module'),
        ];
    }

    /**
     * @return string|null
     */
    public function getLocation(): ?string
    {
        return self::getLocationsList()[$this->location] ?? null;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPage()
    {
        return $this->hasOne(Page::class, ['id' => 'page_id']);
    }

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        if ($this->link) {
            return $this->link;
        }

        if ($this->page) {
            $clearLink = trim($this->page->alias, '/');
            return "/{$clearLink}";
        }

        return null;
    }
}
