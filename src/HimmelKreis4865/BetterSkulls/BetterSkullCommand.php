<?php

namespace HimmelKreis4865\BetterSkulls;

use DateTime;
use Exception;
use HimmelKreis4865\BetterSkulls\utils\ConfigManager;
use HimmelKreis4865\BetterSkulls\utils\PlayerConfigManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;
use function str_replace;

class BetterSkullCommand extends Command implements PluginIdentifiableCommand {
	
	public function __construct() {
		parent::__construct("skull", "Gives you the possibility to get your and other skulls", "/skull [player: target]", ["head"]);
		$this->setPermission("skull.command");
	}
	
	/**
	 * @param CommandSender $sender
	 * @param string $commandLabel
	 * @param array $args
	 * @return mixed|void
	 * @throws Exception
	 */
	public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
		if (!$sender instanceof Player) return;
		
		if (!$this->testPermissionSilent($sender)) {
			$sender->sendMessage(ConfigManager::getInstance()->messages["no-permission"] ?? "");
			return;
		}
		
		if (BetterSkulls::getInstance()->hasCooldown($sender)) {
			$stamp = new DateTime(date(DateTime::ISO8601, PlayerConfigManager::getInstance()->getCooldown($sender->getName())));
			$interval = $stamp->diff(new DateTime("now"));
			
			$sender->sendMessage(str_replace(["{days}", "{hours}", "{minutes}", "{seconds}"], [$interval->format("%a"), $interval->format("%H"), $interval->format("%I"), $interval->format("%S")], ConfigManager::getInstance()->messages["under-cooldown"] ?? ""));
			return;
		}
		
		if (($target = Server::getInstance()->getPlayer($args[0] ?? $sender->getName())) === null) {
			$sender->sendMessage(str_replace("{name}", $args[0] ?? $sender->getName(), ConfigManager::getInstance()->messages["not-found"] ?? ""));
			return;
		}
		if (!$sender->hasPermission("skull.blacklist.give") && !$sender->hasPermission("skull.blacklist." . $target->getName() . ".give") && BetterSkulls::getInstance()->isSkullBlocked($target->getName())) {
		    $sender->sendMessage(str_replace("{name}", $target->getName(), ConfigManager::getInstance()->messages["skull-blocked"] ?? ""));
		    return;
        }
		if (!$sender->hasPermission("skull.command.bypass")) BetterSkulls::getInstance()->refillCooldown($sender);
		if (BetterSkulls::getInstance()->isSkullBlocked($target->getName())) {
		    if ($sender->hasPermission("skull.blacklist." . $target->getName() . ".give") || $sender->hasPermission("skull.blacklist.give")) {
                $sender->getInventory()->addItem(BetterSkulls::constructPlayerHeadItem($target->getName(), $target->getSkin()));
                $sender->sendMessage(str_replace("{name}", $target->getName(), ConfigManager::getInstance()->messages["success"] ?? ""));
                return;
            } else {
                $sender->sendMessage(str_replace("{name}", $target->getName(), ConfigManager::getInstance()->messages["skull-blocked"] ?? ""));
                return;
            }
        }
		$sender->getInventory()->addItem(BetterSkulls::constructPlayerHeadItem($target->getName(), $target->getSkin()));
		$sender->sendMessage(str_replace("{name}", $target->getName(), ConfigManager::getInstance()->messages["success"] ?? ""));
	}
	
	public function getPlugin(): Plugin {
		return BetterSkulls::getInstance();
	}
}