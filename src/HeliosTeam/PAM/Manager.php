//Copyright zMxZero
<?php

namespace HeliosTeam\PAM;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;

class Manager extends PluginBase implements Listener
{

    private $setworlds;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->setworlds = new Config("setworlds.yml", Config::YAML);
        $this->getServer()->getCommandMap()->getCommand("attackdelay")->setPermission("attackdelay.cmd");
        $this->getServer()->getCommandMap()->getCommand("knockback")->setPermission("knockback.cmd");
        $this->getServer()->getCommandMap()->getCommand("pam")->setPermission("pam.cmd");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ($command->getName()) {
            case "attackdelay":
                if (!$sender->hasPermission("attackdelay.cmd")) {
                    $sender->sendMessage("§cYou do not have permission to use this command");
                }

                if (!isset($args[0])) {
                    $sender->sendMessage("Usage: §b/attackdelay §c{world} §c{cooldown}");
                } else {
                    foreach ($this->getServer()->getLevels() as $level) {
                        if($args[0] != $level->getFolderName()) {
                            $sender->sendMessage("§cThe §f".$args[1]." §cdoes not exist");
                        } else {
                            $sender->sendMessage("§cYou have entered the correct world: §f". $args[0]. " §cbut you are missing the cooldown\nUsage: /§battackdelay " .$args[0]. "§c{cooldown} ");
                        }
                    }
                }
                if (!isset($args[1])) {
                    $sender->sendMessage("Please define a cooldown\nUsage: §b/attackdelay " . $args[0] . " §c{cooldown}");
                } elseif (!is_numeric($args[1])) {
                    $sender->sendMessage("§c" .$args[1] . " §bis not a number");
                } elseif (is_numeric($args[1])) {
                    $sender->sendMessage("§bAttack delay for §f" . $args[0] . " §bhas been set to §f" . $args[1]);
                    $this->getSetWorlds()->set($args[0], $args[1]);
                }
                break;
            case "knockback":
                if (!$sender->hasPermission("knockback.cmd")) {
                    $sender->sendMessage("§cYou do not have permission to use this command");
                }

                if (!isset($args[0])) {
                    $sender->sendMessage("Usage: §b/knockback §c{world} §c{value}");
                } else {
                    foreach ($this->getServer()->getLevels() as $level) {
                        if($args[0] != $level->getFolderName()) {
                            $sender->sendMessage("§cThe §f".$args[1]." §cdoes not exist");
                        } else {
                            $sender->sendMessage("§cYou have entered the correct world: §f". $args[0]. " §cbut you are missing the value\nUsage: /§bknockback " .$args[0]. "§c{value} ");
                        }
                    }
                }
                if (!isset($args[1])) {
                    $sender->sendMessage("Please define a value\nUsage: §b/knockback " . $args[0] . " §c{value}");
                } elseif (!is_numeric($args[1])) {
                    $sender->sendMessage("§c" .$args[1] . " §bis not a number");
                } elseif (is_numeric($args[1])) {
                    $sender->sendMessage("§bKnockback for §f" . $args[0] . " §bhas been set to §f" . $args[1]);
                    $this->getSetWorlds()->set($args[0], $args[1]);
                }
        }
        return true;
    }

    public function EntityDamageEvent(EntityDamageEvent $event)
    {
        if($event instanceof Player && $event instanceof EntityDamageByEntityEvent) {
            $player = $event->getPlayer();
            foreach ($this->getServer()->getLevels() as $level) {
                if($player->getLevel() === $level) {
                    $event->setAttackCooldown($this->getSetWorlds()->get($player->getLevel()->getFolderName()));
                }
            }
        }
    }

    public function getSetWorlds(): Config
    {
        return $this->setworlds;
    }
}
