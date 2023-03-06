<?php

namespace common\components;

use common\helpers\MediaHelper;
use common\models\FpmFile;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\i18n\Formatter as BaseFormatter;

/**
 * Class Formatter
 *
 * @package api\components
 * @todo relocate to common
 */
class Formatter extends BaseFormatter
{
    /**
     * @param string $label
     * @param string $link
     *
     * @return array
     */
    public function link(string $label, string $link): array
    {
        return ['label' => $label, 'url' => $link];
    }

    /**
     * @param string $label
     *
     * @return array
     */
    public function button(string $label): array
    {
        return ['label' => $label];
    }

    /**
     * @param string $label
     * @param string $actionLink
     *
     * @return array
     */
    public function submitButton(string $label, string $actionLink): array
    {
        return ['label' => $label, 'url' => $actionLink];
    }

    /**
     * @param int $id
     *
     * @return array
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function image(?int $id): ?array
    {
        if (is_null($id)) {
            return null;
        }
        // @todo rewrite to query (will fix second query for relation)
//        $file = (new \yii\db\Query())
//            ->from(FpmFile::tableName())
//            ->where(['id' => $id])
//            ->leftJoin('meta', ['file_id' => 'id', 'language' => app()->language])
//            ->one();
        $file = FpmFile::findOne($id);
        if ($file === null) {
            return null;
        }
        $default = [];
        $webp = [];
        foreach (MediaHelper::getSizesDictionary() as $type => $size) {
            $defaultPath = MediaHelper::THUMBNAIL_PATH . '/' . $id . '-' . $type . '-' . $file->base_name . '.' . $file->extension;
            $webpPath = MediaHelper::THUMBNAIL_PATH . '/' . $id . '-' . $type . '-' . $file->base_name . '.' . 'webp';
            $default[MediaHelper::getBrakePoints()[$type] ?? null] = file_exists(getAlias('@webroot') . $defaultPath)
                ? $defaultPath : null;
            $webp[MediaHelper::getBrakePoints()[$type] ?? null] = file_exists(getAlias('@webroot') . $webpPath)
                ? $webpPath : null;
        }
        $stubPath = MediaHelper::THUMBNAIL_PATH . '/' . $id . '-' . MediaHelper::SIZE_STUB . '-' . $file->base_name . '.' . $file->extension;
        $stub = file_exists(getAlias('@webroot') . $stubPath) ? $stubPath : null;
        if ($file->meta !== null) {
            $src['alt'] = $file->meta->alt;
            $src['title'] = $file->meta->title;
        }
        $src['originalSrc'] = MediaHelper::getOriginalByFile($file);
        $src['thumb'] = $stub;
        $src['default'] = $default;
        $src['webp'] = $webp;

        return $src;
    }

    /**
     * @param $imageIds
     *
     * @return array
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function images($imageIds): array
    {
        $images = [];
        $imageIds = json_decode($imageIds, true);
        if (!is_array($imageIds)) {
            $imageIds = [$imageIds];
        }

        if (!empty($imageIds)) {
            foreach ($imageIds as $imageId) {
                $images[] = $this->image($imageId);
            }
        }

        return $images;
    }

    public function video(?int $id, ?int $posterId = null): ?array
    {
        if (is_null($id)) {
            return null;
        }

        $file = FpmFile::findOne($id);
        if ($file === null) {
            return null;
        }
        $webm = MediaHelper::WEBM_PATH . '/' . MediaHelper::getVideoName($file);
        if ($posterId !== null) {
            $src['poster'] = $this->image($posterId);
        }
        $src['src'] = [
            'mp4' => MediaHelper::getOriginalByFile($file),
            'webm' => file_exists(getAlias('@webroot') . $webm) ? $webm : null,
        ];

        return $src;
    }

    /**
     * @param int $id
     *
     * @return string|null
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function file(?int $id): ?string
    {
        if (is_null($id)) {
            return null;
        }

        return MediaHelper::originalSrc($id);
    }
}
