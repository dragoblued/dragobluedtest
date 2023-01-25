<?php

namespace App\Traits;

trait StatsCounter
{
    public function incrementCount(string $modelName, $uniqueField, array $searchColumnsArr,
                                   string $columnName, $incrementCount = 1): bool
    {
        return $this->set($modelName, $uniqueField, $searchColumnsArr,
            $columnName, null, true, $incrementCount);
    }

    public function decrementCount(string $modelName, $uniqueField, array $searchColumnsArr,
                                   string $columnName, $incrementCount = 1): bool
    {
        return $this->set($modelName, $uniqueField, $searchColumnsArr,
            $columnName, null, false, $incrementCount);
    }

    public function setCount(string $modelName, $uniqueField, array $searchColumnsArr,
                             string $columnName, int $countNumber): bool
    {
        return $this->set($modelName, $uniqueField, $searchColumnsArr,
            $columnName, $countNumber, null);
    }

    private function set(string $modelName, $uniqueField, array $searchColumnsArr,
                         string $columnName, $countNumber, $increment, $incrementCount = 1): bool
    {
        if ($incrementCount === 0) {
            return false;
        }
        $item = $this->findItem($modelName, $uniqueField, $searchColumnsArr);
        if (!is_null($item)) {
            $currentCount = $item->$columnName;
            if (!is_null($countNumber)) {
                $newCount = (int) $countNumber;
            } elseif ((bool) $increment === true) {
                $newCount = $currentCount + $incrementCount;
            } elseif ((bool) $increment === false) {
                $newCount = $currentCount - $incrementCount;
            } else {
                return false;
            }
            $item->fill([$columnName => $newCount]);
            $item->save();
            return true;
        } else {
            return false;
        }
    }

    private function findItem(string $modelName, $uniqueField, array $searchColumnsArr) {
        $modelName = "\App\\$modelName";
        $class = new $modelName();
        return $class::where(function($q) use ($searchColumnsArr, $uniqueField){
            foreach($searchColumnsArr as $key){
                $q->orWhere($key, $uniqueField);
            }
        })->first();
    }
}
