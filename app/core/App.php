<?php
class App {
    protected $currentController = 'Pages';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();
        // @file_put_contents('../public/routing.log', date('H:i:s') . ' URL: ' . implode('/', $url) . "\n", FILE_APPEND);

        // Special routing: map /admin/<name> or single-segment admin<name> to controller Admin<Name> if that controller exists
        if (isset($url[0]) && strtolower($url[0]) === 'admin') {
            if (isset($url[1]) && file_exists('../app/controllers/Admin' . ucwords($url[1]) . '.php')) {
                $this->currentController = 'Admin' . ucwords($url[1]);
                unset($url[0]);
                unset($url[1]);
            } else {
                $this->currentController = 'Admin';
                unset($url[0]);
            }
            // Re-index $url so next segment becomes $url[0]
            $url = array_values($url);
        } elseif (isset($url[0]) && preg_match('/^admin([A-Za-z0-9_]+)$/i', $url[0], $m) && file_exists('../app/controllers/Admin' . ucwords($m[1]) . '.php')) {
            // Support single-segment routes like /admincontacts -> AdminContacts controller
            $this->currentController = 'Admin' . ucwords($m[1]);
            unset($url[0]);
            $url = array_values($url);
        } elseif (isset($url[0]) && file_exists('../app/controllers/' . ucwords($url[0]) . '.php')) {
            $this->currentController = ucwords($url[0]);
            unset($url[0]);
            $url = array_values($url);
        }

        require_once '../app/controllers/' . $this->currentController . '.php';
        $this->currentController = new $this->currentController;

        // After re-indexing, the method is now at $url[0]
        if (isset($url[0])) {
            if (method_exists($this->currentController, $url[0])) {
                $this->currentMethod = $url[0];
                unset($url[0]);
            }
        } else {
            // If no method segment provided and this is a POST request,
            // prefer calling `update` when available so POSTs to
            // controller base routes (e.g., /admin/about) reach update().
            if (isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST') {
                if (method_exists($this->currentController, 'update')) {
                    $this->currentMethod = 'update';
                }
            }
        }

        $this->params = $url ? array_values($url) : [];
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    public function getUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}
