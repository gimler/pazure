<?php

/*
 * This file is part of Pazure.
 *
 * (c) Gordon Franke <info@nevalon.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pazure\Command\Services\Storage;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Cilex\Command\Command;

/**
 * The Get Storage Keys operation returns the primary and secondary access keys for the specified storage account.
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class KeysCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('services:storage:keys')
            ->setDescription('List storage service account keys')
            ->setDefinition(array(
                new InputArgument(
                    'service_name', InputArgument::REQUIRED
                )
            ));
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceName = $input->getArgument('service_name');

        $command = $this->getService('guzzle')
            ->get('azure')
            ->getCommand('services.storage.keys', array('service_name' => $serviceName));
        $storageAccountKeys = $command->execute();

        $output->writeln(sprintf('<comment>Service name: %s</comment>', $serviceName));
        $output->writeln(sprintf('Url: %s', $storageAccountKeys->Url));
        $output->writeln(sprintf('Primary: %s', $storageAccountKeys->StorageServiceKeys->Primary));
        $output->writeln(sprintf('Secondary: %s', $storageAccountKeys->StorageServiceKeys->Secondary));
    }
}
