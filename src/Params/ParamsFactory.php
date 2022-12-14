<?php namespace DBDiff\Params;

use DBDiff\Exceptions\CLIException;


class ParamsFactory {

    public static function get($config=null) {

        $params = new DefaultParams;

        if (isset($config) && is_array($config)) {
            foreach ($config as $key => $val) {
                if (property_exists($params, $key)) {
                    $params->{$key} = $val;
                }
            }
        } else {
            $cli = new CLIGetter;
            $paramsCLI = $cli->getParams();

            if (!isset($paramsCLI->debug)) {
                error_reporting(E_ERROR);
            }

            $fs = new FSGetter($paramsCLI);
            $paramsFS = $fs->getParams();
            $params = self::merge($params, $paramsFS);

            $params = self::merge($params, $paramsCLI);
        }

        if (empty($params->server1)) {
            throw new CLIException("A server is required");
        }
        return $params;

    }

    static protected function merge($obj1, $obj2) {
        return (object) array_merge((array) $obj1, (array) $obj2);
    }
}
