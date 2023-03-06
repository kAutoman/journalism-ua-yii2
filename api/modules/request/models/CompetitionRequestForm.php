<?php

namespace api\modules\request\models;

use common\models\CompetitionRequest;
use common\modules\config\application\components\AggregateMaker;
use common\modules\config\application\entities\CompetitionRequest as CompetitionRequestConfig;
use yii\behaviors\TimestampBehavior;

/**
 * Class SubmitForm
 *
 * @package api\modules\request\models
 */
class CompetitionRequestForm extends CompetitionRequest
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'name',
                    'email',
                    'gender',
                    'age',
                    'city',
                    'position',
                    'company_name',
                    'phone',
                    'experience',
                    'material_label',
                    'material_type',
                    'program_label',
                    'program_published_date',
                    'program_link',
                    'nomination',
                    'argument',
                    'awards'
                ],
                'required',
            ],
            [
                [
                    'name',
                    'email',
                    'gender',
                    'age',
                    'city',
                    'position',
                    'company_name',
                    'phone',
                    'experience',
                    'other_name',
                    'material_type',
                    'program_label',
                    'program_published_date',
                    'program_link',
                    'nomination',
                ],
                'string',
                'max' => 255
            ],
            [
                [
                    'material_label',
                    'argument',
                    'awards'
                ],
                'string',
                'max' => 5000
            ],
            [
                ['gender_id', 'material_type_id', 'nomination_id'],
                'integer'
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

    /**
     * @return array
     */
    public function getSuccessMsg(): array
    {
        /** @var CompetitionRequestConfig $config */
        $config = (new AggregateMaker(CompetitionRequestConfig::class))->make();

        return [
            'label' => $config->formSuccessLabel,
            'text' => $config->formSuccessText,
        ];
    }

    /**
     * @param bool $insert
     *
     * @return bool
     */
    public function beforeSave($insert)
    {
        $list = [
            1 => 'Найкраще інтерв’ю',
            2 => 'Найкращий репортаж',
            3 => 'Найкраще новинне висвітлення резонансної події',
            4 => 'Найкраща аналітика',
            5 => 'Найкраще розслідування',
            6 => 'Найкраща публіцистика',
            7 => 'Спецномінація Конструктивна журналістика',
            8 => 'Спецномінація Пояснювальна журналістика в часи пандемії',
            9 => 'Спецномінація Експерт-знахідка',
            10 => 'Спецномінація Найкращий матеріал на тему дотримання професійних стандартів, журналістської етики та саморегуляції',
            11 => 'Спецномінація Найкращий журналістський експеримент',
        ];

        $key = (int)array_search($this->nomination, $list);

        if ($key) {
            $this->nomination_id = $key;
        }

        return parent::beforeSave($insert);
    }
}
