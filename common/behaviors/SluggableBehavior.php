<?php

namespace common\behaviors;

use yii\behaviors\SluggableBehavior as BaseSluggableBehavior;

/**
 * Class SluggableBehavior
 *
 * @package common\behaviors
 */
class SluggableBehavior extends BaseSluggableBehavior
{
    /**
     * @var string
     */
    public $attribute = 'label';
    /**
     * @var string
     */
    public $slugAttribute = 'alias';
    /**
     * @var bool
     */
    public $immutable = true;
    /**
     * @var bool
     */
    public $ensureUnique = true;
}
