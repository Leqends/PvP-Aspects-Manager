<?php

namespace HeliosTeam\PAM\Commands;

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

//Plugin imports
use HeliosTeam\PAM\Manager;
use JsonException;

//Commando stuff
use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseCommand;

//Pocketmine stuff
use pocketmine\command\CommandSender;
use pocketmine\Server;

class KnockbackCMD extends BaseCommand
{
    protected function prepare(): void
    {
        try {
            $this->registerArgument(0, new RawStringArgument("world", false));
            $this->registerArgument(1, new IntegerArgument("value", false));
            $this->setPermission("knockback.cmd");
            $this->setUsage("§bknockback §c{world} §c{value}");
        } catch (\Exception $exception){
            Server::getInstance()->getLogger()->debug("Unable to register argument: {$exception->getMessage()}");
        }

    }

    /**
     * @throws JsonException
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if(!$sender->hasPermission("knockback.cmd")) {
            $sender->sendMessage(self::getPermissionMessage());
            return;
        }

        $world = $args["world"];
        $value = $args["value"];

        if (!Manager::worldChecker($world)) {
            $sender->sendMessage("§cWorld does not exist, please enter the folder name of the world");
            return;
        }

        if (!is_numeric($value)) {
            $sender->sendMessage("§cCooldown must be a numeric value");
            return;
        }

        $config = Manager::getSetWorlds();
        $config->setNested("$world.knockback", intval($value));
        $config->save();

        $sender->sendMessage("§bKnockback for §f" . $world . " §bhas been set to §f" . $value);
    }
}
