<?php

/*
 * This file is part of Pazure.
 *
 * (c) Gordon Franke <info@nevalon.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pazure\Command\Certificates;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Cilex\Command\Command;

use Guzzle\Http\Exception\ClientErrorResponseException;

use Exception;

/**
 * Deletes a certificate from the list of management certificates.
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class DeleteCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('managment:certificates:delete')
            ->setDescription('Delete management certificate')
            ->setDefinition(array(
                new InputArgument(
                    'thumbprint', InputArgument::REQUIRED
                )
            ));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $thumbprint = $input->getArgument('thumbprint');

        $command = $this->getService('guzzle')
            ->get('azure')
            ->getCommand('certificates.delete', array('thumbprint' => $thumbprint));

        try {
            $result = $command->execute();
        } catch (ClientErrorResponseException $e) {
            if (404 === $e->getResponse()->getStatusCode()) {
                throw new Exception(sprintf('Invalid certificate thumbprint `%s`', $thumbprint));
            }

            throw $e;
        }

        $output->writeln(sprintf('<comment>Delete managment certificate `%s`</comment>', $thumbprint));
        $output->writeln(sprintf('Request id: %s', $command->getResponse()->getHeader('x-ms-request-id')));
    }
}
