<?php

declare(strict_types=1);

namespace DaPigGuy\PiggyCustomEnchants\enchants\tools\hoe;

use DaPigGuy\PiggyCustomEnchants\enchants\CustomEnchant;
use DaPigGuy\PiggyCustomEnchants\enchants\ReactiveEnchantment;
use pocketmine\block\BlockLegacyIds;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\player\Player;

/**
 * Class FertilizerEnchant
 * @package DaPigGuy\PiggyCustomEnchants\enchants\tools\hoe
 */
class FertilizerEnchant extends ReactiveEnchantment
{
    /** @var string */
    public $name = "Fertilizer";
    /** @var int */
    public $maxLevel = 3;

    /**
     * @return array
     */
    public function getReagent(): array
    {
        return [PlayerInteractEvent::class];
    }

    /**
     * @return array
     */
    public function getDefaultExtraData(): array
    {
        return ["radiusMultiplier" => 1];
    }

    /**
     * @param Player $player
     * @param Item $item
     * @param Inventory $inventory
     * @param int $slot
     * @param Event $event
     * @param int $level
     * @param int $stack
     */
    public function react(Player $player, Item $item, Inventory $inventory, int $slot, Event $event, int $level, int $stack): void
    {
        if ($event instanceof PlayerInteractEvent) {
            $block = $event->getBlock();
            if ($block->getId() === BlockLegacyIds::GRASS || ($block->getId() === BlockLegacyIds::DIRT && $block->getMeta() === 0)) {
                $radius = $level * $this->extraData["radiusMultiplier"];
                for ($x = -$radius; $x <= $radius; $x++) {
                    for ($z = -$radius; $z <= $radius; $z++) {
                        $newBlock = $block->getPos()->getWorld()->getBlock($block->getPos()->add($x, 0, $z));
                        if ($newBlock->getId() === BlockLegacyIds::GRASS || ($newBlock->getId() === BlockLegacyIds::DIRT && $newBlock->getMeta() === 0)) {
                            $this->setCooldown($player, 1);
                            $block->getPos()->getWorld()->useItemOn($newBlock->getPos(), $item, 0, $newBlock->getPos(), $player);
                        }
                    }
                }
            }
        }
    }

    /**
     * @return int
     */
    public function getItemType(): int
    {
        return CustomEnchant::ITEM_TYPE_HOE;
    }
}