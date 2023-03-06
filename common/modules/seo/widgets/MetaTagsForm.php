<?php

namespace common\modules\seo\widgets;

use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;
use common\components\model\ActiveRecord;
use common\modules\seo\behaviors\MetaTagsBehavior;
use common\modules\seo\models\MetaTags;
use common\modules\seo\models\MetaTagsContent;
use yii\base\Widget;
use yii\widgets\ActiveForm;

/**
 * Class MetaTagsForm
 *
 * @package common\modules\seo\widgets
 */
class MetaTagsForm extends Widget
{
    /**
     * @var ActiveForm
     */
    public $form;
    /**
     * @var ActiveRecord|MetaTagsBehavior
     */
    public $model;

    /**
     * @return null|string
     */
    public function run()
    {
        /**
         * @var MetaTagsBehavior $behavior
         */
        $behavior = $this->model->getBehavior('seo');
        if (!$behavior || !$this->form || !$this->model->metaTags) {
            return null;
        }

        return $this->render('form', [
            'form' => $this->form,
            'metaTags' => $this->model->metaTags,
            'behavior' => $behavior
        ]);
    }
}
