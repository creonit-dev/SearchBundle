<?php

namespace Creonit\SearchBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class SearchDaemonCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('search:daemon')
            ->setDescription('...')
            ->addOption('start', null, InputOption::VALUE_NONE)
            ->addOption('stop', null, InputOption::VALUE_NONE)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        if($input->getOption('stop')){
            $this->getContainer()->get('creonit_search')->stop();
        }else{
            $this->getContainer()->get('creonit_search')->start();
        }

    }

}
