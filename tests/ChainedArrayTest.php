<?php

namespace Tests\vdeApps\phpCore;

use PHPUnit\Framework\TestCase;
use vdeApps\phpCore\ChainedArray;

class ChainedArrayTest extends TestCase {

    protected $arr1 = [
      'level1' => 'strLevel1'
    ];

    protected $arr2 = [
        'level1' => [
            'level2' => 'strLevel2'
        ],
        'level3'=> 'strLevel3'
    ];

    protected $arr2SetValues = [
        'level1' => [
            'level2' => 'strLevel2'
        ],
        'level3'=> 'strLevel3',
        'newElement'=>[
            'val1'=>1,
            'val2'=>2,
            'val3'=>3,
            'val4'=>4,
        ],
    ];

    protected $json2='{"level1":{"level2":"strLevel2"},"level3":"strLevel3"}';

    protected $arrAddLevel = [
        'level1' => [
            'level2' => 'strLevel2'
        ],
        'level3'=> 'strLevel3',
        'addLevel' => 'strAddLevel',
    ];

    protected $arrRemoveLevel3 = [
        'level1' => [
            'level2' => 'strLevel2'
        ],
    ];

    protected $jsonAppend = '{"level1":{"level2":"strLevel2"},"level3":"strLevel3","level5":[{"name":"vdeapps","line":2},{"name":"vdeapps","line":4},{"name":"vdeapps","line":6}]}';


    public function testConstruct(){

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
        catch (\Exception $ex){
            $this->assertEquals($ex->getCode(), 5);
        }
    }

    public function testCreate(){

        $o = ChainedArray::getInstance();
        try {
            $o->setArray("ezrezr");
        }
        catch (\Exception $ex){
            $this->assertEquals($ex->getCode(), 5);
        }

        $this->assertInstanceOf(
            ChainedArray::class,
            $o->setArray($this->arr2)
        );

        $o->setArray($this->arr2);
        $o->newElement
            ->set('val1',1)
            ->set('val2',2)
            ->set('val3',3)
            ->set('val4',4);


        $this->assertEquals($o->toArray(),$this->arr2SetValues);
    }

    public function testReturn(){
        $o = ChainedArray::getInstance($this->arr2);
    
        $this->assertEquals($this->arr2, $o->toArray());
    
        $this->assertEquals($this->json2, $o->toJson());
    
    
        // Ajout de sous-clés
        $o->sub1->sub2->sub3 = 'sub3Data';
    
        $this->assertInternalType('array', $o->level1);
    
    
        $this->assertInstanceOf(ChainedArray::class, $o->sub1->sub2);
        
        $this->assertEquals('sub3Data', $o->sub1->sub2->sub3);
    }

    public function testOperations(){
        $o = ChainedArray::getInstance($this->arr2);
        $o->addLevel = 'strAddLevel';
        $this->assertEquals($this->arrAddLevel, $o->toArray());

        $o->setArray($this->arr2);
        unset($o->level3);
        $this->assertEquals($this->arrRemoveLevel3, $o->toArray());

        $o = ChainedArray::getInstance($this->arr2);
        $o->level5
            ->append(['name'=>'vdeapps', 'line'=>2])
            ->append(['name'=>'vdeapps', 'line'=>4])
            ->append(['name'=>'vdeapps', 'line'=>6]);

        $this->assertEquals($o->toJson(), $this->jsonAppend);
    }
}