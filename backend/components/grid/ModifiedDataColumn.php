<?php

namespace backend\components\grid;


use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\grid\DataColumn;
use yii\helpers\Html;

class ModifiedDataColumn extends DataColumn
{
    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        /** @var $model ActiveRecord */
        if ($this->format == 'boolean') {
            return Html::activeCheckbox($model, $this->attribute, [
                'label' => false,
                'class' => 'ajax-checkbox',
                'data' => [
                    'id' => $key,
                    'modelName' => $model->className(),
                    'attribute' => $this->attribute,
                ]
            ]);
        }
        if ($this->content === null) {
            return $this->grid->formatter->format($this->getDataCellValue($model, $key, $index), $this->format);
        } else {
            return parent::renderDataCellContent($model, $key, $index);
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderFilterCellContent()
    {
        if ($this->format == 'boolean') {
            $this->filter = [
                Yii::t('back/app', 'No'),
                Yii::t('back/app', 'Yes'),
            ];
        }

        if (is_string($this->filter)) {
            return $this->filter;
        }

        $model = $this->grid->filterModel;

        if ($this->filter !== false && $model instanceof Model && $this->attribute !== null && $model->isAttributeActive($this->attribute)) {
            if ($model->hasErrors($this->attribute)) {
                Html::addCssClass($this->filterOptions, 'has-error');
                $error = ' ' . Html::error($model, $this->attribute, $this->grid->filterErrorOptions);
            } else {
                $error = '';
            }
            if (is_array($this->filter)) {
                $options = array_merge(['prompt' => ''], $this->filterInputOptions);
                return Html::activeDropDownList($model, $this->attribute, $this->filter, $options) . $error;
            } else {
                return Html::activeTextInput($model, $this->attribute, $this->filterInputOptions) . $error;
            }
        } else {
            return parent::renderFilterCellContent();
        }
    }
}
