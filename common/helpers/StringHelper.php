<?php

namespace common\helpers;

use yii\helpers\Html;
use yii\helpers\StringHelper as BaseStringHelper;

/**
 * Class StringHelper
 *
 * @package common\helpers
 */
class StringHelper extends BaseStringHelper
{
    /**
     * Convert YouTube video to Embed link for correct using in iframes
     * @param string $link to YouTube Video
     * @return string
     */
    public static function parseYoutubeLink($link)
    {
        $video = '';

        if (preg_match("/(www.youtube.com\/watch.v=+)([^=]+)/i", $link, $key)) {
            $video = "https://www.youtube.com/embed/$key[2]";
        } elseif (preg_match("/(youtu.be\/+)([^\/]+)/i", $link, $key)) {
            $video = "https://www.youtube.com/embed/$key[2]";
        } elseif (preg_match("/(www.youtube.com\/embed\/+)([^\/]+)/i", $link, $key)) {
            $video = "https://www.youtube.com/embed/$key[2]";
        }

        return $video;
    }

    /**
     * Change *asterisks* wrap text to html tag.
     *
     * @param string $content
     * @param string $tag
     * @param bool $br
     * @param array $tagOptions
     * @return string Html-formatted string
     */
    public static function getHighlightedContent(string $content, string $tag = 'span', bool $br = false, array $tagOptions = []): string
    {
        $string = preg_replace("/\*(.*?)\*/", Html::tag($tag, "$1", $tagOptions), $content);

        return $br ? nl2br($string) : $string;
    }

    /**
     * @param array $data
     * @param int $limit
     * @return array
     */
    public static function limitBreadcrumbsCharsets(array $data, int $limit = 50): array
    {
        $returnData = [];
        foreach ($data as $item) {
            $itemLabel = $item['label'] ?? $item;
            if (is_array($itemLabel)) {
                return $data;
            }
            if (strlen($itemLabel) < $limit) {
                $returnData[] = $item;
                continue;
            }
            $label = mb_substr($itemLabel, 0, $limit) . '...';
            if (is_array($item)) {
                $item['label'] = $label;
                $returnData[] = $item;
                continue;
            }
            $returnData[] = $label;
        }

        return $returnData;
    }
}
