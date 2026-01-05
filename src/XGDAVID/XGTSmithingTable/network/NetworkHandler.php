<?php

declare(strict_types=1);

namespace XGDAVID\XGTSmithingTable\network;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\CraftingDataPacket;
use pocketmine\network\mcpe\protocol\types\recipe\SmithingTransformRecipe as ProtocolSmithingTransformRecipe;
use pocketmine\utils\Binary;
use Throwable;
use XGDAVID\XGTSmithingTable\crafting\SmithingTransformRecipe;
use XGDAVID\XGTSmithingTable\Main;

class NetworkHandler implements Listener
{

    public const SMITHING_RECIPE_NETWORK_OFFSET = 200000;

    private Main $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @priority LOWEST
     */
    public function onDataPacketSend(DataPacketSendEvent $event): void
    {
        foreach ($event->getPackets() as $packet) {
            if ($packet instanceof CraftingDataPacket) {
                $this->injectSmithingRecipes($packet);
            }
        }
    }

    private function injectSmithingRecipes(CraftingDataPacket $packet): void
    {
        $converter = TypeConverter::getInstance();
        $recipes = $this->plugin->getSmithingRecipeRegistry()->getRecipes();
        $index = self::SMITHING_RECIPE_NETWORK_OFFSET;

        foreach ($recipes as $recipe) {
            if ($recipe instanceof SmithingTransformRecipe) {
                try {
                    $packet->recipesWithTypeIds[] = new ProtocolSmithingTransformRecipe(
                        CraftingDataPacket::ENTRY_SMITHING_TRANSFORM,
                        Binary::writeInt($index),
                        $converter->coreRecipeIngredientToNet($recipe->getTemplate()),
                        $converter->coreRecipeIngredientToNet($recipe->getInput()),
                        $converter->coreRecipeIngredientToNet($recipe->getAddition()),
                        $converter->coreItemStackToNet($recipe->getResult()),
                        "smithing_table",
                        $index
                    );
                } catch (Throwable) {
                }
            }
            $index++;
        }
    }
}
