<?php

namespace common\models\query;

use common\components\model\DefaultQuery;
use paulzi\nestedsets\NestedSetsQueryTrait;

/**
 * Class PageQuery
 *
 * @package common\models\query
 */
class PageQuery extends DefaultQuery
{
    use NestedSetsQueryTrait;
}
