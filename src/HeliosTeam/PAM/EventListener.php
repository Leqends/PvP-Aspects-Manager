<?php

namespace HeliosTeam\PAM;

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

//Pmmp imports
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;

class EventListener implements Listener
{
    private $plugin;

    public function __construct(Manager $plugin)
    {
        $this->plugin = $plugin;
    }

    public function EntityDamageEvent(EntityDamageEvent $event): void
    {
        if (!$event->getEntity() instanceof Player || !($event instanceof EntityDamageByEntityEvent)) {
            return;
        }

        $player = $event->getEntity();
        $world = $player->getWorld();
        $worldName = $world->getFolderName();
        $setWorlds = Manager::getSetWorlds();

        $knockback = $setWorlds->getNested("$worldName.knockback");
        $attackDelay = $setWorlds->getNested("$worldName.attackdelay");

        $event->setKnockBack(is_null($knockback) ? 1 : $knockback);
        $event->setAttackCooldown(is_null($attackDelay) ? 1 : $attackDelay);
    }
}