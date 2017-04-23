<?php

namespace MongoDB_CLI;

class Transformer
{
    private $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    /**
     * Get all projections
     *
     * @return array
     */
    public function getAll()
    {
        return [];
    }

    /**
     * Get array of projections
     *
     * @param $projections
     * @return array
     */
    public function getProjections($projections)
    {
        $array = [];

        for ($i=0;$i<count($projections);$i++)
        {
            $array[$projections[$i]] = 1;
        }

        return $array;

    }

    /**
     * Get WHERE options for find
     *
     * @param $expressions
     * @param string $operation
     * @return array
     */
    public function whereCondition($expressions,$operation = "")
    {
        $options = [];

        for($i=0;$i<count($expressions);$i++)
        {
            $expression = $expressions[$i];

            if ((int)$expression["value"])
            {
                $expression["value"] = (int)$expression["value"];
            }

            switch ($expression["condition"])
            {
                case "<":
                   $options[$expression["property"]] = array('$lt' => $expression["value"]);
                   break;
                case "<=":
                    $options[$expression["property"]] =  array('$lte' => $expression["value"]);
                    break;
                case ">":
                    $options[$expression["property"]] =  array('$gt' => $expression["value"]);
                    break;
                case ">=":
                    $options[$expression["property"]] = array('$gte' => $expression["value"]);
                    break;
                case "=":
                    $options[$expression["property"]] =  $expression["value"];
                    break;
                case "<>":
                    $options[$expression["property"]] = array('$ne' => $expression["value"]);
                    break;
            }

        }

        if ($operation == "OR")
        {
            $oroptions = [];
            foreach ($options as $key => $value)
            {
                $oroptions['$or'][] = [$key => $value];
            }

            return $oroptions;
        }
        else
        {
            return $options;
        }

    }

    /**
     * Get array of expression for WHERE
     *
     * @param $sql
     * @param $word
     * @return array
     */
    public function expressionGenerator($sql,$word)
    {
        $expression = [];

        for($i=0;$i<count($word);$i++)
        {
            $expression[] =
                [
                    "property" => $sql[$word[$i] + 1],
                    "condition" => $sql[$word[$i] + 2],
                    "value" => $sql[$word[$i] + 3]
                ];
        }

        return $expression;
    }

    /**
     * Get Limit value
     *
     * @param $sql
     * @return int
     */
    public function getLimitValue($sql)
    {
        return (int)$sql[array_search("LIMIT",$sql) + 1];
    }

    /**
     * Get Skip value
     *
     * @param $sql
     * @return int
     */
    public function getSkipValue($sql)
    {
        return (int)$sql[array_search("SKIP",$sql) + 1];
    }

    /**
     * Get Order By value
     *
     * @param $sql
     * @return mixed
     */
    public function getOrderByValue($sql)
    {
        $order["property"] = $sql[array_search("ORDER_BY",$sql) + 1];
        if ($sql[array_search("ORDER_BY",$sql) + 2] == "ASC")
        {
            $order["val"] = 1;
        }
        elseif ($sql[array_search("ORDER_BY",$sql) + 2] == "DESC")
        {
            $order["val"] = -1;
        }

        return $order;
    }

    /**
     * Get Mongo cursor based on defined values
     *
     * @param $options
     * @param $projections
     * @param $limit
     * @param $skip
     * @param $order
     * @return mixed
     */
    public function executeQuery($options,$projections,$limit,$skip,$order)
    {
        if ($limit and $skip)
        {
            $results = $this->collection->find($options, ['projection'=> $projections, 'skip' => $skip ,'limit' => $limit,'sort'=> array($order["property"] => $order["val"])]);
        }
        elseif ($skip)
        {
            $results = $this->collection->find($options, ['projection'=> $projections, 'skip' => $skip,'sort'=> array($order["property"] => $order["val"])]);
        }
        elseif ($limit)
        {
             $results = $this->collection->find($options, ['projection'=> $projections, 'limit' => $limit,'sort'=> array($order["property"] => $order["val"])]);
        }
        else
        {
            $results = $this->collection->find($options, ['projection'=> $projections, 'sort'=> array($order["property"] => $order["val"])]);
        }

        return $results;
    }

    /**
     * Print result to console
     *
     * @param $results
     */
    public function echoResult($results)
    {
        foreach ($results as $result) {
            foreach ($result as $key => $value) {
                if (is_object($value))
                {
                    echo $key. ":\n";
                    foreach ($value as $embkey => $embvalue)
                    {
                        echo $embkey . ':'. $embvalue . '    ';
                    }
                }
                else
                {
                    echo $key . ':'. $value . '    ';
                }
            }
            echo "\n \n";
        }

    }
}