<?php
/**
 * This is the template for generating a CRUD controller class file.
 */

use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator  backend\components\gii\migration\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use <?= $generator->ns . '\\' . ltrim($generator->modelClass, '\\') ?>;
<?php if ($generator->enableAjaxValidation) : ?>
use yii\helpers\ArrayHelper;
use backend\actions\ActionCreate;
use backend\actions\ActionUpdate;

<?php endif; ?>
/**
 * Class <?= $controllerClass . "\n"; ?>
 *
 * @package <?= $generator->ns . "\n"; ?>
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return <?= StringHelper::basename($generator->modelClass) ?>::class;
    }
<?php if ($generator->enableAjaxValidation) : ?>

    /**
     * @return array
     * @inheritdoc
     */
    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'update' => [
                'class' => ActionUpdate::class,
                'modelClass' => $this->getModelClass(),
                'enableAjaxValidation' => true,
            ],
            'create' => [
                'class' => ActionCreate::class,
                'modelClass' => $this->getModelClass(),
                'enableAjaxValidation' => true
            ]
        ]);
    }
<?php endif; ?>
}
