<?php namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class AlterTableEngineSQL implements SQLGenInterface {

    function __construct($obj) {
        $this->obj = $obj;
    }

    public function getUp() {
        $table = $this->obj->table;
        $engine = $this->obj->engine;
        return "ENGINE = $engine";
    }

    public function getDown() {
        $table = $this->obj->table;
        $prevEngine = $this->obj->prevEngine;
        return "ENGINE = $prevEngine";
    }

}
