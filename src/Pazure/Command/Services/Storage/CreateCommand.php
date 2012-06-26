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
#                    'geo_replication_enabled', 'g', InputOption::VALUE_REQUIRED, 'Specifies whether the storage account is created with the geo-replication enabled.', true
                    'disable_geo_replication', 'g', InputOption::VALUE_NONE, 'Specifies whether the storage account is created with the geo-replication enabled.'
                ),
                new InputOption(
                    'extended_property', 'e', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Extended storage account property pair name and value seperated by `=`.', array()
                ),
            ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceName = $input->getArgument('service_name');

        //TODO: validation check Storage account names must be between 3 and 24 characters in length and use numbers and lower-case letters only.
        //TODO: validation The name may be up to 100 characters in length
        //TODO: validation description the description may be up to 1024 characters in length.
        //TODO: validation You must include either a Location or AffinityGroup element in the request body, but not both.
        //TODO: validation The maximum length of the Name element is 64 characters, only alphanumeric characters and underscores are valid in the Name, and the name must start with a letter. unique name
        //TODO: validation You can have a maximum of 50 extended property name/value pairs.
        //TODO: validation each extended property value has a maximum length of 255 characters

        $storageAccountConfig = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><CreateStorageServiceInput xmlns="http://schemas.microsoft.com/windowsazure"></CreateStorageServiceInput>');
        $storageAccountConfig->addChild('ServiceName', $serviceName);
        if (null !== $description = $input->getOption('description')) {
            $storageAccountConfig->addChild('Description', $description);
        }
        $storageAccountConfig->addChild('Label', base64_encode($input->getArgument('label')));
        if (null !== $affinityGroup = $input->getOption('affinity_group')) {
            $storageAccountConfig->addChild('AffinityGroup', $affinityGroup);
        } else {
            $storageAccountConfig->addChild('Location', $input->getOption('location'));
        }
        $storageAccountConfig->addChild('GeoReplicationEnabled', true === $input->getOption('disable_geo_replication') ? 'false':'true');
        //TODO: add extended properties

        $command = $this->getService('guzzle')
            ->get('azure')
            ->getCommand('services.storage.create', array('data' => $storageAccountConfig->asXML()));

        $command->execute();

        $output->writeln(sprintf('<comment>Create storage service account `%s`</comment>', $serviceName));
        $output->writeln(sprintf('Request id: %s', $command->getResponse()->getHeader('x-ms-request-id')));
    }
}
