<?php

namespace common\validators;

use yii\validators\EmailValidator;

/**
 * Class EmailsInStringValidator is a validator for new line separated emails, basically from textarea.
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class EmailsInStringValidator extends EmailValidator
{
    public function validateAttribute($model, $attribute)
    {
        $emails = $this->splitEmails($model->$attribute);
        if (!empty($emails)) {
            foreach ($emails as $email) {
                $email = trim($email);
                $result = $this->validateValue($email);
                if (!empty($result)) {
                    $this->addError($model, $attribute, $email . ': ' . $result[0], $result[1]);
                }
            }
        }
    }

    private function splitEmails(string $emails)
    {
        return explode("\n", trim($emails));
    }

    /**
     * This validator do not cover client validation.
     *
     * @param $model
     * @param $attribute
     * @param $view
     * @return null|string|void
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        return;
    }
}
