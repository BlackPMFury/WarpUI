<?php

/** -==| WarpUI |==-
* You can Teleport To Warp With Class-UI
* Good Luck, If have Problem with this Plugin, Please Contact To FB: Thái Thiên Long Or Post Issue in My Plugin's status.
* Defends SimpleWarp or EssentialsTP
*/

namespace Github\WarpUI;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\command\CommandMap;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\Player;
use pocketmine\Server;
use jojoe7777\FormAPI;
use Github\WarpUI\Task\CountdownTask;

class Main extends PluginBase implements Listener{
	public $warps = "§c>>•§aWarpUI§c•<<";
	
	public $task;
	public $tasks = [];
	public $config = [];
	
	public function onEnable(){
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getLogger()->info($this->warps . "§l§a 🐀 .... Enable Plugin .... 🐀");
		$this->warp = new Config($this->getDataFolder() . "Warps.yml", Config::YAML);
		
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		$this->getResource("Config.yml");
	}
	
	public function onJoin(PlayerJoinEvent $ev){
		$player = $ev->getPlayer();
		if($this->warp->exists($player->getName())){
			$this->warp->remove($player->getName());
			$player->sendMessage($this->warps . "§a Your Data at Warps.yml is Deleted!");
		}else{
			$player->getNameTag($player->setNameTag("§aTeleporter"));
			return true;
		}
	}
	
	public function onDisable(){
		$this->warp->removeAll();
		$this->getServer()->getLogger()->warning("Disable Plugin.");
	}
	
	public function onLoad(): void{
		$this->getServer()->getLogger()->info("\n\n§c•§a ༶W༶A༶R༶P༶U༶I༶ \n§c ❤️ §aBy BlackPMFury\n\n");
	}
	
	public function createTask($sender){
		$name = $sender->getName();
		$task = new CountdownTask($this, $sender);
		$this->getScheduler()->scheduleRepeatingTask($task, 20);
		$this->tasks[$sender->getId()] = $task;
		$this->tasks[] = $name;
	}
	
	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
		switch(strtolower($cmd->getName())){
			case "warpui": // First Command
			case "WarpUI": // Second Command
			case "Warpui": // Third Command
			/**
			* Have Three Command Allies as warpui
			* @Params $Event;
			* @return $config;
			*/
			if(!($sender instanceof Player)){
				$this->getLogger()->warning("Use this command in-game!");
				return true;
			}
			$name = $sender->getName();
			if(in_array($name, $this->tasks)){
				return true;
			}
			$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
			$form = $api->createSimpleForm(Function (Player $sender, $data){
				
				$result = $data;
				if ($result == null) {
				}
				switch ($result) {
					case 0:
					$sender->sendMessage("§c");
					break;
					case 1:
					$this->teleportToWarp($sender);
					break;
					case 2:
					$this->adminTools($sender);
					break;
				}
			});
			$form->setTitle($this->getConfig()->get("WarpUI.title"));
			$form->addButton("§cEXIT", 0);
			$form->addButton($this->getConfig()->get("Warps.button"), 1);
			$form->addButton($this->getConfig()->get("AdminTools.button"), 2);
			$form->sendToPlayer($sender);
		}
		return true;
	}
	
	public function teleportToWarp($sender){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createCustomForm(Function (Player $sender, $data){
			if(!(isset($data[1]))){
				$sender->sendMessage($this->warps . " §cPlease Write Full name of Warp!");
				return true;
			}
			Server::getInstance()->dispatchCommand($sender, "warp ". $data[1]);
			$sender->sendMessage($this->warps . "§l§a Teleport to §e".$data[1]."§a Is Success!");
			$this->createTask($sender);
		});
		$form->setTitle($this->getConfig()->get("WarpUI.title"));
		$form->addLabel("§a=> §lWrite Name of Warp to Input!");
		$form->addInput("§eTo:");
		$form->sendToPlayer($sender);
	}
	
	public function adminTools($sender){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createSimpleForm(Function (Player $sender, $data){
			
			$ketqua = $data;
			if ($ketqua == null) {
			}
			switch ($ketqua) {
				case 0:
				$this->addWarp($sender);
				break;
				case 1:
				$this->delWarp($sender);
				break;
				case 2:
				$this->listWarp($sender);
				break;
			}
		});
		
		$form->setTitle($this->getConfig()->get("AdminTools.button"));
		$form->addButton($this->getConfig()->get("addWarp.button"), 0);
		$form->addButton($this->getConfig()->get("delWarp.button"), 1);
		$form->addButton($this->getConfig()->get("listWarp.button"), 2);
		$form->sendToPlayer($sender);
	}
	
	public function addWarp($sender){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createCustomForm(Function (Player $sender, $data){
			if($sender->hasPermission("AdminTools.command.warpui")){
				Server::getInstance()->dispatchCommand($sender, "addwarp ". $data[1]);
				$sender->sendMessage($this->warps . " §aAdded Warp §e".$data[1]."§a!");
			}else{
				$sender->sendMessage($this->warps . " §cYou're Can not Create Warp!");
				return true;
			}
		});
		$form->setTitle($this->getConfig()->get("AddWarp.title"));
		$form->addLabel("§c<=> §aAddWarpUI §c<=>");
		$form->addInput("§aName Of Warp:");
		$form->sendToPlayer($sender);
	}
	
	public function delWarp($sender){
		$api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
		$form = $api->createCustomForm(Function (Player $sender, $data){
			if($sender->hasPermission("AdminTools.command.warpui")){
				Server::getInstance()->dispatchCommand($sender, "delwarp ". $data[1]);
				$sender->sendMessage($this->warps . " §cDeleted Warp §e".$data[1]."§a!");
			}else{
				$sender->sendMessage($this->warps . " §cYou're Can not Delete this Warp!");
				return true;
			}
		});
		$form->setTitle($this->getConfig()->get("delWarp.button"));
		$form->addLabel("§c<=> §aDelWarpUI §c<=>");
		$form->addInput("§aName Of Warp:");
		$form->sendToPlayer($sender);
	}
	
	public function listWarp($sender){
		Server::getInstance()->dispatchCommand($sender, "warp");
	}
}