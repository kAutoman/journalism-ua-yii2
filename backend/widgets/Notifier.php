<?php

namespace backend\widgets;

use yii\base\Widget;

/**
 * Class Notifier - simple widget to transform session based flash messages into pretty animated notifiers.
 *
 * @author Bogdan K. Fedun <delagics@gmail.com>
 */
class Notifier extends Widget
{
    const ALERT_TYPES = ['error', 'danger', 'success', 'info', 'warning'];

    public function init()
    {
        parent::init();
        $flashes = session()->getAllFlashes();
        foreach ($flashes as $type => $data) {
            if (in_array($type, self::ALERT_TYPES)) {
                $data = (array)$data;
                foreach ($data as $message) {
                    $this->registerAlertScript($type, $message);
                }
                session()->removeFlash($type);
            }
        }
    }

    public function registerAlertScript(string $type, string $message)
    {
        app()->getView()->registerJs(<<<JS
            $.notify({icon: 'si si-info', message: '$message'}, {
                type: '$type', 
                allow_dismiss: true, 
                showProgressbar: false,
                mouse_over: 'pause',
                offset: 20,
                spacing: 10,
                z_index: 1000001,
                delay: 3000,
                timer: 1000,
                placement: {from: "bottom", align: "right"},
            });
JS
        );
    }
}
