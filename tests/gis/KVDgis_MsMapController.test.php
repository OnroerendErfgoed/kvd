<?php

Mock::generate('CAI_Sessie');

class TestOfMsMapController extends UnitTestCase
{

    function testExisting()
    {
        $sessie = new MockCAI_Sessie( $this );
        //$sessie = new CAI_Sessie();
        $mapperFactory = new CAI_MapperFactory ( $sessie , CAI2_DM_DIR );
        $this->assertNotNull( $mapperFactory->createMapper ( 'VM_Locatie' ) );
        $this->assertNoErrors( $mapperFactory->createMapper ( 'VM_Locatie' ) );
    }

    function testNonExisting()
    {
        //$sessie = new CAI_Sessie();
        $sessie = new MockCAI_Sessie( $this );
        $mapperFactory = new CAI_MapperFactory ( $sessie , CAI2_DM_DIR );
        try {
            $mapperFactory->createMapper ( 'OnbestaandeClass' );
        } catch (Exception $e) {
            $testMessage = "Er werd geen mapper voor class OnbestaandeClass gevonden. Reden: Bestand " . CAI2_DM_DIR . "OnbestaandeClassDM.class.php bestaat niet.";
            $this->assertEqual($e->getMessage() , $testMessage );
        }
    }
}
?>
