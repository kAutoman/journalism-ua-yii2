<?php

namespace backend\modules\seo\models;

use backend\components\BackendModel;
use common\modules\seo\models\Robots as CommonRobots;
use metalguardian\formBuilder\ActiveFormBuilder;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * Class Robots
 */
class Robots extends CommonRobots implements BackendModel
{

    public function rules()
    {
        return [
            [['text'], 'required'],
            [['text'], 'string', 'max' => 65535]
        ];
    }

    /**
     * Get title for the template page
     *
     * @return string
     */
    public function getTitle()
    {
        return 'robots.txt';
    }

    /**
     * Get attribute columns for index and view page
     *
     * @param $page
     *
     * @return array
     */
    public function getColumns($page)
    {
        return [];
    }

    /**
     * @return ActiveRecord|null
     */
    public function getSearchModel()
    {
        return null;
    }

    /**
     * @return array
     */
    public function getFormConfig()
    {
        return [
            bt('Main') => [
                'text' => [
                    'type' => ActiveFormBuilder::INPUT_TEXTAREA,
                    'options' => [
                        'rows' => 6,
                    ],
                    'hint' => "\n\nSitemap: " . configurator()->get('app.front.domain') . "/sitemap.xml",
                ],
            ],
        ];
    }
}
