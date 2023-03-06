<?php
/**
 * This is the template for generating CRUD search class of the specified model.
 */

use yii\helpers\StringHelper;


/* @var $this yii\web\View */
/* @var $generator \backend\components\gii\crud\Generator */
/* @var $rules */
/* @var $searchConditions */
/* @var array $translationTypes */
/* @var bool $hasLangTable */

$modelClass = StringHelper::basename($generator->modelClass);
$searchClass = $modelClass . 'Search';

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use yii\base\Model;
use yii\data\ActiveDataProvider;
<?php if ($hasLangTable) : ?>
use common\models\lang\<?= $modelClass ?>Lang;
use common\behaviors\TranslatedBehavior;
<?php endif; ?>

/**
 * <?= $searchClass ?> represents the model behind the search form about `<?= $generator->modelClass ?>`.
<?php if ($hasLangTable) : ?>
 *
<?php foreach ($translationTypes as $attribute => $type) : ?>
 * @property <?= "{$type} \${$attribute}\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $searchClass ?> extends <?= $modelClass ?>

{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            <?= implode(",\n            ", $rules) ?>,
        ];
    }
<?php if ($hasLangTable) : ?>

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // clear all behaviours except translations
        return [
            'translated' => [
                'class' => TranslatedBehavior::class,
                'translateAttributes' => $this->getLangAttributes()
            ],
        ];
    }
<?php endif; ?>

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function getDataProvider($params)
    {
        $query = <?= $searchClass ?>::find()<?= $hasLangTable ? "->with(['hasTranslate'])" : null ?>;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]]
        ]);

        $this->load($params);

<?php if ($hasLangTable) : ?>
        $query->joinWith(['currentTranslate']);
<?php endif; ?>

        if (!$this->validate()) {
            return $dataProvider;
        }
<?php if ($hasLangTable) : ?>

        $langTable = <?= $modelClass ?>Lang::tableName();

<?php endif; ?>
        <?= implode("\n        ", $searchConditions) ?>

        return $dataProvider;
    }
}
