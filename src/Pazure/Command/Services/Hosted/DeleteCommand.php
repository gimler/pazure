<?php

/*
 * This file is part of Pazure.
 *
 * (c) Gordon Franke <info@nevalon.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pazure\Command\Services\Hosted;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Cilex\Command\Command;

use Guzzle\Http\Exception\ClientErrorResponseException;

use Exception;

/**
 * The Delete Hosted Service operation deletes the specified hosted service from Windows Azure.
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class DeleteCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('services:hosted:delete')
            ->setDescription('Delete Hosted Account')
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
            ->getCommand('services.hosted.delete', array('service_name' => $serviceName));

        try {
            $result = $command->execute();
        } catch (ClientErrorResponseException $e) {
            if (404 === $e->getResponse()->getStatusCode()) {
                throw new Exception(sprintf('Invalid hosted account `%s`', $serviceName));
            }

            throw $e;
        }

        $output->writeln(sprintf('<info>Successfully delete hosted account `%s`</info>', $serviceName));
    }
}
