<?php
/**
 * Copyright (c) vdeApps 2018
 */

namespace vdeApps\phpCore;

class ChainedArray implements \Iterator {
    
    private $array = [];

    /**
     * chainedArray constructor.
     *
     * @param chainedArray | array $arr
     * @throws \Exception
     */
    public function __construct($arr = []) {
        $this->setArray($arr);
    }

    /**
     * Initialize object
     *
     * @param chainedArray | array $array
     * @return ChainedArray
     * @throws \Exception
     */
    public function setArray($array) {
        if (is_a($array, self::class)) {
            $this->array = $array->getData();
        }
        elseif (is_array($array)){
            $this->array = $array;
        }
        else {
            throw new \Exception("Not an array or ChainedArray", 5);
        }
        return $this;
    }
    
    /**
     * return data storage
     *
     * @return array
     */
    public function getData() {
        return $this->array;
    }

    /**
     * Instance of
     *
     * @param chainedArray | array $arr
     *
     * @return chainedArray
     * @throws \Exception
     */
    public static function getInstance($arr = []) {
        return new self($arr);
    }
    
    /**
     * Return array result
     *
     * @return array
     */
    public function __invoke() {
        return $this->toArray();
    }
    
    /**
     * return array
     *
     * @return array
     */
    public function toArray() {
        
        $result = $this->array;
        
        foreach ($result as $key => $val) {
            
            /**
             * @var self $val
             */
            if (is_a($val, self::class)) {
                $result[$key] = $val->toArray();
            }
            else {
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
    public function set($name, $value) {
        $this->__set($name, $value);
        
        return $this;
    }

    /**
     * @param $name
     *
     * @return mixed
     * @throws \Exception
     */
    public function __get($name) {
        if (array_key_exists($name, $this->array)) {
            return $this->array[$name];
        }
        else {
            $this->array[$name] = new self();
            
            return $this->array[$name];
        }
    }
    
    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value) {
        
        //        $this->array[$name] = (is_array($value) ) ? self::getInstance($value) :  $value;
        $this->array[$name] = $value;
    }
    
    /**
     * @param $name
     *
     * @return $this
     */
    public function __unset($name) {
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
    public function __isset($name) {
        if (is_null($this->array[$name])) {
            return false;
        }
        else {
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
    public function current() {
        return current($this->array);
    }
    
    /**
     * Move forward to next element
     *
     * @link   http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since  5.0.0
     */
    public function next() {
        next($this->array);
    }
    
    /**
     * Return the key of the current element
     *
     * @link   http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since  5.0.0
     */
    public function key() {
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
    public function valid() {
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
    public function rewind() {
        reset($this->array);
    }
    
    /**
     * Append array to element
     *
     * @param $arr
     *
     * @return $this
     */
    public function append($arr = []) {
        $this->array [] = $arr;
        
        return $this;
    }
    
    /**
     * Return Json format
     * @return string
     */
    public function toJson(){
        return json_encode($this->toArray());
    }
}
