<?php
/*
 * Copyright zMxZero/Leqends
 */
declare(strict_types=1);
namespace HeliosTeam\PAM;

use JetBrains\PhpStorm\Pure;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;

class Manager extends PluginBase implements Listener
{

    public Config $setworlds;

    public function onEnable() : void
    {
        @mkdir($this->getDataFolder());
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->setworlds = new Config($this->getDataFolder() . "setworlds.yml", Config::YAML);
        $this->getServer()->getCommandMap()->getCommand("attackdelay");
        $this->getServer()->getCommandMap()->getCommand("knockback");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {

        switch (strtolower($command->getName())) {
            case "attackdelay":
                if($sender instanceof Player) {
                    if(!$sender->hasPermission("attackdelay.cmd")) {
                        $sender->sendMessage("§cYou do not have permission");
                        return true;
                    }

                    if(!isset($args[0])) {
                        $sender->sendMessage("Usage: §b/attackdelay §c{world} §c{cooldown}");
                        return true;
                    }

                    if($this->worldChecker($args[0]) == true) {
                        if(!isset($args[1])) {
                            $sender->sendMessage("§cPlease add a cooldown\nUsage: §b/attackdelay §c{world} §c{cooldown}");
                            return true;
                        } elseif (is_numeric($args[1])) {
                            $sender->sendMessage("§bAttack delay for §f" . $args[0] . " §bhas been set to §f" . $args[1]);
                            $this->getSetWorlds()->setNested("$args[0].attackdelay", intval($args[1]));
                            $this->getSetWorlds()->save();
                            return true;
                        } else {
                            $sender->sendMessage("§cValue must be numeric\nUsage: §b/attackdelay §c{world} §c{cooldown}");
                            return true;
                        }
                    } else {
                        $sender->sendMessage("§cWorld does not exist, please enter the folder name of the world");
                        return true;
                    }
                }
                break;
            case "knockback":
                if($sender instanceof Player) {
                    if(!$sender->hasPermission("knockback.cmd")) {
                        $sender->sendMessage("§cYou do not have permission");
                        return true;
                    }

                    if(!isset($args[0])) {
                        $sender->sendMessage("Usage: §b/knockback §c{world} §c{value}");
                        return true;
                    }

                    if($this->worldChecker($args[0]) == true) {
                        if(!isset($args[1])) {
                            $sender->sendMessage("§cPlease add a value\nUsage: §b/knockback §c{world} §c{value}");
                            return true;
                        } elseif (is_numeric($args[1])) {
                            $sender->sendMessage("§bKnockback for §f" . $args[0] . " §bhas been set to §f" . $args[1]);
                            $this->getSetWorlds()->setNested("$args[0].knockback",  floatval($args[1]));
                            $this->getSetWorlds()->save();
                            return true;
                        } else {
                            $sender->sendMessage("§cValue must be numeric\nUsage: §b/knockback §c{world} §c{value}");
                            return true;
                        }
                    } else {
                        $sender->sendMessage("§cWorld does not exist, please enter the folder name of the world");
                        return true;
                    }
                }
                break;
        }
        return true;
    }

    public function EntityDamageEvent(EntityDamageEvent $event)
    {
        if($event->getEntity() instanceof Player && $event instanceof EntityDamageByEntityEvent) {
            $player = $event->getEntity();
            $worldName = $player->getWorld()->getFolderName();
            foreach ($this->getServer()->getWorldManager()->getWorlds() as $level) {
                if($player->getWorld() === $level) {
                    $event->setKnockback($this->getSetWorlds()->getAll()[$worldName]["knockback"]);
                    $event->setAttackCooldown($this->getSetWorlds()->getAll()[$worldName]["attackdelay"]);
                }
            }
        }
    }

    #[Pure] public function worldChecker(string $arg): bool
    {
        $rv = false;
        foreach ($this->getServer()->getWorldManager()->getWorlds() as $world) {
            if($world->getFolderName() === $arg) {
                $rv = true;
            }
            break;
        }
        return $rv;
    }

    public function getSetWorlds(): Config
    {
        return $this->setworlds;
    }
}
