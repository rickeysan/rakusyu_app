<?php



require_once(dirname(__FILE__).'/../php_sheet/function.php');
// require_once('php_sheet/function.php');

debug('テストです');


class EmailfunctionTest extends PHPUnit\Framework\TestCase{
    public function testValidEmail1(){
        validEmail('abcgmail.com','email');
        $results = getErrMsg('email');
        $this->assertEquals(MSG04,$results);
        global $err_msg;
        $err_msg = array();
    }

    public function testValidEmailTrue(){
        validEmail('abc@gmail.com','email');
        $results = getErrMsg('email');
        $this->assertNull($results);
    }

    public function testValidHalf1(){
        validHalf('あいうえお','chars');
        $results = getErrMsg('chars');
        $this->assertEquals(MSG05,$results);
    }

    public function testValidEmailDupTrue(){
        $result = validEmailDup('red@outlook.jp');
        $this->assertEquals($result,1);
    }

    public function testGetUserProf(){
        $result = getUserProf(2);
        $this->assertTrue(!$result);
    }


}




