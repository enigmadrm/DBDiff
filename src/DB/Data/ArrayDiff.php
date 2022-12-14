<?php namespace DBDiff\DB\Data;

use Diff\Differ\MapDiffer;
use Diff\DiffOp\DiffOpAdd;
use Diff\DiffOp\DiffOpRemove;
use Illuminate\Support\Arr;


class ArrayDiff {

    public static $size = 1000;
    public $rows = 0;

    function __construct($key, $dbiterator1, $dbiterator2, $params) {
        $this->key = $key;
        $this->dbiterator1 = $dbiterator1;
        $this->dbiterator2 = $dbiterator2;
        $this->sourceBucket = [];
        $this->targetBucket = [];
        $this->diffBucket = [];
        $this->params = $params;
    }

    public function getDiff($table, $where) {
        $this->dbiterator1->where($where);
        $this->dbiterator2->where($where);
        while ($this->dbiterator1->hasNext() || $this->dbiterator2->hasNext()) {
            if ($this->params->limit && count($this->diffBucket) >= $this->params->limit) break;
            $this->iterate($table);
            $this->tagChanges($table);
            $this->findNewOld();
        }
        return $this->diffBucket;
    }

    public function iterate($table) {
        $this->sourceBucket = $this->dbiterator1->next(ArrayDiff::$size)->toArray();
        foreach ($this->sourceBucket as &$entry) {
            $entry = (array) $entry;
        }
        $this->targetBucket = $this->dbiterator2->next(ArrayDiff::$size)->toArray();
        foreach ($this->targetBucket as &$entry) {
            $entry = (array) $entry;
        }
    }

    public function isKeyEqual($entry1, $entry2) {
        foreach ($this->key as $key) {
            if (empty($entry1[$key])) return false;
            if (empty($entry2[$key])) return false;
            if ($entry1[$key] !== $entry2[$key]) return false;
        }
        return true;
    }

    public function tagChanges($table) {
        if (!count($this->sourceBucket) || !count($this->targetBucket)) return;
        foreach ($this->sourceBucket as &$entry1) {
            if (empty($entry1)) continue;
            foreach ($this->targetBucket as &$entry2) {
                if (empty($entry2)) continue;

                if ($this->isKeyEqual($entry1, $entry2)) {

                    // unset the fields to ignore
                    if (isset($this->params->fieldsToIgnore[$table])) {
                        foreach ($this->params->fieldsToIgnore[$table] as $fieldToIgnore) {
                            unset($entry1[$fieldToIgnore]);
                            unset($entry2[$fieldToIgnore]);
                        }
                    }

                    $differ = new MapDiffer();
                    $diff = $differ->doDiff($entry2, $entry1);
                    if (!empty($diff)) {
                        $this->diffBucket[] = [
                            'keys' => Arr::only($entry1, $this->key),
                            'diff' => $diff
                        ];
                        if ($this->params->limit && count($this->diffBucket) >= $this->params->limit) return;
                    }
                    $entry1 = null;
                    $entry2 = null;
                }
            }
        }
    }

    public function findNewOld() {
        // New
        foreach ($this->sourceBucket as $entry) {
            if (empty($entry)) continue;
            if ($this->params->limit && count($this->diffBucket) >= $this->params->limit) break;
            $this->diffBucket[] = [
                'keys' => Arr::only($entry, $this->key),
                'diff' => new DiffOpAdd($entry)
            ];
        }

        // Deleted
        foreach ($this->targetBucket as $entry) {
            if (empty($entry)) continue;
            if ($this->params->limit && count($this->diffBucket) >= $this->params->limit) break;
            $this->diffBucket[] = [
                'keys' => Arr::only($entry, $this->key),
                'diff' => new DiffOpRemove($entry)
            ];
        }
    }
}
