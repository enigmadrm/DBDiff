<?php namespace DBDiff\SQLGen\DiffToSQL;

use DBDiff\SQLGen\SQLGenInterface;


class AlterTableCollationSQL implements SQLGenInterface {

    function __construct($obj) {
        $this->obj = $obj;
    }

    public function getUp() {
        $table = $this->obj->table;
        $collation = $this->obj->collation;
        return "DEFAULT COLLATE $collation";
    }

    public function getDown() {
        $table = $this->obj->table;
        $prevCollation = $this->obj->prevCollation;
        return "DEFAULT COLLATE $prevCollation";
    }

}
