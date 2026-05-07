<?php
class Controller {
    private static $publicSettingsCache = null;

    public function model($model) {
        require_once '../app/models/' . $model . '.php';
        return new $model();
    }

    protected function getPublicSettings() {
        if (self::$publicSettingsCache !== null) {
            return self::$publicSettingsCache;
        }

        try {
            $settingModel = $this->model('Setting');
            self::$publicSettingsCache = $settingModel->getPublicSettings();
        } catch (Throwable $error) {
            self::$publicSettingsCache = [];
        }

        return self::$publicSettingsCache;
    }

    public function view($view, $data = []) {
        if (!isset($data['public_settings']) && strpos($view, 'client/') === 0) {
            $data['public_settings'] = $this->getPublicSettings();
        }

        if (file_exists('../app/views/' . $view . '.php')) {
            require_once '../app/views/' . $view . '.php';
        } else {
            die('View does not exist');
        }
    }
}
