<?php

declare(strict_types=1);

namespace DavidFlash\LobbyCore;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\entity\Effect;
use pocketmine\entity\EffectInstance;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\SchedulerTask;
use pocketmine\event\Listener;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use DavidFlash\FlashItem\libs\jojoe77777\FormAPI\SimpleForm;

class Main extends PluginBase implements Listener {

	public function onEnable() {
		$this->getLogger()->info("Plugin Loaded!");
		if(!is_file($this->getDataFolder() . "config.yml")){ //if the config is not in plugin_data/FlashCore
			$this->saveResource('/config.yml'); //save the resource from "resources".
		}
		//if "world" is empty
		if(empty($this->getConfig()->get("world"))){ //if "world" value is empty.
			$this->getLogger()->warning("The 'world' value in config cannot be empty.");
		}
		//if "message" is empty
		if(empty($this->getConfig()->get("message"))){ //if "world" value is empty.
			$this->getLogger()->warning("The 'message' value in config cannot be empty.");
		}
		//if "title" is empty
		if(empty($this->getConfig()->get("title"))){ //if "title" value is empty.
			$this->getLogger()->warning("The 'title' value in config cannot be empty.");
		}
		//if "subtitle" is empty
		if(empty($this->getConfig()->get("subtitle"))){ //if "subtitle" value is empty.
			$this->getLogger()->warning("The 'subtitle' value in config cannot be empty.");
		}
		//if "gamemode" is empty
		if(empty($this->getConfig()->get("gamemode"))){ //if "gamemode" value is empty.
			$this->getLogger()->warning("The 'gamemode' value in config cannot be empty.");
		}
		//if "gamemode" is not numeric
		if(is_nan($this->getConfig()->get("gamemode"))){ //if "gamemode" value is not numeric.
			$this->getLogger()->warning("The 'gamemode' value need to be a number.");
		}
	if(!$this->getServer()->isLevelGenerated($this->getConfig()->get("world"))){
		$this->getLogger()->warning("NOTICE! Level is not generated");
		}
		if(!$this->getServer()->isLevelLoaded($this->getConfig()->get("world"))){
		$this->getServer()->loadLevel($this->getConfig()->get("world"));
		}
	}
	
	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {
		if($command->getName() == "infoui") {
			if(!$sender instanceof Player){
				$sender->sendMessage("§cThis works only in-game");
				return true;
			}
			if($this->getConfig()->get("enabled") === false){
				$sender->sendMessage("§cThis feature is disabled");
				return true;
			}
			$form = new SimpleForm(function (Player $sender,  $data) {
				switch($data) {
					case 0:
						break;
				}
			});
			$form->setTitle("Server Info");
			$form->setContent($this->getConfig()->get("text"));
			$form->addButton("OK");
			$form->sendToPlayer($sender);
		}

		if($command->getName() == "hub") {
			if(!$sender instanceof Player){
				$sender->sendMessage("§cThis works only in-game");
				return true;
			}
			$sender->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), 20, 2, false));
			$sender->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 20, 2, false));
			$sender->addTitle($this->getConfig()->get("title"));
			$sender->addSubTitle($this->getConfig()->get("subtitle"));
			$sender->setImmobile(true);
			$this->getScheduler()->scheduleDelayedTask(new class($this, $sender) extends Task{
				protected $main;
				public $player;

				public function __construct(Main $main, CommandSender $player){ //constructor
					$this->main = $main;
					$this->player = $player;
					
				}
			
				public function onRun(int $currentTick){ //when is running after 2 secs.
					
						$navigate = ItemFactory::get($this->main->getConfig()->get("item"), 0, 1);
						$navigate->setCustomName($this->main->getConfig()->get("name"));
					
					$this->player->sendMessage($this->main->getConfig()->get("message")); //u got msg?
					$this->player->teleport($this->main->getServer()->getLevelByName($this->main->getConfig()->get("world"))->getSafeSpawn());
					$this->player->setGamemode($this->main->getConfig()->get("gamemode"));
					$this->player->setImmobile(false);
					$this->player->addTitle("§f");
					$this->player->addSubTitle("§f");

					if($this->main->getConfig()->get("enabled-item") === true){
						$this->player->getInventory()->addItem($navigate);
					}
	
				}

			},
			40 //time
			);

		return true;
	}if($command->getName() == "lobby") {
		if(!$sender instanceof Player){
			$sender->sendMessage("§cThis works only in-game");
			return true;
		}
			$sender->addEffect(new EffectInstance(Effect::getEffect(Effect::SLOWNESS), 20, 2, false));
			$sender->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 20, 2, false));
			$sender->addTitle($this->getConfig()->get("title"));
			$sender->addSubTitle($this->getConfig()->get("subtitle"));
			$sender->setImmobile(true);
			$this->getScheduler()->scheduleDelayedTask(new class($this, $sender) extends Task{ //schedule a delayed task.
				protected $main;
				public $player;

				public function __construct(Main $main, CommandSender $player){ //constructor
					$this->main = $main;
					$this->player = $player;
				}

				public function onRun(int $currentTick){ //when is running after 2 secs.
					
						$navigate = ItemFactory::get($this->main->getConfig()->get("item"), 0, 1);
						$navigate->setCustomName($this->main->getConfig()->get("name"));
					
					$this->player->getInventory()->removeItem($navigate);
					$this->player->sendMessage($this->main->getConfig()->get("message")); //u got msg?
					$this->player->teleport($this->main->getServer()->getLevelByName($this->main->getConfig()->get("world"))->getSafeSpawn());
					$this->player->setGamemode($this->main->getConfig()->get("gamemode"));
					$this->player->setImmobile(false);
					
					if($this->main->getConfig()->get("enabled-item") === true){
						$this->player->getInventory()->addItem($navigate);
					}
				}

			},
			40 //time
			);

		return true;
	}if($command->getName() == "flashcore") {
			$sender->sendMessage("§l§gFlash§fCore §r§f1.0.5 by David Flash");
			$sender->sendMessage("§fA Highly Customizable LobbyCore Plugin.");
			$sender->sendMessage("");
			$sender->sendMessage("§fEverything can be edited in config.yml that can be found");
			$sender->sendMessage("§fin FlashCore folder in plugin_data or plugins.");
			$sender->sendMessage("");
			$sender->sendMessage("§fThank you for using §gFlash§fCore plugin!");


		return true;
	
	}if($command->getName() == "freeze"){
		if(!$sender instanceof Player){
			$sender->sendMessage("§cThis works only in-game");
			return true;
		}
		if(!$sender->hasPermission('fc.freeze')){
			$sender->sendMessage('§cYou don\' have permission to use this command');
			return true;
		}
		if(!isset($args[0])){
			if($sender->IsImmobile() === true){
				$sender->setImmobile(false);
				$sender->removeAllEffects();
				$sender->sendMessage("§l§f[§gFlash§fCore] §aYou are now unfrozen!");
				return true;
			}
			$sender->setImmobile(true);
			$sender->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 9999999, 5, false));
			$sender->addTitle("§cYOU ARE NOW FROZEN", "§7Seems like you're not kind!");
			$sender->sendMessage("§l§f[§gFlash§fCore] §cYou are now frozen!");
			return true;
		}else{
			$player = $this->getServer()->getPlayer($args[0]);
			if($player != null){
				if($player->IsImmobile() === true){
					$player->setImmobile(false);
					$player->removeAllEffects();
					$player->addTitle("§aYOU ARE NOW UNFROZEN", "§7Good boy");
					$player->sendMessage("§l§f[§gFlash§fCore] §aYou are now unfrozen!!");
					$sender->sendMessage("§l§f[§gFlash§fCore] §a" . $player->getName() . " is now frozen!");
					return true;
				}
				$player->setImmobile(true);
				$player->addEffect(new EffectInstance(Effect::getEffect(Effect::BLINDNESS), 9999999, 5, false));
				$player->addTitle("§cYOU ARE NOW FROZEN", "§7Seems like you're not kind!");
				$player->sendMessage("§l§f[§gFlash§fCore] §cYou are now frozen!");
				$sender->sendMessage("§l§f[§gFlash§fCore] §c" . $player->getName() . " has been frozen!");
				return true;
			}else{
				$sender->sendMessage('§l§f[§gFlash§fCore] §cPlayer not found.');
				return true;
			}
		return true;
		}
	
	}if($command->getName() == "haf"){
		if(!$sender instanceof Player){
			$sender->sendMessage("§cThis works only in-game");
			return true;
		}
		if(!$sender->hasPermission('fc.haf')){
			$sender->sendMessage('§cYou don\' have permission to use this command');
			return true;
		}
		if(!isset($args[0])){
			$sender->setHealth($sender->getMaxHealth());
			$sender->setFood(20);
			$sender->sendMessage("§l§f[§gFlash§fCore] §aYou are now fully restored!");
			return true;
		}else{
			$player = $this->getServer()->getPlayer($args[0]);
			if($player != null){
				$player->setHealth($sender->getMaxHealth());
				$player->setFood(20);
				$player->sendMessage("§l§f[§gFlash§fCore] §aYou are now fully restored!");
				$sender->sendMessage("§l§f[§gFlash§fCore] §a" . $player->getName() . " is now fully restored!");
					return true;
			}else{
				$sender->sendMessage('§l§f[§gFlash§fCore] §cPlayer not found.');
				return true;
			}
		return true;
		}
	
	}if($command->getName() == "vanish"){
		if(!$sender instanceof Player){
			$sender->sendMessage("§cThis works only in-game");
			return true;
		}
		if(!$sender->hasPermission('fc.vanish')){
			$sender->sendMessage('§cYou don\' have permission to use this command');
			return true;
		}
		if(!isset($args[0])){
			if($sender->getAllowFlight() === true){
				$sender->setAllowFlight(false);
				$sender->setFlying(false);
				$sender->removeAllEffects();
				$sender->sendMessage("§l§f[§gFlash§fCore] §cYour VANISH is now disabled.");
				return true;
			}
			$sender->addEffect(new EffectInstance(Effect::getEffect(Effect::INVISIBILITY), 9999999, 1, false));
			$sender->addEffect(new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION), 9999999, 5, false));
			$sender->addTitle("§aYOU ARE NOW VANISHED", "To other players");
			$sender->setAllowFlight(true);
			$sender->setFlying(true);
			$sender->sendMessage("§l§f[§gFlash§fCore] §aYour VANISH is now enabled.");
			$sender->addActionBarMessage("§aYou Are Invisible To Other Players!");
			return true;
		}else{
			$player = $this->getServer()->getPlayer($args[0]);
			if($player != null){
				if($player->getAllowFlight() === true){
					$player->removeAllEffects();
					$player->setAllowFlight(false);
					$player->setFlying(false);
					$player->sendMessage("§l§f[§gFlash§fCore] §cYour VANISH is now disabled!");
					$sender->sendMessage("§l§f[§gFlash§fCore] §c" . $player->getName() . "'s VANISH is now disabled.");
					return true;
				}
				$player->addEffect(new EffectInstance(Effect::getEffect(Effect::INVISIBILITY), 9999999, 1, false));
				$player->addEffect(new EffectInstance(Effect::getEffect(Effect::NIGHT_VISION), 9999999, 5, false));
				$player->addTitle("§aYOU ARE NOW VANISHED");
				$player->addSubTitle("To Other Players");
				$player->addActionBarMessage("§aYou Are Invisible To Other Players!");
				$player->setAllowFlight(true);
				$player->setFlying(true);
				$player->sendMessage("§l§f[§gFlash§fCore] §aYour VANISH is now enabled.");
				$sender->sendMessage("§l§f[§gFlash§fCore] §a" . $player->getName() . "'s VANISH is now enabled.");
				return true;
			}else{
				$sender->sendMessage('§l§f[§gFlash§fCore] §cPlayer not found.');
				return true;
			}
		return true;
		}

	}if($command->getName() == "fly"){
		if(!$sender instanceof Player){
			$sender->sendMessage("§cThis works only in-game");
			return true;
		}
		if(!$sender->hasPermission('fc.fly')){
			$sender->sendMessage('§cYou don\' have permission to use this command');
			
			return true;
		}
		if(!isset($args[0])){
			if($sender->getAllowFlight() === true){
				$sender->setAllowFlight(false);
				$sender->setFlying(false);
				$sender->sendMessage("§l§f[§gFlash§fCore] §cYour fly is now disabled.");
				return true;
			}
			$sender->setAllowFlight(true);
			$sender->setFlying(true);
			$sender->sendMessage("§l§f[§gFlash§fCore] §aYour fly is now enabled.");
			return true;
		}else{
			$player = $this->getServer()->getPlayer($args[0]);
			if($player != null){
				if($player->getAllowFlight() === true){
					$player->setAllowFlight(false);
					$player->setFlying(false);
					$player->sendMessage("§l§f[§gFlash§fCore] §cYour fly is now disabled.");
					$sender->sendMessage("§l§f[§gFlash§fCore] §c" . $player->getName() . "'s fly is now disabled.");
					return true;
				}
				$player->setAllowFlight(true);
				$player->setFlying(true);
				$player->sendMessage("§l§f[§gFlash§fCore] §aYour fly is now enabled.");
				$sender->sendMessage("§l§f[§gFlash§fCore] §a" . $player->getName() . "'s fly is now enabled.");
				return true;
			}else{
				$sender->sendMessage('§l§f[§gFlash§fCore] §cPlayer not found.');
				return true;
			}
		}
		return true;
	}
	return true;
  }
}