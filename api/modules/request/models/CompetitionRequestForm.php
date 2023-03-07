<?php

namespace api\modules\request\models;

use common\models\CompetitionRequest;
use common\modules\config\application\components\AggregateMaker;
use common\modules\config\application\entities\CompetitionRequest as CompetitionRequestConfig;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;

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
        $query = new Query;
        // compose the query
        $query->select('model_id, label')
            ->from('member_item_lang')
            ->where(['label'=>$this->nomination]);
        // build and execute the query
        $nomination = $query->one();
        if(empty($nomination)){
            echo 'Error: Can not find nomination id from database!';
            exit();
        }

        $this->nomination_id = $nomination['model_id'];
        
        return parent::beforeSave($insert);
    }
}
