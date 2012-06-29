<?php

/*
 * This file is part of Pazure.
 *
 * (c) Gordon Franke <info@nevalon.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pazure\Command\Services\Storage\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

use DOMDocument;

class StorageAccount
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\MinLength(limit=3)
     * @Assert\MaxLength(limit=24)
     * @Assert\Regex(pattern="/^[0-9,a-z]+$/")
     */
    protected $serviceName;

    /**
     * @var string
     *
     * @Assert\MaxLength(1024)
     */
    protected $description;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\MaxLength(100)
     */
    protected $label;

    //TODO: validation require location or affinityGroup not both
    /**
     * @var string
     */
    protected $affinityGroup;

    /**
     * @var string
     */
    protected $location;

    /**
     * @var bool
     *
     * @Assert\Type(type="bool")
     */
    protected $geoReplicationEnabled = true;

    /**
     * @var array
     *
     * @Assert\Valid
     */
    protected $extendedProperties = array();

    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    public function getServiceName()
    {
        return $this->serviceName;
    }

    public function setAffinityGroup($affinityGroup)
    {
        $this->affinityGroup = $affinityGroup;
    }

    public function getAffinityGroup()
    {
        return $this->affinityGroup;
    }

    public function setLocation($location)
    {
        $this->location = $location;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function enableGeoReplication()
    {
        $this->geoReplicationEnabled = true;
    }

    public function disableGeoReplication()
    {
        $this->geoReplicationEnabled = false;
    }

    public function isGeoReplicationEnabled()
    {
        return true === $this->geoReplicationEnabled;
    }

    public function __toString()
    {
        return $this->getDom()->saveXML();
    }

    public function getDOM()
    {
        $domDoc = new DOMDocument();
        $domDoc->loadXML('<?xml version="1.0" encoding="utf-8"?><CreateStorageServiceInput xmlns="http://schemas.microsoft.com/windowsazure"></CreateStorageServiceInput>');
        $domRoot = $domDoc->documentElement;

        $serviceNameNode = $domDoc->createElement('ServiceName', $this->getServiceName());
        $domRoot->appendChild($serviceNameNode);

        if (null !== $this->getDescription()) {
            $descriptionNode = $domDoc->createElement('Description', $this->getDescription());
            $domRoot->appendChild($descriptionNode);
        }

        $labelNode = $domDoc->createElement('Label', base64_encode($this->getLabel()));
        $domRoot->appendChild($labelNode);

        if (null !== $this->getAffinityGroup()) {
            $affinityGroupNode = $domDoc->createElement('AffinityGroup', $this->getAffinityGroup());
            $domRoot->appendChild($affinityGroupNode);
        }

        if (null !== $this->getLocation()) {
            $locationNode = $domDoc->createElement('Location', $this->getLocation());
            $domRoot->appendChild($locationNode);
        }

        if (!$this->isGeoReplicationEnabled()) {
            $geoReplicationEnabledNode = $domDoc->createElement('GeoReplicationEnabled', 'false');
            $domRoot->appendChild($geoReplicationEnabledNode);
        }

        if ($this->hasExtendedProperties()) {
            $extendedPropertiesNode = $domDoc->createElement('ExtendedProperties');

            $properties = $this->getExtendedProperties();
            foreach ($properties as $property) {
                $extendedPropertyNode = $domDoc->importNode($property->getDom()->documentElement, true);
                $extendedPropertiesNode->appendChild($extendedPropertyNode);
            }

            $domRoot->appendChild($extendedPropertiesNode);
        }

        return $domDoc;
    }

    public function addExtendedProperty(ExtendedProperty $extendedProperty)
    {
        $this->extendedProperties[] = $extendedProperty;
    }

    public function getExtendedProperties()
    {
        return $this->extendedProperties;
    }

    public function hasExtendedProperties()
    {
        return !empty($this->extendedProperties);
    }

    public function isValid(ExecutionContext $context)
    {
        if ((null !== $this->getLocation() ^ null !== $this->getAffinityGroup()) === 0) {

        }
    }
}
