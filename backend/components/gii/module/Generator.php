<?php

namespace backend\components\gii\module;

use Yii;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\gii\CodeFile;
use yii\gii\generators\module\Generator as BaseGenerator;

/**
 * @inheritdoc
 */
class Generator extends BaseGenerator
{
    /**
     * @var bool
     */
    public $enableI18N = true;

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'Advanced Module Generator';
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $modulePath = $this->getModulePath();

        $files[] = new CodeFile(
            $modulePath . '/' . StringHelper::basename($this->moduleClass) . '.php',
            $this->render("module.php")
        );
        $files[] = new CodeFile(
            $modulePath . '/controllers/.gitkeep',
            ''
        );
        $files[] = new CodeFile(
            $modulePath . '/views/.gitkeep',
            ''
        );
        $files[] = new CodeFile(
            $modulePath . '/models/.gitkeep',
            ''
        );

        return $files;
    }

    /**
     * @inheritdoc
     */
    public function successMessage()
    {
        if (Yii::$app->hasModule($this->moduleID)) {
            $link = Html::a('try it now', Yii::$app->getUrlManager()->createUrl($this->moduleID), ['target' => '_blank']);

            return "The module has been generated successfully. You may $link.";
        }

        $output = <<<EOD
<p>The module has been generated successfully.</p>
<p>To access the module, you need to add this to your application configuration:</p>
EOD;
        $code = <<<EOD
<?php
    ......
    'modules' => [
        '{$this->moduleID}' => {$this->moduleClass}::class,
    ],
    ......
EOD;

        return $output . '<pre>' . highlight_string($code, true) . '</pre>';
    }
}
