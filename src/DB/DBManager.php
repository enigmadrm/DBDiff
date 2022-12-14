<?php namespace DBDiff\DB;

use Illuminate\Database\Capsule\Manager as Capsule;
use DBDiff\Exceptions\DBException;
use Illuminate\Support\Arr;


class DBManager {

    function __construct($params) {
        $this->capsule = new Capsule;
        $this->params = $params;
    }

    public function connect() {
        foreach ($this->params->input as $key => $input) {
            if ($key === 'kind') continue;
            $server = $this->params->{$input['server']};
            $db = $input['db'];
            $this->capsule->addConnection([
                'driver'    => 'mysql',
                'host'      => $server['host'],
                'port'      => $server['port'],
                'database'  => $db,
                'username'  => $server['user'],
                'password'  => $server['password'],
                'charset'   => 'utf8',
                'collation' => 'utf8_unicode_ci'
            ], $key);
        }
    }

    public function testResources() {
        $this->testResource($this->params->input['source'], 'source');
        $this->testResource($this->params->input['target'], 'target');
    }

    public function testResource($input, $res) {
        try {
            $this->capsule->getConnection($res);
        } catch(\Exception $e) {
            throw new DBException("Can't connect to target database");
        }
        if (!empty($input['table'])) {
            try {
                $this->capsule->getConnection($res)->table($input['table'])->first();
            } catch(\Exception $e) {
                throw new DBException("Can't access target table");
            }
        }
    }

    public function getDB($res) {
        return $this->capsule->getConnection($res);
    }

    public function getTables($connection) {
        $result = $this->getDB($connection)->select("SHOW FULL TABLES WHERE table_type = 'base table'");
        $tables = [];
        foreach ($result as $res) {
            $res = (array) $res;
            $tables[] = array_shift($res);
        }
        return $tables;
    }

    public function getColumns($connection, $table) {
        $result = $this->getDB($connection)->select("show columns from `$table`");
        return Arr::pluck($result, 'Field');
    }

    public function getKey($connection, $table) {
        $keys = $this->getDB($connection)->select("show indexes from `$table`");
        $ukey = [];
        foreach ($keys as $key) {
            if ($key->{'Key_name'} === 'PRIMARY') {
                $ukey[] = $key->{'Column_name'};
            }
        }
        return $ukey;
    }

}
