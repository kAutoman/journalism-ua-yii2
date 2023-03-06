<?php
/**
 * This is the template for generating a controller class file for static page.
 */

use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator \backend\components\gii\staticPageModel\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClassName);

echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

use backend\components\StaticPageController;
use <?= $generator->ns . '\\' . ltrim($generator->modelClassName, '\\') ?>;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends StaticPageController
{
    /**
     * @return string
     */
    public function getModelClass(): string
    {
        return <?= $generator->modelClassName ?>::class;
    }
}
