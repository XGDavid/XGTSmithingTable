<?php

declare(strict_types=1);

namespace XGDAVID\XGTSmithingTable;

use pocketmine\plugin\PluginBase;
use XGDAVID\XGTSmithingTable\crafting\SmithingRecipeRegistry;
use XGDAVID\XGTSmithingTable\libs\bStats\Metrics;
use XGDAVID\XGTSmithingTable\network\ItemStackRequestHandler;
use XGDAVID\XGTSmithingTable\network\NetworkHandler;

class Main extends PluginBase
{
    private const BSTATS_PLUGIN_ID = 28706;

    private SmithingRecipeRegistry $recipeRegistry;

    public function onEnable(): void
    {
        $metrics = new Metrics($this, self::BSTATS_PLUGIN_ID);
        $metrics->scheduleMetricsDataSend();

        $this->recipeRegistry = new SmithingRecipeRegistry();
        $this->recipeRegistry->registerDefaults();

        $this->getServer()->getPluginManager()->registerEvents(new SmithingTableListener(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new NetworkHandler($this), $this);
        $this->getServer()->getPluginManager()->registerEvents(new ItemStackRequestHandler($this), $this);
    }

    public function getSmithingRecipeRegistry(): SmithingRecipeRegistry
    {
        return $this->recipeRegistry;
    }
}
