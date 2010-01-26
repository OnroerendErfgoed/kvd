<?php

require_once ( 'PHPUnit/Framework.php' );

class KVDhtml_ToolsTest extends PHPUnit_Framework_TestCase
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
        foreach ( $this->testData as $input => $output ) {
            $this->assertEquals ( $output, KVDhtml_Tools::out( $input) );
        }
    }

    public function testOutImplode( )
    {
        $this->assertEquals('Dit is een test. | Lena &gt; Mira | Luka &amp; Felix',  KVDhtml_Tools::outImplode( $this->testData , ' | ' ) );
        $this->assertEquals('Dit is een test. <br/> Lena &gt; Mira <br/> Luka &amp; Felix', KVDhtml_Tools::outImplode( $this->testData , ' <br/> ' ) );
    }
}
?>
