<?php

namespace Tests\vdeApps\phpCore;

use PHPUnit\Framework\TestCase;
use vdeApps\phpCore\ChainedArray;
use vdeApps\phpCore\Helper;

class CTest {
    
    protected $var1 = 'var1';
    
    public function fct1() {
        return $this->var1;
    }
}

class ChainedArrayTest extends TestCase {
    
    protected $arr1 = [
        'level1' => 'strLevel1',
    ];
    
    protected $arr2 = [
        'level1' => [
            'level2' => 'strLevel2',
        ],
        'level3' => 'strLevel3',
    ];
    
    protected $arr2SetValues = [
        'level1'     => [
            'level2' => 'strLevel2',
        ],
        'level3'     => 'strLevel3',
        'newElement' => [
            'val1' => 1,
            'val2' => 2,
            'val3' => 3,
            'val4' => 4,
        ],
    ];
    
    protected $json2 = '{"level1":{"level2":"strLevel2"},"level3":"strLevel3"}';
    
    protected $arrAddLevel = [
        'level1'   => [
            'level2' => 'strLevel2',
        ],
        'level3'   => 'strLevel3',
        'addLevel' => 'strAddLevel',
    ];
    
    protected $arrRemoveLevel3 = [
        'level1' => [
            'level2' => 'strLevel2',
        ],
    ];
    
    protected $jsonAppend = '{"level1":{"level2":"strLevel2"},"level3":"strLevel3","level5":[{"name":"vdeapps","line":2},{"name":"vdeapps","line":4},{"name":"vdeapps","line":6}]}';
    
    protected $arrToSort = [
        [
            'prenom' => 'vincent',
            'nom'    => 'test',
        ],
        [
            'prenom' => 'Romain',
            'nom'    => 'test',
        ],
        [
            'prenom' => 'Eloise',
            'nom'    => 'test',
        ],
    ];
    
    protected $arrSorted = [
        [
            'prenom' => 'Eloise',
            'nom'    => 'test',
        ],
        [
            'prenom' => 'Romain',
            'nom'    => 'test',
        ],
        [
            'prenom' => 'vincent',
            'nom'    => 'test',
        ],
    ];
    
    public function testConstruct() {
        
        $this->assertInstanceOf(
            ChainedArray::class,
            ChainedArray::getInstance()
        );
        
        $this->assertInstanceOf(
            ChainedArray::class,
            new ChainedArray()
        );
        
        $this->assertInstanceOf(
            ChainedArray::class,
            ChainedArray::getInstance($this->arr1)
        );
        
        try {
            $this->assertInstanceOf(
                Exception::class,
                ChainedArray::getInstance("NotAnArray")
            );
        }
        catch (\Exception $ex) {
            $this->assertEquals($ex->getCode(), 5);
        }
    }
    
    public function testCreate() {
        
        $o = ChainedArray::getInstance();
        try {
            $o->setArray("ezrezr");
        }
        catch (\Exception $ex) {
            $this->assertEquals($ex->getCode(), 5);
        }
        
        $this->assertInstanceOf(
            ChainedArray::class,
            $o->setArray($this->arr2)
        );
        
        $o->setArray($this->arr2SetValues);
        $this->assertEquals($o->toArray(), $this->arr2SetValues);
        
        $o->newSubElement
            ->set('val1', 1)
            ->set('val2', 2)
            ->set('val3', 3)
            ->set('val4', 4);
        
        $this->assertInstanceOf(ChainedArray::class, $o->newSubElement);
        
        $this->assertInstanceOf(ChainedArray::class, $o->newElement);
        
        $o->myObject = new CTest();
        $this->assertInstanceOf(CTest::class, $o->myObject);
    }
    
    public function testReturn() {
        $o = ChainedArray::getInstance($this->arr2SetValues);
        $o->newSubElement
            ->set('val1', 1)
            ->set('val2', 2)
            ->set('val3', 3)
            ->set('val4', 4);
        
        $resultArray = [
            'level1' =>
                [
                    'level2' => 'strLevel2',
                ],
            
            'level3'     => 'strLevel3',
            'newElement' =>
                [
                    'val1' => 1,
                    'val2' => 2,
                    'val3' => 3,
                    'val4' => 4,
                ],
            
            'newSubElement' =>
                [
                    'val1' => 1,
                    'val2' => 2,
                    'val3' => 3,
                    'val4' => 4,
                ],
        
        ];
        
        
        $this->assertEquals($resultArray, $o->toArray());
        
        $resultJson = '{"level1":{"level2":"strLevel2"},"level3":"strLevel3","newElement":{"val1":1,"val2":2,"val3":3,"val4":4},"newSubElement":{"val1":1,"val2":2,"val3":3,"val4":4}}';
        $this->assertEquals($resultJson, $o->toJson());
        
        
        $o = ChainedArray::getInstance($this->arr2);
        
        // Ajout de sous-clés
        $o->sub1->sub2->sub3 = 'sub3Data';
        
        $this->assertInstanceOf(ChainedArray::class, $o->level1);
        
        $this->assertInstanceOf(ChainedArray::class, $o->sub1->sub2);
        
        $this->assertEquals('sub3Data', $o->sub1->sub2->sub3);
    }
    
    public function testOperations() {
        $o = ChainedArray::getInstance($this->arr2);
        $o->addLevel = 'strAddLevel';
        $this->assertEquals($this->arrAddLevel, $o->toArray());
        
        $o->setArray($this->arr2);
        unset($o->level3);
        $this->assertEquals($this->arrRemoveLevel3, $o->toArray());
        
        $o = ChainedArray::getInstance($this->arr2);
        $o->level5
            ->append(['name' => 'vdeapps', 'line' => 2])
            ->append(['name' => 'vdeapps', 'line' => 4])
            ->append(['name' => 'vdeapps', 'line' => 6]);
        
        $this->assertEquals($o->toJson(), $this->jsonAppend);
        
        // count elements from level5
        $this->assertEquals(3, count($o->get('level5')->toArray()));
        
        // clear elements from level5
        $o->level5->clear();
        
        // count elements from level5
        $this->assertEquals(0, count($o->get('level5')->toArray()));
        
        // clear all elements
        $o->clear();
        // count elements
        $this->assertEquals(0, count($o->toArray()));
    }
    
    public function testFind() {
        
        $o = ChainedArray::getInstance($this->arr2SetValues);
        
        $res = ChainedArray::getValue($o->toArray(), 'newElement', 'foo');
        $this->assertEquals($this->arr2SetValues['newElement'], $res);
        
        $res = ChainedArray::getValue($o->toArray(), 'badkey', 'foo');
        $this->assertEquals('foo', $res);
        
        $res = ChainedArray::getValue($o->toArray(), 'badkey');
        $this->assertEquals(false, $res);
        
        
        $res = ChainedArray::getValue($this->arr2SetValues, 'newElement');
        $this->assertEquals($this->arr2SetValues['newElement'], $res);
        
        $res = ChainedArray::getValue($this->arr2SetValues, 'badKey', 'notfound');
        $this->assertEquals('notfound', $res);
        
    }
    
    
    public function testCompareValues() {
        
        $tb1 = ['A', 'B', 'C', 'D', 'E'];
        
        $tbIN = ['B', 'D'];
        
        $tbNOTIN = ['B', 'D'];
        
        $res = ChainedArray::compareValues($tb1, $tb1);
        
        $this->assertEquals($tb1, $res);
        //        print_r($res);
        
    }
    
    public function testSortValues() {
        
        $arrToSort = $this->arrToSort;
        
        $ret = ChainedArray::assocSort($arrToSort, 'prenom', SORT_STRING);
        
        $this->assertEquals($ret, true);
        
        $this->assertEquals($arrToSort, $this->arrSorted);
    }
}
