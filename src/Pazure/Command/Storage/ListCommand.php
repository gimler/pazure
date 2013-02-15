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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Cilex\Command\Command;

/**
 * The List Storage Accounts operation lists the storage accounts available under the current subscription.
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class ListCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('services:storage:list')
            ->setDescription('List storage service accounts');
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getService('guzzle')
            ->get('azure')
            ->getCommand('services.storage.list');
        $storageAccounts = $command->execute();

        if (empty($storageAccounts)) {
            $output->writeln('<error>No storage service accounts found.</error>');
        } else {
            foreach ($storageAccounts as $storageAccount) {
                $output->writeln(sprintf('<comment>Service name: %s</comment>', $storageAccount->ServiceName));
                $output->writeln(sprintf('Url: %s', $storageAccount->Url));

                $output->writeln('Properties:');
                $properties = array(
                    'Description'  => 'Description',
                    'AffinityGroup' => 'Affinity group',
                    'Location'  => 'Location',
                    'Label'  => 'Label',
                    'Status'  => 'Status',
                );
                foreach ($properties as $key => $text) {
                    if (isset($storageAccount->StorageServiceProperties->$key)) {
                        $value = $storageAccount->StorageServiceProperties->$key;
                        if ('Label' == $key) {
                            $value = base64_decode($value);
                        }
                        $output->writeln(sprintf('  %s: %s', $text, $value));
                    }
                }

                $output->writeln('');
            }
        }
    }
}
