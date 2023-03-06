<?php
namespace common\modules\dynamicForm\widgets;

use backend\components\FormBuilder;
use yii\base\InvalidCallException;
use yii\widgets\ActiveField;

/**
 * Class DummyFormBuilder
 *
 * @package backend\modules\dynamicForm\widgets
 */
class DummyFormBuilder extends FormBuilder
{
    /**
     * @var ActiveField[] the ActiveField objects that are currently active
     */
    private $_fields = [];

    /**
     * Runs the widget.
     * This registers the necessary JavaScript code and renders the form close tag.
     *
     * @throws InvalidCallException if `beginField()` and `endField()` calls are not matching.
     */
    public function run()
    {
        if (!empty($this->_fields)) {
            throw new InvalidCallException('Each beginField() should have a matching endField() call.');
        }

        $content = ob_get_clean();
        $this->registerClientScript();

        echo $content;
    }
}
