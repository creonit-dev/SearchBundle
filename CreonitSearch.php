<?php

namespace Creonit\SearchBundle;

use Creonit\SearchBundle\Model\SearchWordformQuery;
use Foolz\SphinxQL\Drivers\ResultSetInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Process\ProcessBuilder;

class CreonitSearch
{
    /** @var  ContainerInterface */
    protected $container;
    protected $configuration;

    /** @var  CreonitSearchIndex[] */
    protected $indexes = [];
    protected $sphinxDir;
    protected $sphinxIndexDir;
    protected $sphinxConfPath;
    protected $sphinxIndexer;
    protected $sphinxSearchd;



    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
        $this->sphinxDir = $this->container->getParameter('kernel.root_dir') . '/../var/sphinx';
        $this->sphinxConfPath = $this->sphinxDir . '/main.conf';
        $this->sphinxIndexDir = $this->sphinxDir . '/index';
    }

    public function setConfiguration($configuration){
        $this->configuration = $configuration;

        $this->sphinxIndexer = ($configuration['sphinx']['path'] ? $configuration['sphinx']['path'] . '/' : '') . 'indexer';
        $this->sphinxSearchd = ($configuration['sphinx']['path'] ? $configuration['sphinx']['path'] . '/' : '') . 'searchd';

        foreach($configuration['indexes'] as $indexName => $indexConfiguration){
            $this->addIndex(new CreonitSearchIndex(array_replace($indexConfiguration, ['name' => $indexName])));
        }
    }

    public function addIndex(CreonitSearchIndex $index){
        $this->indexes[$index->getName()] = $index;
    }

    protected function build(){
        if(!is_dir($this->sphinxIndexDir)){
            @mkdir($this->sphinxIndexDir, 0777, true);
        }

        $handle = fopen($this->sphinxDir . '/wordforms.txt', 'w');
        foreach(SearchWordformQuery::create()->find() as $wordform){
            fputs($handle, "{$wordform->getSource()} > {$wordform->getTarget()}\n");
        }
        fclose($handle);

        $config = "
            searchd
            {
                listen         = {$this->configuration['sphinx']['host']}:{$this->configuration['sphinx']['port']}:mysql41
                log            = {$this->sphinxDir}/searchd.log
                query_log      = {$this->sphinxDir}/query.log
                pid_file       = {$this->sphinxDir}/searchd.pid
                binlog_path =
            }
        
            source main
            {
                type			= {$this->configuration['database']['type']}
                sql_host		= {$this->configuration['database']['host']}
                sql_user		= {$this->configuration['database']['user']}	
                sql_pass		= {$this->configuration['database']['password']}
                sql_db			= {$this->configuration['database']['dbname']}
                sql_query_pre	= SET NAMES utf8
            }
  
        ";

        foreach ($this->indexes as $index){
            $config .= $index->build($this->sphinxDir, $this->sphinxIndexDir);
        }


        file_put_contents($this->sphinxConfPath, $config);
    }

    public function getEntities(ResultSetInterface $result)
    {
        $data = $result->fetchAllAssoc();
        $entities = [];

        $index = null;

        foreach ($data as $item){
            if(null === $index){
                $index = $item['search_index'];
            }else if($index != $item['search_index']){
                $index = false;
                break;
            }
        }

        if($index){
            $ids = [];
            foreach($data as $item){
                $ids[] = $item['id'];
            }
            $entities = $this->indexes[$index]->retrieveEntities($ids);

        }else{
            foreach($data as $item){
                $entities[] = $this->indexes[$item['search_index']]->retrieveEntity($item['id']);
            }
        }

        return $entities;
    }

    public function index($rotate = false)
    {
        $this->build();

        $process = new ProcessBuilder([$this->sphinxIndexer]);
        $process->add('--all');
        if(true === $rotate){
            $process->add('--rotate');
        }
        $process->add("--config");
        $process->add($this->sphinxConfPath);

        $process->getProcess()->run(function($type, $data){
            print_r($data);
        });
    }

    public function start()
    {
        $process = new ProcessBuilder([$this->sphinxSearchd]);
        $process->setTimeout(null);
        $process->add("--config");
        $process->add($this->sphinxConfPath);

        $process->getProcess()->run(function($type, $data){
            print_r($data);
        });
    }

    public function stop()
    {
        $process = new ProcessBuilder([$this->sphinxSearchd]);
        $process->add("--config");
        $process->add($this->sphinxConfPath);
        $process->add('--stop');

        $process->getProcess()->run(function($type, $data){
            print_r($data);
        });
    }

}