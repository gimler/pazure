<?php

/*
 * This file is part of Pazure.
 *
 * (c) Gordon Franke <info@nevalon.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pazure\Command\Locations;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Cilex\Command\Command;

/**
 * The List Locations operation lists all of the data center locations that are valid for your subscription.
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class ListCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('locations:list')
            ->setDescription('List data center locations');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getService('guzzle')
            ->get('azure')
            ->getCommand('locations.list');
        $locations = $command->execute();

        foreach ($locations as $location) {
            $output->writeln(sprintf('<comment>Name: %s</comment>', $location->Name));
            $output->writeln(sprintf('Display name: %s', $location->DisplayName));

            $services = current($location->AvailableServices);
            $output->writeln(sprintf('Services: %s', implode(', ', $services)));

            $output->writeln('');
        }
    }
}
