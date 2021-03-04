<?php

namespace HimmelKreis4865\BetterSkulls\utils;

use HimmelKreis4865\BetterSkulls\BetterSkulls;
use pocketmine\utils\Config;

class PlayerConfigManager {
	use InstantiableTrait;
	
	/** @var null | Config $config */
	protected $config = null;
	
	public function __construct() {
		$this->config = new Config(BetterSkulls::getInstance()->getDataFolder() . "players.yml", Config::YAML);
	}
	
	/**
	 * Returns the cooldown in seconds
	 *
	 * @api
	 *
	 * @param string $player
	 *
	 * @return int
	 */
	public function getCooldown(string $player): int {
		return $this->config->get($player, (time() - 1));
	}
	
	/**
	 * Changes the cooldown to another value, does not add it to the existing one
	 *
	 * @api
	 *
	 * @param string $player
	 * @param int $cooldown
	 *
	 * @return void
	 */
	public function setCooldown(string $player, int $cooldown): void {
		$this->config->set($player, $cooldown);
		$this->config->save();
		$this->config->reload();
	}
}