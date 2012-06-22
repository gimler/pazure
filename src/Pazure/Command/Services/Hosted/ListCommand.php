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

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Cilex\Command\Command;

/**
 * The List Hosted Services operation lists the hosted services available under the current subscription. 
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class ListCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('services:hosted:list')
            ->setDescription('List hosted services');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getService('guzzle')
            ->get('azure')
            ->getCommand('services.hosted.list');
        $hostedAccounts = $command->execute();

        if (empty($hostedAccounts)) {
            $output->writeln('<error>No hosted accounts found.</error>');
        } else {

        foreach ($hostedAccounts as $hostedAccount) {
            $output->writeln(sprintf('<comment>Service name: %s</comment>', $hostedAccount->ServiceName));
            $output->writeln(sprintf('Url: %s', $hostedAccount->Url));

            $output->writeln('Properties:');
            $properties = array(
                'Description'      => 'Description',
                'AffinityGroup'    => 'Affinity group',
                'Location'         => 'Location',
                'Label'            => 'Label',
                'Status'           => 'Status',
                'DateCreated'      => 'Created',
                'DateLastModified' => 'Last modified'
            );
            foreach ($properties as $key => $text) {
                if (isset($hostedAccount->HostedServiceProperties->$key)) {
                    $output->writeln(sprintf('  %s: %s', $text, $hostedAccount->HostedServiceProperties->$key));
                }
            }

            $output->writeln('');
        }
        }
    }
}
