<?php

namespace HeliosTeam\PAM\Commands;

/*
 * Copyright © 2022 KingRainbow44, Eerie6560, zMxZero/Leqends.
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
use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;

//Pocketmine stuff
use pocketmine\command\CommandSender;
use pocketmine\Server;

class AttackdelayCMD extends BaseCommand {

    protected function prepare(): void {
        try {
            $this->registerArgument(0, new RawStringArgument("world", false));
            $this->registerArgument(1, new IntegerArgument("cooldown", false));
        } catch (\Exception $exception){
            Server::getInstance()->getLogger()->debug("Unable to register argument: {$exception->getMessage()}");
        }

    }

    /**
     * @throws JsonException
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $config = Manager::getSetWorlds();
        $world = $args["world"];

        if(!$sender->hasPermission("attackdelay.cmd")) {
            $sender->sendMessage(self::getPermissionMessage());
        }

        if(Manager::worldChecker($args["world"]) == true) {
            if(is_numeric($args["cooldown"])) {
                $sender->sendMessage("§bAttack delay for §f" . $args["world"] . " §bhas been set to §f" . $args["cooldown"]);
                $config->setNested("$world.attackdelay", intval($args["cooldown"]));
                $config->save();
            }
        } else {
            $sender->sendMessage("§cWorld does not exist, please enter the folder name of the world");
        }
    }
}