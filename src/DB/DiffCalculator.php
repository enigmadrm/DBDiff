<?php namespace DBDiff\DB;

use DBDiff\DB\Schema\DBSchema;
use DBDiff\DB\Schema\TableSchema;
use DBDiff\DB\Data\DBData;
use DBDiff\DB\Data\TableData;


class DiffCalculator {

    function __construct($params) {
        $this->manager = new DBManager($params);
        $this->params = $params;
    }

    public function getDiff() {
        // Connect and test accessibility
        $this->manager->connect();
        $this->manager->testResources();

        $schemaDiff = [];
        $dataDiff = [];

        // Schema diff
        if ($this->params->type === 'schema') {
            if ($this->params->input['kind'] === 'db') {
                $dbSchema = new DBSchema($this->manager, $this->params);
                $schemaDiff = $dbSchema->getDiff($this->params);
            } else {
                $tableSchema = new TableSchema($this->manager, $this->params);
                $schemaDiff = $tableSchema->getDiff($this->params->input['source']['table']);
            }
        }
        // New Data diff
        else if ($this->params->type === 'newdata') {
            $this->params->newDataOnly = true;
            if ($this->params->input['kind'] === 'db') {
                $dbData = new DBData($this->manager, $this->params);
                $dataDiff = $dbData->getDiff();
            } else {
                $tableData = new TableData($this->manager, $this->params);
                $dataDiff = $tableData->getDiff($this->params->input['source']['table']);
            }
        }
        else if ($this->params->type === 'fulldata') {
            if ($this->params->input['kind'] === 'db') {
                $dbData = new DBData($this->manager, $this->params);
                $dataDiff = $dbData->getDiff();
            } else {
                $tableData = new TableData($this->manager, $this->params);
                $dataDiff = $tableData->getDiff($this->params->input['source']['table']);
            }
        }

        return [
            'schema' => $schemaDiff,
            'data'   => $dataDiff,
        ];

    }
}
