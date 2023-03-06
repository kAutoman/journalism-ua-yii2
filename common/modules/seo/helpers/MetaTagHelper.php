<?php

namespace common\modules\seo\helpers;

use common\helpers\UrlHelper;
use Exception;
use metalguardian\fileProcessor\helpers\FPM;
use common\modules\seo\models\MetaTags;
use common\components\model\ActiveRecord;
use common\modules\seo\behaviors\MetaTagsBehavior;

/**
 * Class MetaTagHelper
 *
 * @package api\helpers
 */
class MetaTagHelper
{
    /**
     * @param ActiveRecord $entity
     * @return array|null
     * @throws Exception
     */
    public static function register(ActiveRecord $entity)
    {
        $seo = $entity->getBehavior('seo');
        $response = [];
        $value = '';
        if ($seo && $seo instanceof MetaTagsBehavior && $entity->hasProperty('metaTags')) {
            foreach ($entity->metaTags as $name => $metaTag) {
                $tag = $metaTag->tag;
                $isOg = mb_strpos($name, 'og_') !== false;
                switch ($tag->type) {
                    case MetaTags::TYPE_IMAGE:
                        $domain = configurator()->get('app.front.domain');
                        $img = $metaTag->image ? $domain . FPM::originalSrc($metaTag->image->file_id) : null;
                        if ($isOg) {
                            $response['og'][mb_substr($name, 3)] = $img;
                        } else {
                            $response[$name] = $img;
                        }
                        break;
                    case MetaTags::TYPE_CHECKBOX:
                        if ($name === 'noindex') {
                            $value .= (bool) $metaTag->value ? 'noindex,' : 'index,';
                        }
                        if ($name === 'nofollow') {
                            $value .= (bool) $metaTag->value ? 'nofollow,' : 'follow,';
                        }
                        break;
                    default:
                        if ($isOg) {
                            $response['og'][mb_substr($name, 3)] = $metaTag->value;
                        } else {
                            $response[$name] = $metaTag->value;
                        }
                }
            }
            $response['robots'] = rtrim($value, ',');
            // default, not rendered in admin params
            $response['og']['url'] = UrlHelper::canonical();
            $response['canonical'] = UrlHelper::canonical();
            return $response;
        }

        return null;
    }
}
