<?php
/**
 * Copyright (c) vdeApps 2018
 */

namespace vdeApps\phpCore;

class ChainedArray implements \Iterator
{
    
    private $array = [];
    
    /**
     * chainedArray constructor.
     *
     * @param chainedArray | array $arr
     *
     * @throws \Exception
     */
    public function __construct($arr = [])
    {
        $this->setArray($arr);
    }
    
    /**
     * Return value of array
     *
     * @param array  $data
     * @param string $key          key of array
     * @param mixed  $defaultvalue value if not found
     *
     * @return mixed
     */
    public static function getValue($data, $key, $defaultvalue = false)
    {
        //        if (is_a($data, ChainedArray::class)) {
        //            $result = $data->get($key);
        //
        //            if (is_a($result, ChainedArray::class)) {
        //                /** ChainedArray $result */
        //                return ($result->count()==0) ? $defaultvalue : $result;
        //            } else {
        //                return $result;
        //            }
        //        } else
        if (is_array($data)) {
            return (array_key_exists($key, $data)) ? $data[$key] : $defaultvalue;
        } else {
            return $defaultvalue;
        }
    }
    
    /**
     * Instance of
     *
     * @param chainedArray | array $arr
     *
     * @return chainedArray
     * @throws \Exception
     */
    public static function getInstance($arr = [])
    {
        return new self($arr);
    }
    
    /**
     * Compare 2 array and return result of IN/NOTIN
     *
     * @param        mixed      ,array  $arr1 La variable sera transformée en tableau
     * @param        mixed      ,array  $arr2 La variable sera transformée en tableau
     * @param string $operateur IN, NOTIN
     *
     * @return array|bool   Tableau des valeurs (IN ou NOTIN) ou FALSE
     */
    public static function compareValues($arr1 = [], $arr2 = [], $operateur = 'IN')
    {
        $result = [];
        
        if (!is_array($arr1)) {
            $arr1 = [$arr1];
        }
        if (!is_array($arr2)) {
            $arr2 = [$arr2];
        }
        
        if (!is_array($arr1) || !is_array($arr2)) {
            return false;
        }
        
        switch (strtoupper($operateur)) {
            case 'IN':
                $result = array_intersect($arr1, $arr2);
                break;
            
            case 'NOTIN':
                $result = array_diff($arr1, $arr2);
                break;
            
            default:
                $result = false;
                break;
        }
        
        return ($result && count($result) != 0) ? $result : false;
    }
    
    /**
     * Tri d'un tableau associatif
     *
     * @param     $arr
     * @param     $key
     * @param int $type
     *
     * @return mixed
     */
    public static function assocSort(&$arr, $key, $type = SORT_STRING)
    {
        
        $ret = usort($arr, function ($a, $b) use ($key, $type)
        {
            switch ($type) {
                case SORT_STRING:
                    return strcasecmp($a[$key], $b[$key]);
                    break;
                
                case SORT_NUMERIC:
                    return $a[$key] > $b[$key];
                    break;
                
                default:
                    return 0;
            }
        });
        
        return $ret;
    }
    
    /**
     * Initialize object
     *
     * @param chainedArray | array $array
     *
     * @return ChainedArray
     * @throws \Exception
     */
    public function setArray($array)
    {
        if (is_a($array, self::class)) {
            $this->array = $array->getData();
        } elseif (is_array($array)) {
            $this->array = $array;
        } else {
            throw new \Exception("Not an array or ChainedArray", 5);
        }
        
        return $this;
    }
    
    /**
     * return data storage
     *
     * @return array
     */
    public function getData()
    {
        return $this->array;
    }
    
    /**
     * Clear all data
     * @return ChainedArray
     */
    public function clear()
    {
        return $this->setArray([]);
    }
    
    /**
     * Return array result
     *
     * @return array
     */
    public function __invoke()
    {
        return $this->toArray();
    }
    
    /**
     * return array
     *
     * @return array
     */
    public function toArray()
    {
        
        $result = $this->array;
        
        foreach ($result as $key => $val) {
            
            /**
             * @var self $val
             */
            if (is_a($val, self::class)) {
                $result[$key] = $val->toArray();
            } else {
                $result[$key] = $val;
            }
        }
        
        return $result;
    }
    
    /**
     * set name / value
     *
     * @param $name
     * @param $value
     *
     * @return $this
     */
    public function set($name, $value)
    {
        $this->__set($name, $value);
        
        return $this;
    }
    
    /**
     * Get data by name
     *
     * @param string $name
     *
     * @return mixed
     */
    public function get($name)
    {
        return $this->__get($name);
    }
    
    /**
     * @param $name
     *
     * @return mixed
     * @throws \Exception
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->array)) {
            if (is_array($this->array[$name])) {
                return self::getInstance($this->array[$name]);
            } else {
                return $this->array[$name];
            }
        } else {
            $this->array[$name] = new self();
            
            return $this->array[$name];
        }
    }
    
    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        
        //        $this->array[$name] = (is_array($value) ) ? self::getInstance($value) :  $value;
        $this->array[$name] = $value;
    }
    
    /**
     * Number of elements
     * @return int
     */
    public function count()
    {
        return count($this->array);
    }
    
    /**
     * @param $name
     *
     * @return $this
     */
    public function __unset($name)
    {
        unset($this->array[$name]);
        
        return $this;
    }
    
    /**
     * Return false if value is NULL
     *
     * @param $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        if (is_null($this->array[$name])) {
            return false;
        } else {
            return true;
        }
    }
    
    /**
     * Return the current element
     *
     * @link   http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since  5.0.0
     */
    public function current()
    {
        return current($this->array);
    }
    
    /**
     * Move forward to next element
     *
     * @link   http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since  5.0.0
     */
    public function next()
    {
        next($this->array);
    }
    
    /**
     * Return the key of the current element
     *
     * @link   http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since  5.0.0
     */
    public function key()
    {
        return key($this->array);
    }
    
    /**
     * Checks if current position is valid
     *
     * @link   http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since  5.0.0
     */
    public function valid()
    {
        $key = key($this->array);
        
        return ($key !== null && $key !== false);
    }
    
    /**
     * Rewind the Iterator to the first element
     *
     * @link   http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since  5.0.0
     */
    public function rewind()
    {
        reset($this->array);
    }
    
    /**
     * Append array to element
     *
     * @param $arr
     *
     * @return $this
     */
    public function append($arr = [])
    {
        $this->array [] = $arr;
        
        return $this;
    }
    
    /**
     * Return Json format
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
}
