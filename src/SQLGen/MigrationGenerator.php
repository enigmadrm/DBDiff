<?php namespace DBDiff\SQLGen;


class MigrationGenerator {

    public static function generate($tableDiffs, $method) {
        $sql = ["SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;\n\n"];
        foreach ($tableDiffs as $table => $diffs) {
            $alterArray = [];
            foreach ($diffs as $diff) {
                $reflection = new \ReflectionClass($diff);
                $sqlGenClass = __NAMESPACE__ . "\\DiffToSQL\\" . $reflection->getShortName() . "SQL";
                $gen = new $sqlGenClass($diff);
                if (stripos($reflection->getShortName(), 'Alter') === 0) {
                    $alterArray[] = $gen->$method();
                } else {
                    $sql[] = $gen->$method() . ";\n\n";
                }
            }
            if (count($alterArray)) {
                $sql[] = "ALTER TABLE `$table`\n\t" . implode(",\n\t", $alterArray) . ";\n\n";
            }
        }
        return $sql;
    }

}
