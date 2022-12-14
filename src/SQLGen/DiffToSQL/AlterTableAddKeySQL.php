<?php namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class AlterTableAddKeySQL implements SQLGenInterface {

    function __construct($obj) {
        $this->obj = $obj;
    }

    public function getUp() {
        $table = $this->obj->table;
        $schema = $this->obj->diff->getNewValue();
        return "ADD $schema";
    }

    public function getDown() {
        $table = $this->obj->table;
        $key   = $this->obj->key;
        if ($key === 'PRIMARY') {
            return "DROP PRIMARY KEY";
        } else {
            return "DROP INDEX `$key`";
        }
    }

}
