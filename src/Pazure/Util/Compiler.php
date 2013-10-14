<?php

/*
 * This file is part of Pazure.
 *
 * (c) Gordon Franke <info@nevalon.de>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Pazure\Util;

use Symfony\Component\Finder\Finder;

/**
 * The Compiler class compiles Pazure.
 *
 * It is havy inspired by PHP CS utility compile command.
 *
 * @author Gordon Franke <info@nevalon.de>
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Compiler
{
    /**
     * @param string $pharFile
     */
    public function compile($pharFile = 'pazure.phar')
    {
        if (file_exists($pharFile)) {
            unlink($pharFile);
        }

        $phar = new \Phar($pharFile, 0, 'pazure.phar');
        $phar->setSignatureAlgorithm(\Phar::SHA1);

        $phar->startBuffering();

        // CLI Component files
        foreach ($this->getFiles() as $file) {
            $path = str_replace(__DIR__.'/', '', $file);

            $phar->addFromString($path, file_get_contents($file));
        }
        $this->addPazure($phar);

        // Stubs
        $phar->setStub($this->getStub());

        $phar->stopBuffering();

        // $phar->compressFiles(\Phar::GZ);

        unset($phar);

        chmod($pharFile, 0777);
    }

    /**
     * Remove the shebang from the file before add it to the PHAR file.
     *
     * @param \Phar $phar PHAR instance
     */
    protected function addPazure(\Phar $phar)
    {
        $content = file_get_contents(__DIR__ . '/../../../pazure');
        $content = preg_replace('{^#!/usr/bin/env php\s*}', '', $content);

        $phar->addFromString('pazure', $content);
    }

    /**
     * get the stub string
     *
     * @return string
     */
    protected function getStub()
    {
        return "#!/usr/bin/env php\n<?php Phar::mapPhar('pazure.phar'); require 'phar://pazure.phar/pazure'; __HALT_COMPILER();";
    }

    /**
     * get the license information
     *
     * @return string
     */
    protected function getLicense()
    {
        return '
    /*
     * This file is part of Pazure.
     *
     * (c) Gordon Franke <info@nevalon.de>
     *
     * This source file is subject to the MIT license that is bundled
     * with this source code in the file LICENSE.
     */';
    }

    /**
     * get the needed files
     *
     * @return array
     */
    protected function getFiles()
    {
        $iterator = Finder::create()
            ->files()
            ->exclude(array('tests', 'Tests', 'vendor'))
            ->name('/.*\.(php|json)/')
            ->in(array('vendor', 'src'));

        return array_merge(array('LICENSE'), iterator_to_array($iterator));
    }
}
