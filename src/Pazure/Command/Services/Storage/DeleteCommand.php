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

use Guzzle\Http\Exception\ClientErrorResponseException;

use Exception;

/**
 * The Delete Storage Account operation deletes the specified storage account from Windows Azure.
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class DeleteCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('services:storage:delete')
            ->setDescription('Delete Storage Account')
            ->setDefinition(array(
                new InputArgument(
                    'service_name', InputArgument::REQUIRED
                )
            ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $serviceName = $input->getArgument('service_name');

        $command = $this->getService('guzzle')
            ->get('azure')
            ->getCommand('services.storage.delete', array('service_name' => $serviceName));

        try {
            $result = $command->execute();
        } catch (ClientErrorResponseException $e) {
            if (404 === $e->getResponse()->getStatusCode()) {
                throw new Exception(sprintf('Invalid storage account `%s`', $serviceName));
            }

            throw $e;
        }

        $output->writeln(sprintf('<info>Successfully delete storage account `%s`</info>', $serviceName));
    }
}
