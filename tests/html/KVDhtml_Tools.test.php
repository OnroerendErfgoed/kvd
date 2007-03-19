<?php
class TestOfTools extends UnitTestCase
{
    private $testData;
    
    function setUp( )
    {
        $this->testData = array (  'Dit is een test.'       =>  'Dit is een test.' ,
                                    'Lena > Mira'           =>  'Lena &gt; Mira' ,
                                    'Luka & Felix'          =>  'Luka &amp; Felix' );
    }

    function tearDown( )
    {
        $this->testData = null;
    }

    public function testOut( )
    {
        foreach ( $this->testData as $key => $value ) {
            $this->assertEqual ( KVDhtml_Tools::out( $key) , $value );
        }
    }

    public function testOutImplode( )
    {
        $this->assertEqual( KVDhtml_Tools::outImplode( $this->testData , ' | ' ) , 'Dit is een test. | Lena &gt; Mira | Luka &amp; Felix');
        $this->assertEqual( KVDhtml_Tools::outImplode( $this->testData , ' <br/> ' ) , 'Dit is een test. <br/> Lena &gt; Mira <br/> Luka &amp; Felix');
    }
}
?>
