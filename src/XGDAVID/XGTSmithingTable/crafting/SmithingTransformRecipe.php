<?php

declare(strict_types=1);

namespace XGDAVID\XGTSmithingTable\crafting;

use pocketmine\crafting\RecipeIngredient;
use pocketmine\item\Item;

class SmithingTransformRecipe implements SmithingRecipe
{

    private Item $result;

    public function __construct(
        private readonly RecipeIngredient $input,
        private readonly RecipeIngredient $addition,
        private readonly RecipeIngredient $template,
        Item                              $result
    )
    {
        $this->result = clone $result;
    }

    public function getInput(): RecipeIngredient
    {
        return $this->input;
    }

    public function getAddition(): RecipeIngredient
    {
        return $this->addition;
    }

    public function getTemplate(): RecipeIngredient
    {
        return $this->template;
    }

    /** @param Item[] $inputs */
    public function getResultFor(array $inputs): ?Item
    {
        foreach ($inputs as $item) {
            if ($this->input->accepts($item)) {
                $result = $this->getResult();
                $result->setNamedTag($item->getNamedTag());
                return $result;
            }
        }
        return null;
    }

    public function getResult(): Item
    {
        return clone $this->result;
    }
}
