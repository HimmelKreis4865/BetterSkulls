<?php

namespace HimmelKreis4865\BetterSkulls;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockSpreadEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\nbt\tag\StringTag;
use function get_class;
use function var_dump;

class EventListener implements Listener {
	/**
	 * @priority HIGHEST
	 * @ignoreCancelled false
	 *
	 * @param BlockBreakEvent $event
	 */
	public function onBreak(BlockBreakEvent $event) {
		if ($event->isCancelled()) return;
		if ($event->getBlock()->getId() === Block::SKULL_BLOCK) {
			/** @var SkullEntity $skull */
			if (($skull = $event->getBlock()->getLevelNonNull()->getNearestEntity($event->getBlock()->floor()->add(0.5, 0, 0.5), 0.5)) instanceof SkullEntity) {
				
				$name = ($skull->namedtag->hasTag("skull_name", StringTag::class) ? $skull->namedtag->getString("skull_name") : "-");
				
				$event->setDrops([BetterSkulls::constructPlayerHeadItem($name, $skull->getSkin())]);
				
				$skull->flagForDespawn();
			} else {
                                $event->setDrops([$event->getBlock()->getPickedItem()]);
                        }
		}
	}
	
	/**
	 * @priority HIGHEST
	 * @ignoreCancelled false
	 *
	 * @param BlockSpreadEvent $event
	 */
	public function onSpread(BlockSpreadEvent $event) {
		if ($event->isCancelled()) return;
		if ($event->getBlock()->getId() === Block::SKULL_BLOCK and $event->getBlock()->getDamage() === 1) {
			
			/** @var SkullEntity $skull */
			if (($skull = $event->getBlock()->getLevelNonNull()->getNearestEntity($event->getBlock()->floor()->add(0.5, 0, 0.5), 0.3)) instanceof SkullEntity) {
				
				$name = ($skull->namedtag->hasTag("skull_name", StringTag::class) ? $skull->namedtag->getString("skull_name") : "-");
				
				$event->getBlock()->getLevelNonNull()->dropItem($event->getBlock()->add(0, 0.5), BetterSkulls::constructPlayerHeadItem($name, $skull->getSkin()));
				
				$skull->flagForDespawn();
			}
		}
	}
}
