<?php


/**
 * handle requests and route them to the appropriate action
 */
class Router {

    protected $baseURL = '/';

    protected $actions = array();

    protected $urls = array();

    protected $settings = array();

    protected static $defaultRouter;

    /**
     * singleton: get instance
     */
    public static function getRouter() {
        if (! self::$defaultRouter) {
            self::$defaultRouter = new self();
            self::$defaultRouter->base(rtrim(dirname($_SERVER['PHP_SELF']),
                                       '/').'/');
        }
        return self::$defaultRouter;
    }

    /**
     * register an action for an URL
     *
     * A test can be a function, an array or a string. The final URL will be
     * matched against that test (or the test will be called with it).
     */
    public function registerAction($test, $action) {
        $this->actions[] = array($test, $action);
        return $this;
    }

    /**
     * get the action for an URL
     *
     * If no URL matches, the function returns Null
     */
    public function getAction($url=Null) {
        if ($url === Null) {
            $url = substr($_SERVER['REQUEST_URI'],
                                 strlen($this->baseURL));
            if ($url === False) {
                $url = '';
            }
        }
        $req = new Request($url);
        $this->addSetting('request', $req);
        $tUrl = $req->trunkUrl;
        foreach ($this->actions as $action) {
            // we check first for array, because else __autoload
            // will get in the way. This means, however, that we
            // can't use the array($class, $method) approach to
            // call a route.
            if (is_array($action[0])) {
                $r = in_array($tUrl, $action[0]);
            } elseif (is_callable($action[0])) {
                $r = $action[0]($tUrl, $this->settings);
            } else {
                $r = ($action[0] === $tUrl);
            }
            if ($r !== False) {
                $req->data = $r;
                return array($action[1], $req);
            }
        }
        return Null;
    }

    /**
     * call the registered action for an URL
     *
     * If no registered action is found, the function returns False.
     */
    public function callAction($url=Null) {
        $action = $this->getAction($url);
        if ($action !== Null) {
            if ($this->settings['request']->type === 'text/html') {
                header('Content-Type: text/html; charset=utf-8');
            }
            try {
                $action[0]($action[1], $this->settings);
            } catch (RoutingError $e) {
                return False;
            }
            return True;
        }
        return False;
    }

    /**
     * add a setting, that applies to all possible actions
     */
    public function addSetting($key, $value) {
        $this->settings[$key] = $value;
        return $this;
    }

    /**
     * get a setting, that applies to all possible actions
     */
    public function getSetting($key, $default=Null) {
        if (array_key_exists($key, $this->settings)) {
            return $this->settings[$key];
        }
        return $default;
    }

    /**
     * register an URL creation scheme for a class
     */
    public function registerUrl($class, $action) {
        $this->urls[$class] = $action;
        return $this;
    }

    /**
     * get the URL for an object based on registerUrl input
     */
    public function getUrl($object=Null) {
        $path = '';
        if ($object === Null) {
            return $this->baseURL;
        } elseif (is_string($object)) {
            $class = $object;
        } elseif (is_object($object)) {
            $class = get_class($object);
        } else {
            throw new Exception("Unsupported type ".gettype($object));
        }
        if (array_key_exists($class, $this->urls)) {
            $path = $this->urls[$class]($object);
        } elseif (is_string($object)) {
            $path = ltrim($object, '/');
        }
        return $this->baseURL . $path;
    }

    /**
     * get or set the base URL
     *
     * When setting, returns the old base URL
     */
    public function base($val=NULL) {
        $b = $this->baseURL;
        if ($val !== NULL) {
            $this->baseURL = $val;
        }
        return $b;
    }

    /**
     * redirect to another URL
     */
    public function redirect($url, $http=303) {
        if (substr($url, 0, 4) !== 'http' && $url[0] !== '/') {
            $url = $this->baseURL . $url;
        }
        switch ($http) {
            case 301:
                header('HTTP/1.0 301 Moved Permanently');
                break;
            case 302:
                header('HTTP/1.0 302 Found');
                break;
            case 307:
                header('HTTP/1.0 307 Temporary Redirect');
                break;
            default:
                header('HTTP/1.0 303 See Other');
        }
        header('Location: ' . $url);
        exit();
    }

}


/**
 * thrown, when a view function detects a 404
 */
class RoutingError extends Exception {}


//__END__
