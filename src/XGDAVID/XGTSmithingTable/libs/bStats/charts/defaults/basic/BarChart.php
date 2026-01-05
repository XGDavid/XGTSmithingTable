<?php
declare(strict_types=1);

namespace XGDAVID\XGTSmithingTable\libs\bStats\charts\defaults\basic;

use XGDAVID\XGTSmithingTable\libs\bStats\charts\CallbackChart;

class BarChart extends CallbackChart
{
    public static function getType(): string { return "bar"; }

    protected function getValue(): mixed
    {
        $value = $this->call();
        if (empty($value)) return null;
        return $value;
    }
}

