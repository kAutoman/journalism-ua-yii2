<?php


namespace common\behaviors;


use ReflectionClass;
use yii\base\Behavior;
use yii\db\ActiveRecord;

class ManyToManyBehavior extends Behavior
{
    public $modelClass;
    public $ownerField;
    public $relatedField;
    public $fieldName;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
        ];
    }

    public function afterSave()
    {
        $nameClass = (new ReflectionClass($this->owner))->getShortName();
        $data = \Yii::$app->request->post()[$nameClass][$this->fieldName] ?? [];
        $oldData = $this->modelClass::findAll([$this->ownerField => $this->owner->id]);

        if (is_array($data)) {
            foreach ($data as $row) {
                $newRow = new $this->modelClass;
                $newRow->{$this->ownerField} = $this->owner->id;
                $newRow->{$this->relatedField} = $row;
                $newRow->save();
            }
        }

        foreach ($oldData as $oldRow) {
            $oldRow->delete();
        }

    }
}
