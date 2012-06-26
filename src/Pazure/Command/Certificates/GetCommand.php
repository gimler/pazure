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
 * Gets information about the management certificate with the specified thumbprint.
 *
 * @author Gordon Franke <info@nevalon.de>
 */
class GetCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('managment:certificates:get')
            ->setDescription('Get management certificate')
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
            ->getCommand('certificates.get', array('thumbprint' => $thumbprint));

        try {
            $certificate = $command->execute();
        } catch (ClientErrorResponseException $e) {
            if (404 === $e->getResponse()->getStatusCode()) {
                throw new Exception(sprintf('Invalid certificate thumbprint `%s`', $thumbprint));
            }

            throw $e;
        }

        //TODO: move to method share with list command
        $certDer = base64_decode((string) $certificate->SubscriptionCertificateData);
        $certPem = $this->der2pem($certDer);
        $certData = openssl_x509_parse($certPem);

        //TODO: seperate each entry free line, and bold headline?
        //TODO: add valid info in green color
        $output->writeln(sprintf('<comment>Id: %s</comment>', $certificate->SubscriptionCertificateThumbprint));
        $output->writeln(sprintf('Created: %s', $certificate->Created));

        $properties = array(
            'C'  => 'Country Name',
            'ST' => 'State or Province Name',
            'L'  => 'Locality Name',
            'O'  => 'Organization Name',
            'OU' => 'Organizational Unit Name',
            'CN' => 'Common Name',
            'emailAddress' => 'E-Mail-Address'
        );
        foreach ($properties as $key => $text) {
            if (isset($certData['subject'][$key])) {
                $output->writeln(sprintf('%s: %s', $text, $certData['subject'][$key]));
            }
        }
    }

    //TODO: move to global helper service
    public function der2pem($der_data)
    {
        $pem = chunk_split(base64_encode($der_data), 64, "\n");
        $pem = "-----BEGIN CERTIFICATE-----\n".$pem."-----END CERTIFICATE-----\n";

        return $pem;
    }
}
