<?php

namespace frontend\modules\article\models;

use common\helpers\MediaHelper;
use common\models\Article as CommonArticle;
use common\modules\builder\models\BuilderModel;
use yii\helpers\Url;

/**
 * Class Article
 * @package api\modules\article\models
 */
class Article extends CommonArticle
{
    public $view = false;

    /**
     * @return array|false
     */
    public function fields()
    {
        return [
            'label',
            'date' => function (Article $model) {
                return formatter()->asDate($model->publication_date, 'php:d.m.Y');
            },
            'preview' => function (Article $model) {
                return formatter()->image($model->preview->file_id ?? null);
            },
            'link',
        ];
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public function getLink(array $params = []): string
    {
        $params[0] = '/article/article/view';
        $params['alias'] = $this->alias;

        return Url::toRoute($params);
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getBlock(): array
    {
        $blocks = [];

        foreach ($this->builderContent as $builderModel) {
            $block = $this->addBlock($builderModel);

            if ($block) {
                $blocks[] = $block;
            }
        }

        return $blocks;
    }

    /**
     * @param BuilderModel $builderModel
     *
     * @return array|null
     * @throws \ReflectionException
     */
    public function addBlock(BuilderModel $builderModel): ?array
    {
        $block = null;

        if ($builderModel->published) {
            $block = [
                'id' => $builderModel->getShortName(),
                'level' => $builderModel->tag_level,
                'attributes' => $builderModel->getApiAttributes()
            ];
        }

        return $block;
    }
}
