<?php

namespace HimmelKreis4865\BetterSkulls\utils;

use HimmelKreis4865\BetterSkulls\BetterSkulls;
use pocketmine\utils\Config;
use ReflectionClass;

/**
 * Class ConfigManager
 * @package HimmelKreis4865\BetterSkulls\utils
 */
class ConfigManager {
	use InstantiableTrait;
	
	/** @var null | Config $config */
	protected $config = null;
	
	/** @var array $messages */
	public $messages = [];
	
	/** @var float|int $cooldown */
	public $cooldown = 26 * 60 * 60;
	
	/** @var string $format */
	public $format = "§6{player}§7's Skull";
	
	/** @var string[] $blacklist */
	public $blacklist = [];
	
	/**
	 * ConfigManager constructor.
	 */
	public function __construct() {
		$this->config = new Config(BetterSkulls::getInstance()->getDataFolder() . "config.yml", Config::YAML);
		$this->init();
	}
	
	/**
	 * Initializes important config values
	 *
	 * @internal
	 */
	public function init() {
		$ref = new ReflectionClass($this);
		foreach ($this->config->getAll() as $key => $value) {
			if ($ref->hasProperty($key) and !$ref->getProperty($key)->isStatic()) $this->{$key} = $value;
		}
	}
}