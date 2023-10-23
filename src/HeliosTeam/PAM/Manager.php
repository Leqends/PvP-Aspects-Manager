<?php

/*
 * Copyright © 2023 KingRainbow44, Eerie6560, Leqends.
 *
 * Project licensed under the MIT License: https://www.mit.edu/~amini/LICENSE.md
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE
 * OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * All portions of this software are available for public use, provided that
 * credit is given to the original author(s).
 */

declare(strict_types=1);
namespace HeliosTeam\PAM;

//Commando Imports
use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use HeliosTeam\PAM\Commands\AttackdelayCMD;
use HeliosTeam\PAM\Commands\KnockbackCMD;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

//Plugin Imports

//Pmmp imports

class Manager extends PluginBase implements Listener
{

    public static Config $setworlds;
    private static self $instance;

    /**
     * @throws HookAlreadyRegistered
     */
    public function onEnable() : void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        self::$setworlds = new Config($this->getDataFolder() . "setworlds.yml", Config::YAML);
        $this->saveDefaultConfig();
        if(!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
        $this->registerCommands();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }

    public function onLoad(): void
    {
        self::$instance = $this;
    }

    /**
     * Registers the commands
     */
    public function registerCommands(): void
    {
        $this->getServer()->getCommandMap()->register(
            strtolower($this->getName()),
            new AttackdelayCMD($this, "attackdelay", "The attack delay command", ["ad", "adm"])
        );

       $this->getServer()->getCommandMap()->register(
            strtolower($this->getName()),
            new KnockbackCMD($this, "knockback", "The knockback command", ["kb", "kbm"])
        );
    }

    /**
     * @param string $arg
     * @return bool
     */
    public static function worldChecker(string $arg): bool
    {
        $worldManager = self::getInstance()->getServer()->getWorldManager();
        foreach ($worldManager->getWorlds() as $world) {
            if ($world->getFolderName() === $arg) {
                return true;
            }
        }
        return false;
    }

    public static function getInstance(): self
    {
        return self::$instance;
    }

    public static function getSetWorlds(): Config
    {
        return self::$setworlds;
    }
}
