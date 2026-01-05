<?php

declare(strict_types=1);

namespace XGDAVID\XGTSmithingTable\network;

use pocketmine\block\inventory\SmithingTableInventory;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\protocol\ItemStackRequestPacket;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\CraftRecipeAutoStackRequestAction;
use pocketmine\network\mcpe\protocol\types\inventory\stackrequest\CraftRecipeStackRequestAction;
use pocketmine\player\Player;
use pocketmine\world\sound\AnvilUseSound;
use XGDAVID\XGTSmithingTable\crafting\SmithingRecipe;
use XGDAVID\XGTSmithingTable\Main;
use XGDAVID\XGTSmithingTable\SmithingTableListener;

class ItemStackRequestHandler implements Listener
{

    private Main $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @priority LOWEST
     */
    public function onDataPacketReceive(DataPacketReceiveEvent $event): void
    {
        $packet = $event->getPacket();
        $player = $event->getOrigin()->getPlayer();

        if (!($packet instanceof ItemStackRequestPacket) || $player === null) {
            return;
        }

        $window = $player->getCurrentWindow();
        if (!($window instanceof SmithingTableInventory)) {
            return;
        }

        foreach ($packet->getRequests() as $request) {
            foreach ($request->getActions() as $action) {
                if ($action instanceof CraftRecipeStackRequestAction || $action instanceof CraftRecipeAutoStackRequestAction) {
                    $recipeId = $action->getRecipeId();

                    if ($recipeId >= NetworkHandler::SMITHING_RECIPE_NETWORK_OFFSET) {
                        $recipeIndex = $recipeId - NetworkHandler::SMITHING_RECIPE_NETWORK_OFFSET;
                        $recipe = $this->plugin->getSmithingRecipeRegistry()->getRecipeFromIndex($recipeIndex);

                        if ($recipe !== null) {
                            $this->handleSmithingCraft($player, $window, $recipe, $event);
                            return;
                        }
                    }
                }
            }
        }
    }

    private function handleSmithingCraft(Player $player, SmithingTableInventory $inventory, SmithingRecipe $recipe, DataPacketReceiveEvent $event): void
    {
        $input = $inventory->getItem(SmithingTableListener::SLOT_INPUT);
        $addition = $inventory->getItem(SmithingTableListener::SLOT_ADDITION);
        $template = $inventory->getItem(SmithingTableListener::SLOT_TEMPLATE);

        if ($input->isNull() || $addition->isNull() || $template->isNull()) {
            return;
        }

        if (
            !$recipe->getInput()->accepts($input) ||
            !$recipe->getAddition()->accepts($addition) ||
            !$recipe->getTemplate()->accepts($template)) {
            return;
        }

        $output = $recipe->getResultFor([$template, $input, $addition]);
        if ($output === null) {
            return;
        }

        $event->cancel();

        $newTemplate = clone $template;
        $newTemplate->setCount($template->getCount() - 1);

        $newAddition = clone $addition;
        $newAddition->setCount($addition->getCount() - 1);

        $inventory->setItem(SmithingTableListener::SLOT_INPUT, VanillaItems::AIR());
        $inventory->setItem(SmithingTableListener::SLOT_ADDITION, $newAddition->isNull() ? VanillaItems::AIR() : $newAddition);
        $inventory->setItem(SmithingTableListener::SLOT_TEMPLATE, $newTemplate->isNull() ? VanillaItems::AIR() : $newTemplate);

        $playerInv = $player->getInventory();
        if ($playerInv->canAddItem($output)) {
            $playerInv->addItem($output);
        } else {
            $player->getWorld()->dropItem($player->getPosition()->add(0, 1.3, 0), $output);
        }

        $player->getWorld()->addSound($player->getPosition(), new AnvilUseSound());
        $player->getNetworkSession()->getInvManager()?->syncAll();
    }
}
