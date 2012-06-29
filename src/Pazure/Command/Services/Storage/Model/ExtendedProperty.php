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

use DOMDocument;

class ExtendedProperty
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\MaxLength(limit=64)
     * @Assert\Regex(pattern="/^[\w_]+$/")
     */
    protected $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     */
    protected $value;

    public function __construct($name, $value)
    {
        $this->setName($name);
        $this->setValue($value);
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString() {
        return $this->getDom()->saveXML();
    }

    public function getDOM() {
        $domDoc = new DOMDocument();
        $domDoc->loadXML('<ExtendedProperty></ExtendedProperty>');

        $nameNode = $domDoc->createElement('Name', $this->getName());
        $domDoc->documentElement->appendChild($nameNode);

        $valueNode = $domDoc->createElement('Value', $this->getValue());
        $domDoc->documentElement->appendChild($valueNode);

        return $domDoc;
    }
}
