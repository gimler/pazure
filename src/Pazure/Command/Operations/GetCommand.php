<?php

/*
 * This file is part of Pazure.
 *
 * (c) Gordon Franke <info@nevalon.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pazure\Command\Operations;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Cilex\Command\Command;

use Guzzle\Http\Exception\ClientErrorResponseException;

use Exception;

/**
 * The Get Operation Status operation returns the status of the specified operation. After calling an asynchronous operation, you can call Get Operation Status to determine whether the operation has succeeded, failed, or is still in progress.
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class GetCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('operations:get')
            ->setDescription('Get the status of the specified operation')
            ->setDefinition(array(
                new InputArgument(
                    'request_id', InputArgument::REQUIRED
                )
            ));
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int|null|void
     * @throws \Guzzle\Http\Exception\ClientErrorResponseException
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $requestId = $input->getArgument('request_id');

        $command = $this->getService('guzzle')
            ->get('azure')
            ->getCommand('operations.get', array('request_id' => $requestId));

        try {
            $operation = $command->execute();
        } catch (ClientErrorResponseException $e) {
            if (400 === $e->getResponse()->getStatusCode()) {
                throw new Exception(sprintf('Invalid operation request id `%s`', $requestId));
            }

            throw $e;
        }

        $output->writeln(sprintf('<comment>Id: %s</comment>', $operation->ID));

        if (isset($operation->Error)) {
            $output->writeln('Error:');
            $output->writeln(sprintf('  Code: %s', $operation->Error->Code));
            $output->writeln(sprintf('  Message: %s', $operation->Error->Message));
        } else {
            $output->writeln(sprintf('Status: %s', $operation->Status));
        }
    }
}
