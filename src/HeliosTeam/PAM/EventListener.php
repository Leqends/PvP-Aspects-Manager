<?php

namespace HeliosTeam\PAM;

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

//Pmmp imports
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;

class EventListener implements Listener
{
    private $plugin;

    public function __construct(Manager $plugin) {
        $this->plugin = $plugin;
    }

    public function EntityDamageEvent(EntityDamageEvent $event)
    {
        if($event->getEntity() instanceof Player && $event instanceof EntityDamageByEntityEvent) {
            $player = $event->getEntity();
            $worldName = $player->getWorld()->getFolderName();
            foreach ($this->plugin->getServer()->getWorldManager()->getWorlds() as $world) {
                if($player->getWorld() === $world) {
                    if(is_null(Manager::getSetWorlds()->getNested("$worldName.knockback"))) {
                        $event->setKnockBack(1);
                    } else {
                        $event->setKnockBack(Manager::getSetWorlds()->getNested("$worldName.knockback"));
                    }

                    if(is_null(Manager::getSetWorlds()->getNested("$worldName.attackdelay"))) {
                        $event->setAttackCooldown(1);
                    } else {
                        $event->setAttackCooldown(Manager::getSetWorlds()->getNested("$worldName.attackdelay"));
                    }
                }
            }
        }
    }
}