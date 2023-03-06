<?php

namespace common\modules\builder\controllers;

use common\components\model\ActiveRecord;
use common\helpers\LanguageHelper;
use common\modules\builder\behaviors\BuilderBehavior;
use Yii;
use Throwable;
use yii\web\Response;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\db\StaleObjectException;
use common\modules\builder\models\Builder;
use common\modules\builder\models\BuilderModel;

/**
 * Class BuilderController
 *
 * @package common\modules\builder\controllers
 */
class BuilderController extends Controller
{
    /**
     * Add new builder block to form
     *
     * @return string
     */
    public function actionAdd()
    {
        $postParams = Yii::$app->getRequest()->post();
        $targetClass = ArrayHelper::getValue($postParams, 'targetClass');
        $targetAttribute = ArrayHelper::getValue($postParams, 'targetAttribute');
        $builderModel = ArrayHelper::getValue($postParams, 'builderModel');
        $key = ArrayHelper::getValue($postParams, 'key');

        /** @var BuilderModel $builder */
        $builder = new $builderModel();
        $builder->target_attribute = $targetAttribute;
        $builder->tag_level = BuilderModel::DEFAULT_TAG_LEVEL;
        $builder->loadDefaultValues();
//        $builder->published = 1;

        /** @var ActiveRecord | BuilderBehavior $modelClass */
        $modelClass = new $targetClass();
        $builder->setIsSortable($modelClass->isSortable);
        $builder->setIsRemovable($modelClass->isRemovable);

        return $this->renderAjax('_block', [
            'builderModel' => $builder,
            'key' => $key,
            'open' => true
        ]);

    }

    /**
     * @param $id
     * @return false|int
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDeleteBuilder($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Builder::findOne($id);
        $res = $model->delete();

        return $res;
    }

    public function actionClone(string $targetClass, string $id, string $attribute, string $lang)
    {
        /** @var Builder[]|BuilderModel[] $blocks */
        $blocks = Builder::find()
            ->andWhere([
                'target_class' => $targetClass,
                'target_id' => $id,
                'language' => LanguageHelper::getDefaultLanguage()->code,
                'target_attribute' => $attribute
            ])
            ->orderBy('position')
            ->indexBy('position')
            ->all();

        foreach ($blocks as $position => $block) {
            $newBlock = clone $block;
            $newBlock->setIsNewRecord(true);
            $newBlock->id = null; //reset ID
            $newBlock->language = $lang; // set target lang
            $newBlock->target_sign = security()->generateRandomString();
            $newBlock->save();
            // clone attributes
            foreach ($block->builderAttributes as $builderAttribute) {
                $attribute = clone $builderAttribute;
                $attribute->setIsNewRecord(true);
                $attribute->id = null;
                $attribute->builder_id = $newBlock->id;

                /** @var BuilderModel $builderModel */
                $builderModel = new $block->builder_model_class;


                if (in_array($attribute->attribute, $builderModel->getUploadAttributes())) {
                    $attribute->value = null;
                }
                $attribute->save();
            }
        }

        return $this->redirect(request()->getReferrer());
    }
}
