<?php
class KVDutil_HttpToolkitTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getSchemelessUriDataprovider
     */
    public function testSchemelessUri($uri, $result)
    {
        $this->assertEquals($result, KVDutil_HttpToolkit::schemelessUri($uri));
    }

    public function getSchemelessUriDataprovider( )
    {
        return array( 
            array ( 'https://inventaris.onroerenderfgoed.be', '//inventaris.onroerenderfgoed.be' ),
            array ( 'http://inventaris.onroerenderfgoed.be', '//inventaris.onroerenderfgoed.be' ),
            array ( '//inventaris.onroerenderfgoed.be', '//inventaris.onroerenderfgoed.be' ),
            array ( '//inventaris.onroerenderfgoed.be/', '//inventaris.onroerenderfgoed.be/' ),
            array ( '//inventaris.onroerenderfgoed.be//test', '//inventaris.onroerenderfgoed.be//test' ),
            array ( '//inventaris.onroerenderfgoed.be//test//', '//inventaris.onroerenderfgoed.be//test//' ),
        );
    }
}
