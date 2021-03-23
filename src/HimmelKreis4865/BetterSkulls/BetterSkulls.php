<?php

namespace HimmelKreis4865\BetterSkulls;

use HimmelKreis4865\BetterSkulls\utils\ConfigManager;
use HimmelKreis4865\BetterSkulls\utils\PlayerConfigManager;
use pocketmine\block\BlockFactory;
use pocketmine\entity\Entity;
use pocketmine\entity\Skin;
use pocketmine\item\Item;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use function base64_encode;
use function is_string;
use function str_replace;
use function str_split;
use function strlen;

class BetterSkulls extends PluginBase {
	/** @var string the geometry used for creating a 2 layer head model */
	public const GEOMETRY = '{"format_version": "1.12.0", "minecraft:geometry": [{"description": {"identifier": "geometry.skull", "texture_width": 64, "texture_height": 64, "visible_bounds_width": 2, "visible_bounds_height": 4, "visible_bounds_offset": [0, 0, 0]}, "bones": [{"name": "Head", "pivot": [0, 24, 0], "cubes": [{"origin": [-4, 0, -4], "size": [8, 8, 8], "uv": [0, 0]}, {"origin": [-4, 0, -4], "size": [8, 8, 8], "inflate": 0.5, "uv": [32, 0]}]}]}]}';
	
	/** @var null | static $instance */
	protected static $instance = null;
	
	public function onEnable() {
		self::$instance = $this;
		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
		Entity::registerEntity(SkullEntity::class, true, ["minecraft:skull_entity", "SkullEntity"]);
		BlockFactory::registerBlock(new SkullBlock(), true);
		$this->getConfig();
		$this->getServer()->getCommandMap()->register("BetterSkulls", new BetterSkullCommand());
	}
	
	/**
	 * Returns the instance of the class
	 *
	 * @api
	 *
	 * @return self|null
	 */
	public static function getInstance(): ?BetterSkulls {
		return self::$instance;
	}
	
	/**
	 * Returns the head item for a specific skin & name
	 *
	 * @api
	 *
	 * @param string $name
	 * @param Skin $skin
	 *
	 * @return Item
	 */
	final public static function constructPlayerHeadItem(string $name, Skin $skin): Item {
		$item = Item::get(Item::SKULL, 3);
		$lengths = str_split(base64_encode($skin->getSkinData()), 32767);
		$tag = new CompoundTag("skull", [
			new StringTag("skull_name", $name),
			new StringTag("skull_data", array_shift($lengths))
		]);
		foreach ($lengths as $key => $length) {
			// preventing random errors
			if (strlen($length) === 0) break;
			
			$tag->setString("skull_data_" . ($key + 1), $length);
		}
		$item->setCustomBlockData($tag);
		$item->setCustomName(str_replace("{player}", $name, ConfigManager::getInstance()->format));
		return $item;
	}
	
	/**
	 * Returns whether a specific player is under cooldown or not
	 *
	 * @api
	 *
	 * @param Player|string $player
	 * @return bool
	 */
	public function hasCooldown($player): bool {
		if ($player instanceof Player) $player = $player->getName();
		if (!is_string($player)) return true;
		return (PlayerConfigManager::getInstance()->getCooldown($player) > time());
	}
	
	/**
	 * Refills the cooldown of a player to the new value
	 *
	 * @api
	 *
	 * @param Player|string $player
	 */
	public function refillCooldown($player): void {
		if ($player instanceof Player) $player = $player->getName();
		if (!is_string($player)) return;
		
		PlayerConfigManager::getInstance()->setCooldown($player, time() + intval(ConfigManager::getInstance()->cooldown));
	}

	public function useBlacklist(): bool
    {
        $useBlacklist = $this->getConfig()->get("useBlacklist");
        if ($useBlacklist == "true") {
            return true;
        } else {
            return false;
        }
    }

	public function isSkullBlocked(string $playerName): bool
    {
        $blacklist = $this->getConfig()->get("blacklist");
        $useBlacklist = $this->getConfig()->get("useBlacklist");
        if (!$this->useBlacklist()) {
            return false;
        }
        if ($this->useBlacklist()) {
            if (in_array($playerName, $blacklist)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}