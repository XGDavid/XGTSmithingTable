<?php

declare(strict_types=1);

namespace XGDAVID\XGTSmithingTable\crafting;

use pocketmine\crafting\ExactRecipeIngredient;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;

class SmithingRecipeRegistry
{

    /** @var SmithingRecipe[] */
    private array $recipes = [];

    public function registerDefaults(): void
    {
        $this->registerNetheriteUpgrades();
    }

    private function registerNetheriteUpgrades(): void
    {
        $template = VanillaItems::NETHERITE_UPGRADE_SMITHING_TEMPLATE();
        $addition = VanillaItems::NETHERITE_INGOT();

        $this->registerTransformRecipe($template, VanillaItems::DIAMOND_SWORD(), $addition, VanillaItems::NETHERITE_SWORD());
        $this->registerTransformRecipe($template, VanillaItems::DIAMOND_PICKAXE(), $addition, VanillaItems::NETHERITE_PICKAXE());
        $this->registerTransformRecipe($template, VanillaItems::DIAMOND_AXE(), $addition, VanillaItems::NETHERITE_AXE());
        $this->registerTransformRecipe($template, VanillaItems::DIAMOND_SHOVEL(), $addition, VanillaItems::NETHERITE_SHOVEL());
        $this->registerTransformRecipe($template, VanillaItems::DIAMOND_HOE(), $addition, VanillaItems::NETHERITE_HOE());
        $this->registerTransformRecipe($template, VanillaItems::DIAMOND_HELMET(), $addition, VanillaItems::NETHERITE_HELMET());
        $this->registerTransformRecipe($template, VanillaItems::DIAMOND_CHESTPLATE(), $addition, VanillaItems::NETHERITE_CHESTPLATE());
        $this->registerTransformRecipe($template, VanillaItems::DIAMOND_LEGGINGS(), $addition, VanillaItems::NETHERITE_LEGGINGS());
        $this->registerTransformRecipe($template, VanillaItems::DIAMOND_BOOTS(), $addition, VanillaItems::NETHERITE_BOOTS());
    }

    public function registerTransformRecipe(Item $template, Item $input, Item $addition, Item $output): void
    {
        $this->recipes[] = new SmithingTransformRecipe(
            new ExactRecipeIngredient($input),
            new ExactRecipeIngredient($addition),
            new ExactRecipeIngredient($template),
            $output
        );
    }

    public function matchRecipe(Item $template, Item $input, Item $addition): ?SmithingRecipe
    {
        foreach ($this->recipes as $recipe) {
            if (
                $recipe->getTemplate()->accepts($template) &&
                $recipe->getInput()->accepts($input) &&
                $recipe->getAddition()->accepts($addition)) {
                return $recipe;
            }
        }
        return null;
    }

    public function getRecipeFromIndex(int $index): ?SmithingRecipe
    {
        return $this->recipes[$index] ?? null;
    }

    /** @return SmithingRecipe[] */
    public function getRecipes(): array
    {
        return $this->recipes;
    }
}
