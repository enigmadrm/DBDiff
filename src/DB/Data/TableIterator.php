<?php namespace DBDiff\DB\Data;

class TableIterator {

    public $connection;
    public $table;
    public $offset;
    public $size;
    public $where;

    function __construct($connection, $table) {
        $this->connection = $connection;
        $this->table = $table;
        $this->offset = 0;
        $this->size = $connection->table($table)->count();
    }

    public function hasNext() {
        return $this->offset < $this->size;
    }

    public function next($size) {
        $data = $this->connection->table($this->table);
        if ($this->where) {
            $data = $data->whereRaw($this->where);
        }
        $data = $data->skip($this->offset)->take($size)->get();
        $this->offset += $size;
        return $data;
    }

    public function where($where) {
        $this->where = $where;
        return $this;
    }

    public function setOffset($offset) {
        $this->offset = $offset;
        return $this;
    }

}
