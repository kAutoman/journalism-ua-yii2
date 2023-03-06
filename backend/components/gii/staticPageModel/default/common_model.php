<?php
/**
 * This is the template for generating the model class of a specified table.
 */

use common\models\Configuration;

/* @var $this yii\web\View */
/* @var $generator backend\components\gii\staticPageModel\Generator */
/* @var $hasFiles bool */

echo "<?php\n";
$modelClassName = $generator->modelClassName;
?>

namespace common\models;

use Yii;
use common\components\model\StaticPage;

/**
 * Class <?= $modelClassName . "\n"; ?>
 *
<?php foreach ($generator->keys as $key) : ?>
 * @property <?= $generator->getPropertyType($key['type']); ?> $<?= $generator->camelCase($key['id']) . "\n"; ?>
<?php endforeach; ?>
 *
 * @package common\models
 */
class <?= $modelClassName ?> extends StaticPage
{
<?php foreach ($generator->keys as $key): ?>
    const <?= $generator->formatToConstant($key['id']) ?> = '<?= $generator->generateKeyName($key['id']) ?>';
<?php endforeach; ?>
<?php foreach ($generator->keys as $key): ?>

    /**
     * @var <?= $generator->getPropertyType($key['type']) . "\n"; ?>
     */
    public $<?= $generator->camelCase($key['id']) ?>;
<?php endforeach; ?>

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%configuration}}';
    }

    /**
     * @return array
     */
    public function getKeys()
    {
        return [
<?php foreach ($generator->keys as $key): ?>
            self::<?= $generator->formatToConstant($key['id']) ?>,
<?php endforeach; ?>
        ];
    }

    /**
     * @return array
     */
    public function getKeyTypes(): array
    {
        return [
<?php foreach ($generator->keys as $key) : ?>
            self::<?= $generator->formatToConstant($key['id']); ?> => Configuration::<?= $generator->getConstantName($key['type']); ?>,
<?php endforeach; ?>
        ];
    }

    /**
     * @return array
     */
    public function getAttributeKeys(): array
    {
        return [
<?php foreach ($generator->keys as $key) : ?>
            '<?= $generator->camelCase($key['id']); ?>' => self::<?= $generator->formatToConstant($key['id']); ?>,
<?php endforeach; ?>
        ];
    }

    /**
     * @return $this
<?php if ($hasFiles) : ?>
     * @throws \ReflectionException
<?php endif; ?>
     */
    public function get()
    {
        $config = Yii::$app->config;
<?php foreach ($generator->keys as $key) : ?>
<?php if ((int) $key['type'] === Configuration::TYPE_FILE) : ?>
        $this-><?= $generator->camelCase($key['id']) ?> = $this->setFile(self::<?= $generator->formatToConstant($key['id']) ?>);
<?php else : ?>
        $this-><?= $generator->camelCase($key['id']) ?> = $config->get(self::<?= $generator->formatToConstant($key['id']) ?>);
<?php endif; ?>
<?php endforeach; ?>

        return $this;
    }
}
