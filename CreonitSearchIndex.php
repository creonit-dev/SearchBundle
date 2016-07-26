<?php

namespace Creonit\SearchBundle;

use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Propel;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class CreonitSearchIndex
{

    protected $name;
    protected $configuration;
    
    
    public function __construct($configuration)
    {
        $this->configuration = $configuration;

        $this->name = $configuration['name'];
    }

    public function build($dir, $indexDir){
        /** @var ModelCriteria $query */
        $query = $this->configuration['entity'] . 'Query';
        $query = new $query;

        $connection = Propel::getConnection();

        $attr = [];

        if(isset($this->configuration['query'])){
            $language = new ExpressionLanguage();
            $query = $language->evaluate($this->configuration['query'], ['query' => $query]);
        }

        if(isset($this->configuration['select'])){
            $select = (array) $query->getSelect();
            $query->select(array_merge($this->configuration['select'], $select));
        }

        if(isset($this->configuration['attr'])){
            $select = (array) $query->getSelect();
            $query->select(array_merge($this->configuration['attr'], $select));
            $attr = $this->configuration['attr'];
            
        }

        $select = (array) $query->getSelect();

        $query
            ->withColumn("'{$this->getName()}'", 'search_index')
            ->select(array_merge($select, ['search_index'], ['id']))
            ->addSelfSelectColumns()
            ->configureSelectColumns();

        $params = [];
        $sql = $query->createSelectSql($params);

        if(!preg_match('/^SELECT `[\w_]+`\.`id` as "id"/usi', $sql)){
            $sql = preg_replace('/^SELECT(.*?), (`[\w_]+`\.`id` as "id")(.*?)/usi', 'SELECT $2,$1$3', $sql);
        }


        $position = 0;
        foreach ($params as $param) {
            $position++;
            $parameter = ':p' . $position;
            $value = $param['value'];
            if (null === $value) {
                $sql = preg_replace('/'. $parameter.'/', $connection->quote($value, \PDO::PARAM_NULL), $sql);
                continue;
            }
            $tableName = $param['table'];
            if (null === $tableName) {
                $type = isset($param['type']) ? $param['type'] : \PDO::PARAM_STR;
                $sql = preg_replace('/'.$parameter.'/', $connection->quote($value, $type), $sql);

                continue;
            }

            $type = is_bool($param['value']) ? \PDO::PARAM_BOOL : (is_numeric($param['value']) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
            $sql = preg_replace('/'.$parameter.'/', $connection->quote($value, $type), $sql);
        }

        $attr = implode("\n\t\t\t\t\t", array_map(function($attr){return "sql_attr_uint = {$attr}";}, $attr));

        return "
                source {$this->getName()} : main {
                    sql_query = {$sql}
                    sql_attr_string = search_index
                    {$attr}
                }
                
                index {$this->getName()} {
                    source = {$this->getName()}
                    path   = {$indexDir}/{$this->getName()}
                    morphology      = stem_en, stem_ru
                    
                    wordforms = {$dir}/wordforms.txt
                    
                    html_strip = 1
                    
                    min_word_len = 3
                    min_infix_len = 3
                   
                    expand_keywords = 1
                    index_exact_words = 1
                }
            ";
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function retrieveEntity($pk)
    {
        $query = $this->configuration['entity'] . 'Query';
        return (new $query)->findPk($pk);
    }

    public function retrieveEntities($ids)
    {
        $query = $this->configuration['entity'] . 'Query';
        return (new $query)->filterById($ids);
    }

}