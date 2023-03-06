<?php

use yii\helpers\StringHelper;

/**
 * @var $this yii\web\View
 * @var $generator \backend\components\gii\builder\Generator
 * @var string $className
 * @var array $attributes
 * @var array $labels
 * @var array $rules
 * @var array $fileAttributes
 * @var string $title
 * @var array $attributeTypes
 */

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;
use <?= $generator->baseClass . ";\n" ?>
use backend\components\FormBuilder;
use backend\widgets\Editor;
use backend\modules\imagesUpload\widgets\imagesUpload\ImageUpload;

/**
 * Class <?= $generator->className . "\n"?>
 *
<?php foreach ($attributes as $name => $type) : ?>
 * @property <?= $type; ?> $<?= $name . "\n" ?>
<?php endforeach; ?>
 *
 * @package <?= $generator->ns . "\n"?>
 */
class <?= $className ?> extends <?= StringHelper::basename($generator->baseClass) . "\n"?>
{
<?php if (!empty($fileAttributes)) : ?>
<?php foreach ($fileAttributes as $attribute => $constant) : ?>
    const <?= $constant; ?> = '<?= mb_strtolower($constant); ?>';
<?php endforeach; ?>
<?php endif; ?>

<?php foreach ($attributes as $name => $type) : ?>
    /**
     * @var <?= $type . "\n"; ?>
     */
    public $<?= $name ?>;

<?php endforeach; ?>
    /**
     * List of ALL attributes corresponding to $this model
     *
     * @return array
     */
    public function getBuilderAttributes(): array
    {
        return [
<?php foreach ($attributes as $name => $type) : ?>
            '<?= $name; ?>',
<?php endforeach; ?>
        ];
    }

<?php if (!empty($fileAttributes)) : ?>
    /**
     * List of all file attributes. MUST have the following syntax:
     * ```
     * return [
     *      ...,
     *      `attributeName` => self::ATTRIBUTE_FILE_CONSTANT,
     *      `image` => self::SAVE_ATTRIBUTE_IMAGE,
     *      ...
     * ];
     * ```
     *
     * @return array
     */
    public function getUploadAttributes(): array
    {

        return [
<?php foreach ($fileAttributes as $attribute => $constant) : ?>
            '<?= $attribute; ?>' => self::<?= $constant; ?>,
<?php endforeach; ?>
        ];

    }
<?php endif; ?>

    /**
     * Returns the validation rules for attributes.
     * The same as default {{rules()}} method.
     *
     * @return array
     */
    public function validationRules(): array
    {
        return [
            <?= implode(",\n            ", $rules) . "\n"; ?>
        ];
    }

    /**
     * Title for current builder block
     *
     * @return string
     */
    public static function getTitle(): string
    {
        return <?= $title; ?>;
    }

    /**
     * Array of properties labels
     *
     * @return array
     * @see `attributeLabels()` in {ActiveRecord}
     */
    public function getAttributeLabels(): array
    {
        return [
<?php foreach ($labels as $attribute => $label) : ?>
            '<?= $attribute; ?>' => <?= $label; ?>,
<?php endforeach; ?>
        ];
    }

    /**
     * Form data config. Passes to {FormBuilder} to generate form fields
     *
     * @return array
     * @see \common\modules\builder\widgets\DummyFormBuilder
     * @throws \Exception
     */
    public function getFormConfig(): array
    {
        return [
<?php foreach ($attributeTypes as $name => $type) : ?>
            '<?= $name; ?>' => <?= $generator->getFormConfigType($type, $name); ?>,
<?php endforeach; ?>
<?php if (!empty($fileAttributes)) : ?>
            'target_sign' => [
                'type' => FormBuilder::INPUT_HIDDEN,
                'label' => false
            ]
<?php endif; ?>
        ];
    }

    /**
     * Block attributes for API response.
     *
     * @return array
     */
    public function getApiAttributes(): array
    {
        return  [];
    }
}
