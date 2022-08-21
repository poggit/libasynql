<?php

declare(strict_types=1);

namespace Poggit\ChatCount;

use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\plugin\PluginBase;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;
use poggit\libasynql\SqlError;

final class Main extends PluginBase implements Listener {
	private DataConnector $db;

	protected function onEnable(): void {
		$this->saveDefaultConfig();
		$this->db = libasynql::create($this, $this->getConfig()->get("database"), [
			"sqlite" => "sqlite.sql",
			"mysql" => "mysql.sql",
		]);
		$this->db->executeGeneric("init");
		$this->db->waitAll();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onChat(PlayerChatEvent $event) : void {
		$this->db->executeGeneric("add", ["player" => $event->getPlayer()->getName()], null,
			fn(SqlError $err) => $this->getLogger()->error($err->getMessage()));
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
		if(isset($args[0])) {
			$name = $args[0];
			$this->db->executeSelect("count", ["player" => $name], function(array $rows) use($sender, $name) {
				if(count($rows) === 0) {
					$sender->sendMessage("$name has never chatted before");
				} else {
					$count = $rows[0]["count"];
					$sender->sendMessage("$name has sent $count messages");
				}
			}, fn(SqlError $err) => $this->getLogger()->error($err->getMessage()));
		} else {
			$this->db->executeSelect("top", [
				"limit" => $this->getConfig()->get("limit"),
			], function(array $rows) use($sender) {
				foreach($rows as $rank => $row) {
					$sender->sendMessage(sprintf("#%d: %s (%d messages)", $rank + 1, $row["player"], $row["count"]));
				}
			}, fn(SqlError $err) => $this->getLogger()->error($err->getMessage()));
		}

		return true;
	}
}
