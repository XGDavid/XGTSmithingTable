<?php

declare(strict_types=1);

namespace XGDAVID\XGTSmithingTable;

use pocketmine\block\inventory\SmithingTableInventory;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;

class SmithingTableListener implements Listener
{

    public const SLOT_INPUT = 0;
    public const SLOT_ADDITION = 1;
    public const SLOT_TEMPLATE = 2;

    /**
     * @priority NORMAL
     */
    public function onInventoryClose(InventoryCloseEvent $event): void
    {
        $inventory = $event->getInventory();
        $player = $event->getPlayer();

        if (!($inventory instanceof SmithingTableInventory)) {
            return;
        }

        $this->returnItemsToPlayer($player, $inventory);
    }

    private function returnItemsToPlayer(Player $player, SmithingTableInventory $inventory): void
    {
        for ($slot = 0; $slot < 3; $slot++) {
            $item = $inventory->getItem($slot);
            if (!$item->isNull()) {
                $this->giveItemToPlayer($player, $item);
                $inventory->setItem($slot, VanillaItems::AIR());
            }
        }
    }

    private function giveItemToPlayer(Player $player, Item $item): void
    {
        $playerInventory = $player->getInventory();
        if ($playerInventory->canAddItem($item)) {
            $playerInventory->addItem($item);
        } else {
            $player->getWorld()->dropItem($player->getPosition()->add(0, 1.3, 0), $item);
        }
    }
}
