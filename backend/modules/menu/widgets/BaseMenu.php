<?php
namespace backend\modules\menu\widgets;

use Yii;
use kartik\nav\NavX;

/**
 * Base menu widget provides internationalization
 *
 * @author Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 */
class BaseMenu extends NavX
{
    /**
     * Category name for translations
     * @var string
     */
    public $translationCategory = 'back/menu';


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->items = $this->translateLabels($this->items);
    }

    /**
     * Translate all menu labels
     *
     * @param array $items Array with menu items
     * @return array mixed
     */
    private function translateLabels($items)
    {
        foreach ($items as $key => $item) {
            if (isset($item['label'])) {
                $params = isset($item['params']) ? $item['params'] : [];
                $items[$key]['label'] = $this->translate($item['label'], $params);
            }
            if (isset($item['items'])) {
                $items[$key]['items'] = $this->translateLabels($item['items']);
            }
        }

        return $items;
    }

    /**
     * Translate label
     *
     * @param string $label Label to translate
     * @param array $params Params for translation
     * @return string
     */
    private function translate($label, $params = [])
    {
        return Yii::t($this->translationCategory, $label, $params);
    }
}
