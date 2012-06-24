<?php

/*
 * This file is part of Pazure.
 *
 * (c) Gordon Franke <info@nevalon.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pazure\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Pazure\Util\Compiler;

use Cilex\Command\Command;

/**
 * Run the pazure compiler
 *
 * It is havy inspired by PHP CS utility compile command.
 *
 * @author Gordon Franke <info@nevalon.de>
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CompileCommand extends Command
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('compile')
            ->setDescription('Compiles pazure as a phar file')
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $compiler = new Compiler();
        $compiler->compile();
    }
}
