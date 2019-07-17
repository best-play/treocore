<?php
/**
 * This file is part of EspoCRM and/or TreoCore.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2019 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * TreoCore is EspoCRM-based Open Source application.
 * Copyright (C) 2017-2019 TreoLabs GmbH
 * Website: https://treolabs.com
 *
 * TreoCore as well as EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TreoCore as well as EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word
 * and "TreoCore" word.
 */

declare(strict_types=1);

namespace Treo\Core\Loaders;

use Symfony\Component\Workflow\DefinitionBuilder;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\SupportStrategy\InstanceOfSupportStrategy;
use Symfony\Component\Workflow\Transition;
use Symfony\Component\Workflow\Workflow as Item;
use Treo\Core\Workflow\MarkingStore\MethodMarkingStore;

/**
 * Class Workflow
 *
 * @author r.ratsun@treolabs.com
 */
class Workflow extends Base
{
    /**
     * @inheritDoc
     */
    public function load()
    {
        // create registry
        $registry = new Registry();

        if (!empty($config = $this->getContainer()->get('metadata')->get('workflow', []))) {
            // get entity manager
            $entityManager = $this->getContainer()->get('entityManager');

            // get event manager
            $eventManager = $this->getContainer()->get('eventManager');

            foreach ($config as $name => $data) {
                // parse name
                $parts = explode("_", $name);

                // skip if wring name
                if (count($parts) != 2) {
                    continue 1;
                }

                // prepare definition
                $definitionBuilder = (new DefinitionBuilder())->addPlaces($data['places']);
                foreach ($data['transitions'] as $transition => $row) {
                    $definitionBuilder->addTransition(new Transition($transition, $row['from'], $row['to']));
                }
                $definition = $definitionBuilder->build();

                // add
                $registry->addWorkflow(
                    new Item($definition, new MethodMarkingStore(true, $parts[1]), $eventManager, $name),
                    new InstanceOfSupportStrategy(get_class($entityManager->getEntity($parts[0])))
                );
            }
        }

        return $registry;
    }
}
