<?php

namespace Flm;


use ReflectionMethod;
use RuntimeException;
use CachedEcho;
use Throwable;

abstract class BaseController
{
    /**
     * @var FileManager
     */
    protected $flm;

    protected $config;

    protected $currentDirectory;

    public function __construct($config)
    {

        $this->config = $config;
    }

    public function handleRequest()
    {
        global $topDirectory;

        if (isset($_POST['action'])) {
            $action = $_POST['action'];

            $call = json_decode($action, true);

            $call = $call ? $call : ['method' => $action];
        } elseif (isset($_POST['cmd'])) {
            $call = $_POST;
        } else {
            self::jsonError('Invalid action');
        }

        try {
            $this->flm = new FileManager($topDirectory, $this->config);

            $out = $this->_processCall((object)$call);

            self::jsonOut($out);

        } catch (Throwable $err) {
            self::jsonError(['code' => $err->getCode(), 'msg' => $err->getMessage()]);
        }

    }

    public static function jsonError($errcode, $msg = 'Internal error')
    {
        self::jsonOut(['errcode' => $errcode, 'status' => 'ERROR', 'msg' => $msg]);
        die();
    }

    public static function jsonOut($data)
    {

        CachedEcho::send(json_encode($data), 'application/json', false);
    }

    /**
     * @param $call
     * @return mixed|null
     * @throws Throwable
     */
    protected function _processCall($call)
    {

        $method = $call->method;

        if ((substr($method, 0, 1) == '_')) {
            throw new RuntimeException("Invalid method");
        }

        unset($call->method);

        $out = null;
        if (method_exists($this, $method)) {
            $reflectionMethod = new ReflectionMethod($this, $method);
            if (!$reflectionMethod->isPublic()) {

                throw new RuntimeException("Invalid method");
            }

            $out = call_user_func_array([$this, $method], [$call]);
        } else {
            throw new RuntimeException("Invalid method");
        }

        return $out;
    }

    public function _getPostData($post_keys, $json = true)
    {
        $ret = [];
        foreach ($post_keys as $key => $err_code) {

            if (!isset($_POST[$key]) || ($json && !($files = json_decode($_POST[$key], true)))) {

                self::jsonError($err_code);
                return false;

            }

            $ret[$key] = $_POST[$key];
        }

        return $ret;

    }

    public function flm(): FileManager
    {
        return $this->flm;
    }
}