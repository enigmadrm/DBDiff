<?php namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class AlterTableAddColumnSQL implements SQLGenInterface {

    function __construct($obj) {
        $this->obj = $obj;
    }

    public function getUp() {
        $table = $this->obj->table;
        $schema = $this->obj->diff->getNewValue();
        $after = $this->obj->after;
        return "ADD $schema " . ($after ? "AFTER `$after`" : 'FIRST');
    }

    public function getDown() {
        $table = $this->obj->table;
        $column = $this->obj->column;
        return "DROP `$column`";
    }

}
