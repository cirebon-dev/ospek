<?php
use PHPUnit\Framework\TestCase;

final class OspekBackendTest extends TestCase
{
    private $_ospek;
    public function tearDown()
    {
        $this->_ospek = null;
    }

    public function testOspekRun()
    {
        $this->_ospek = new \Ospek\OspekBackend("php tests/loop.php", "process.log");
        sleep(3);
        // test getpid()
        $this->assertInternalType('int', $this->_ospek->getPid());
        
        // test status()
        $this->assertTrue($this->_ospek->status());
        
        // test getOutput()
        $this->assertInternalType('array', $this->_ospek->getOutput());
        
        // test stop()
        $this->assertTrue($this->_ospek->stop());
        sleep(1);
        
        // check status after stop
        $this->assertFalse($this->_ospek->status());
        // make sure process.log not empty
        
        $this->assertNotEmpty(file_get_contents("process.log"));
        
    }
    public function testOspekDummy()
    {
        $this->_ospek = new \Ospek\OspekBackend();
        $this->assertNull($this->_ospek->setPid(1234567));
        $this->assertSame(1234567, $this->_ospek->getPid());
         $this->assertFalse($this->_ospek->status());
        try{
            $this->_ospek->getOutput('fakeeeeeeee/path/file.log');
        } catch(Exception $e){
            $this->assertInstanceOf(RuntimeException::class, $e);
        }
      
         
                    
    }
}
            