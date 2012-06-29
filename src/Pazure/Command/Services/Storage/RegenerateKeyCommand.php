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

use Exception;
use DomDocument;

/**
 * The Regenerate Keys operation regenerates the primary or secondary access key for the specified storage account.
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class RegenerateKeyCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('services:storage:regenerate-key')
            ->setDescription('Regenerate storage service account key')
            ->setDefinition(array(
                new InputArgument(
                    'service_name', InputArgument::REQUIRED
                ),
                new InputArgument(
                    'key_type', InputArgument::REQUIRED, 'Specifies which key to regenerate `primary` or `secondary`'
                )
            ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceName = $input->getArgument('service_name');

        $domDoc = $this->buildDom($input->getArgument('key_type'));

        $command = $this->getService('guzzle')
            ->get('azure')
            ->getCommand('services.storage.keys.regenerate', array('service_name' => $serviceName, 'data' => $domDoc->saveXML()));
        $storageAccountKeys = $command->execute();

        $output->writeln(sprintf('<comment>Service name: %s</comment>', $serviceName));
        $output->writeln(sprintf('Url: %s', $storageAccountKeys->Url));
        $output->writeln(sprintf('Primary: %s', $storageAccountKeys->StorageServiceKeys->Primary));
        $output->writeln(sprintf('Secondary: %s', $storageAccountKeys->StorageServiceKeys->Secondary));
    }

    protected function buildDom($keyType)
    {
        $domDoc = new DOMDocument();
        $domDoc->loadXML('<?xml version="1.0" encoding="utf-8"?><RegenerateKeys xmlns="http://schemas.microsoft.com/windowsazure"></RegenerateKeys>');

        $nameNode = $domDoc->createElement('KeyType', ucfirst($keyType));
        $domDoc->documentElement->appendChild($nameNode);

        return $domDoc;
    }
}
