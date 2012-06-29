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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Cilex\Command\Command;

use Pazure\Command\Services\Storage\Model\StorageAccount;
use Pazure\Command\Services\Storage\Model\ExtendedProperty;

/**
 * The Create Storage Account operation creates a new storage account in Windows Azure.
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class CreateCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('services:storage:create')
            ->setDescription('Create storage service account')
            ->setDefinition(array(
                new InputArgument(
                    'service_name', InputArgument::REQUIRED, 'A service name for the storage account that is unique within Windows Azure.'
                ),
                new InputArgument(
                    'label', InputArgument::REQUIRED, 'A name for the storage account.'
                ),
                new InputOption(
                    'affinity_group', 'a', InputOption::VALUE_REQUIRED, 'The name of an existing affinity group in the specified subscription.'
                ),
                new InputOption(
                    'location', 'l', InputOption::VALUE_REQUIRED, 'The location where the storage account is created.'
                ),
                new InputOption(
                    'description', 'd', InputOption::VALUE_REQUIRED, 'A description for the storage account.'
                ),
                new InputOption(
                    'disable_geo_replication', 'g', InputOption::VALUE_NONE, 'Specifies whether the storage account is created with the geo-replication enabled.'
                ),
                new InputOption(
                    'extended_property', 'e', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Extended storage account property pair name and value seperated by `=`.', array()
                ),
            ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $storageAccount = new StorageAccount();
        $storageAccount->setServiceName($input->getArgument('service_name'));
        $storageAccount->setDescription($input->getOption('description'));
        $storageAccount->setLabel($input->getArgument('label'));
        $storageAccount->setAffinityGroup($input->getOption('affinity_group'));
        $storageAccount->setLocation($input->getOption('location'));
        if (true === $input->getOption('disable_geo_replication')) {
            $storageAccount->disableGeoReplication();
        }
        foreach ($input->getOption('extended_property') as $property) {
            list($name, $value) = explode('=', $property);
            $storageAccount->addExtendedProperty(new ExtendedProperty($name, $value));
        }

        $errors = $this->getService('validator')->validate($storageAccount);
        if (count($errors) > 0) {
            throw new \RuntimeException((string) $errors);
        }

        $command = $this->getService('guzzle')
            ->get('azure')
            ->getCommand('services.storage.create', array('data' => (string) $storageAccount));

        $command->execute();

        $output->writeln(sprintf('<comment>Create storage service account `%s`</comment>', $storageAccount->getServiceName()));
        $output->writeln(sprintf('Request id: %s', $command->getResponse()->getHeader('x-ms-request-id')));
    }
}
