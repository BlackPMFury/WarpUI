<?php

namespace Github\WarpUI\Task;

use Github\WarpUI\Main;
use pocketmine\scheduler\Task;
use pocketmine\Player;

class CountdownTask extends Task{
	
	public $seconds = 20;
	
	public function __construct(Main $plugin, Player $player){
		$this->player = $player;
		$this->plugin = $plugin;
	}
	
	public function onRun($tick): void{
		$this->player->sendPopup("§aNext Time for Teleport To Warp is §e". $this->seconds);
		if($this->seconds === 0){
			$name = $this->player->getName();
			if(in_array($name, $this->plugin->tasks)){
				unset($this->plugin->tasks[array_search($name, $this->plugin->tasks)]);
				$this->plugin->tasks[$this->player->getId()]->getHandler()->cancel();
				$this->player->sendPopup("§a Bạn có thể Teleport Tới warp khác Lúc này!");
			}
		}
		$this->seconds--;
	}
}