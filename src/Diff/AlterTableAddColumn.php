<?php namespace DBDiff\Diff;


class AlterTableAddColumn {

    function __construct($table, $column, $diff, $position, $after=null) {
        $this->table = $table;
        $this->column = $column;
        $this->diff = $diff;
        $this->position = $position;
        $this->after = $after;
    }
}
