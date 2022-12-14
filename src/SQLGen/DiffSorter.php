<?php namespace DBDiff\SQLGen;


class DiffSorter {

    private $up_order = [
        "SetDBCharset",
        "SetDBCollation",

        "AddTable",

        "DeleteData",
        "DropTable",

        "AlterTableEngine",
        "AlterTableCollation",

        "AlterTableAddColumn",
        "AlterTableChangeColumn",
        "AlterTableDropColumn",

        "AlterTableAddKey",
        "AlterTableChangeKey",
        "AlterTableDropKey",

        "AlterTableAddConstraint",
        "AlterTableChangeConstraint",
        "AlterTableDropConstraint",

        "InsertData",
        "UpdateData"
    ];

    private $down_order = [
        "SetDBCharset",
        "SetDBCollation",

        "InsertData",
        "AddTable",

        "DropTable",

        "AlterTableEngine",
        "AlterTableCollation",

        "AlterTableAddColumn",
        "AlterTableChangeColumn",
        "AlterTableDropColumn",

        "AlterTableAddKey",
        "AlterTableChangeKey",
        "AlterTableDropKey",

        "AlterTableAddConstraint",
        "AlterTableChangeConstraint",
        "AlterTableDropConstraint",

        "DeleteData",
        "UpdateData"
    ];

    public function sort($diff, $type) {
        $tableDiffs = [];
        foreach ($diff as $item) {
            if (isset($item->table)) {
                if (!isset($tableDiffs[$item->table])) {
                    $tableDiffs[$item->table] = [];
                }
                $tableDiffs[$item->table][] = $item;
            }
        }
        foreach ($tableDiffs as $table => $diffs) {
            usort($diffs, [$this, 'compare'.ucfirst($type)]);
            $tableDiffs[$table] = $diffs;
        }
        return $tableDiffs;
    }

    private function compareUp($a, $b) {
        return $this->compare($this->up_order, $a, $b);
    }

    private function compareDown($a, $b) {
        return $this->compare($this->down_order, $a, $b);
    }

    private function compare($order, $a, $b) {
        $order = array_flip($order);
        $reflectionA = new \ReflectionClass($a);
        $reflectionB = new \ReflectionClass($b);
        $sqlGenClassA = $reflectionA->getShortName();
        $sqlGenClassB = $reflectionB->getShortName();
        $indexA = $order[$sqlGenClassA];
        $indexB = $order[$sqlGenClassB];

        if ($indexA === $indexB) {
            if (isset($a->position) && isset($b->position)) {
                if ($a->position > $b->position) {
                    return 1;
                } else if ($b->position < $a->position) {
                    return -1;
                } else {
                    return 0;
                }
            }
            return 0;
        }
        else if ($indexA > $indexB) return 1;
        return -1;
    }
}
