<?php namespace DBDiff\SQLGen\DiffToSQL;

use App\Models\Page;
use DBDiff\SQLGen\SQLGenInterface;


class AlterTableChangeKeySQL implements SQLGenInterface {

    function __construct($obj) {
        $this->obj = $obj;
    }

    public function getUp() {
        $table = $this->obj->table;
        $key = $this->obj->key;
        $schema = $this->obj->diff->getNewValue();
        if ($key === 'PRIMARY') {
            return "DROP PRIMARY KEY, ADD $schema";
        } else {
            return "DROP INDEX `$key`, ADD $schema";
        }
    }

    public function getDown() {
        $table = $this->obj->table;
        $key = $this->obj->key;
        $schema = $this->obj->diff->getOldValue();
        if ($key === 'PRIMARY') {
            return "DROP PRIMARY KEY, ADD $schema";
        } else {
            return "DROP INDEX `$key`, ADD $schema";
        }
    }

}
