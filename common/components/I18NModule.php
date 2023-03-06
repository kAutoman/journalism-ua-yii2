<?php
/**
 * Created by PhpStorm.
 * User: viktor
 * Date: 29.11.18
 * Time: 14:30
 */

namespace common\components;


use vintage\i18n\models\SourceMessage;
use vintage\i18n\Module;
use Yii;
use yii\i18n\MissingTranslationEvent;

class I18NModule extends Module
{
    /**
     * @param MissingTranslationEvent $event
     */
    public static function missingTranslation(MissingTranslationEvent $event)
    {

        $i18n = Yii::$app->getI18n();
        if (isset($i18n->excludedCategories)) {
            $excludeCategories = $i18n->excludedCategories;
        } else {
            $excludeCategories = [];
        }
        $driver = Yii::$app->getDb()->getDriverName();
        $caseInsensitivePrefix = $driver === 'mysql' ? 'binary' : '';

        if (!in_array($event->category, $excludeCategories)) {
            $sourceMessage = Yii::$app->cacheLang->get($event->category.$event->message);

            if(!$sourceMessage){

                $sourceMessage = SourceMessage::find()
                    ->where('category = :category and message = ' . $caseInsensitivePrefix . ' :message', [
                        ':category' => $event->category,
                        ':message' => $event->message
                    ])
                    ->with('messages')
                    ->one();
                Yii::$app->cacheLang->set($event->category.$event->message, $sourceMessage, 60*60*24);
            }

            if (!$sourceMessage) {
                $sourceMessage = new SourceMessage;
                $sourceMessage->setAttributes([
                    'category' => $event->category,
                    'message' => $event->message
                ], false);
                $sourceMessage->save(false);
            }
            $sourceMessage->initMessages();
            //$sourceMessage->saveMessages();

        }
    }
}
