<?php

namespace HimmelKreis4865\BetterSkulls;

use pocketmine\entity\Human;
use pocketmine\event\entity\EntityDamageEvent;

class SkullEntity extends Human {
	/** @var float $width */
	public $width = 0.025;
	
	/** @var float $height */
	public $height = 0.025;
	
	protected function initEntity(): void {
		$this->setMaxHealth(1);
		$this->setImmobile();
		$this->setScale(1.1275);
		parent::initEntity();
	}
	
	/**
	 * Cancels EntityDamageEvent to prevent abusing bugs
	 *
	 * @internal
	 *
	 * @param EntityDamageEvent $source
	 */
	public function attack(EntityDamageEvent $source): void {
		$source->setCancelled();
	}
	
	/**
	 * Removes all updates
	 *
	 * @internal
	 *
	 * @param int $currentTick
	 *
	 * @return bool
	 */
	public function onUpdate(int $currentTick): bool {
		return true;
	}
}