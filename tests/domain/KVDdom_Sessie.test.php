<?php
Mock::generate('VM_Locatie');
Mock::generate('kzlCAI_AdministratieveEenheid');

class TestofSessie extends UnitTestCase
{
    private $_sessie;
    
    function setUp( )
    {
        
        $this->_sessie = new KVDdom_Sessie ( $gebruikerId , $databaseManager , $config );
    }
    
    function testIdentityMap()
    {
        $this->assertNotNull ($this->_sessie->getIdentityMap());
        $this->assertIsA ($this->_sessie->getIdentityMap(), 'KVDdom_GenericIdentityMap');
    }

    function testInsertDomainObject( )
    {
        $locatie = new MockVM_Locatie ( $this );
        $AE = new MockkzlCAI_AdministratieveEenheid ();
        $AE->setReturnValue('getId', '0');
        $locatie->setReturnValue('getId', '54321');
        $locatie->setReturnValue('getClass', 'VM_Locatie');
        $locatie->setReturnReference( 'getAdministratieveEenheid', $AE);
        $locatie->expectAtLeastOnce('getId');
        $sessie->registerNew ( $locatie );
        $results = $sessie->commit();
        $this->assertEqual ($results['insert'] , 1);
        $this->assertEqual ($results['update'] , 0);
        $this->assertEqual ($results['delete'] , 0);
    }
}
?>
