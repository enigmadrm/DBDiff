<?php namespace DBDiff;

use DBDiff\Params\ParamsFactory;
use DBDiff\DB\DiffCalculator;
use DBDiff\SQLGen\SQLGenerator;
use DBDiff\Exceptions\BaseException;
use DBDiff\Logger;
use DBDiff\Templater;

class DBDiff {

    public $params;
    public static $logging = true;

    /**
     * @throws Exceptions\CLIException
     */
    public function __construct($params=null) {
        // Increase memory limit
        ini_set('memory_limit', '512M');

        $this->params = ParamsFactory::get($params);
    }

    public function suppressLogs() {
        DBDiff::$logging = false;
    }

    public function run() {

        $result = $this->generate();

        if ($result['identical']) {
            Logger::info("Source and target are identical");
            return;
        }

        try {

            // Generate
            $templater = new Templater($result['params'], $result['up'], $result['down']);
            $templater->output();

            Logger::success("Completed");

        } catch (\Exception $e) {
            if ($e instanceof BaseException) {
                Logger::error($e->getMessage(), true);
            } else {
                Logger::error("Unexpected error: " . $e->getMessage());
                throw $e;
            }
        }

    }

    public function generate() {

        try {
            // Diff
            $diffCalculator = new DiffCalculator($this->params);
            $diff = $diffCalculator->getDiff();

            // Empty diff
            if (empty($diff['schema']) && empty($diff['data'])) {
                return [
                    'params' => $this->params,
                    'result' => 'identical'
                ];

            } else {
                // SQL
                $sqlGenerator = new SQLGenerator($diff, $this->params);
                $up =''; $down = '';
                if ($this->params->include !== 'down') {
                    $up = $sqlGenerator->getUp();
                }
                if ($this->params->include !== 'up') {
                    $down = $sqlGenerator->getDown();
                }

                return [
                    'params' => $this->params,
                    'result' => 'different',
                    'up' => $up,
                    'down' => $down
                ];
            }

        } catch (\Exception $e) {
            if ($e instanceof BaseException) {
                Logger::error($e->getMessage(), true);
            } else {
                Logger::error("Unexpected error: " . $e->getMessage());
                throw $e;
            }
        }

    }


}
