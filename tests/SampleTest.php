<?php

require('Sample.php');

class SampleTest extends PHPUnit\Framework\TestCase{
    public function test_add(){
        $this->assertEquals(10,add(4,6));
    }
    public function test_sub(){
        $this->assertEquals(1,sub(7,6));
    }
}
