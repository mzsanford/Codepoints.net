<?php


/**
 * a HTTP request plus data
 */
class Request {

    public $type = 'text/html';

    public $availableTypes = array('text/html', 'application/json');

    public $extensionTypeMap = array(
        'htm' => 'text/html',
        'html' => 'text/html',
        'xml' => 'application/xml',
        'json' => 'application/json',
        'js' => 'application/json',
        'css' => 'text/css',
    );

    public $lang = 'en';

    public $availableLanguages = array('en');

    public $url = '/';

    public $trunkUrl = '/';

    public $data;

    public $method = "GET";

    public function __construct($url=Null) {
        if ($url === Null) {
            $url = $_SERVER['REQUEST_URI'];
        }
        $this->url = $this->trunkUrl = $url;
        if (strpos($url, '?') !== False) {
            $this->trunkUrl = strstr($url, '?', True);
        }
        if (array_key_exists('HTTP_ACCEPT', $_SERVER)) {
            $types = explode(',', $_SERVER['HTTP_ACCEPT']);
            for ($i = 0, $j = count($types); $i < $j; $i++) {
                $type = explode(';', $types[$i]);
                $type = trim($type[0]);
                if (in_array($type, $this->availableTypes)) {
                    $this->type = $type;
                    break;
                }
            }
        }
        if (array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER)) {
            $langs = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
            for ($i = 0, $j = count($langs); $i < $j; $i++) {
                $lang = explode(';', $langs[$i]);
                $lang = trim($lang[0]);
                if (in_array($lang, $this->availableLanguages)) {
                    $this->lang = $lang;
                    break;
                }
            }
        }
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        $ext = strtolower(ltrim($ext, '.'));
        if ($ext && array_key_exists($ext, $this->extensionTypeMap)) {
            $this->trunkUrl = substr($this->trunkUrl, 0, -strlen($ext)-1);
            $this->type = $this->extensionTypeMap[$ext];
        }
        if (array_key_exists('REQUEST_METHOD', $_SERVER)) {
            $this->method = strtoupper($_SERVER['REQUEST_METHOD']);
            if (! in_array($this->method, array("GET", "POST", "HEAD", "PUT",
                                                "DELETE"))) {
                $this->method = "GET";
            }
        }
    }

}


//__END__
