<?php
class KVDutil_DownloadModel implements IteratorAggregate
{
    /**
     * map 
     * 
     * @var string
     */
    private $map;

    private $bestanden;
    
    public function __construct ( $map )
    {
        if ( !file_exists( $map ) || !is_dir ( $map ) ) {
            throw new InvalidArgumentException ( $map . ' is geen geldige map.' );
        }
        $this->genBestanden( $map );
    }

    private function genBestanden( $map )
    {
        $it = new DirectoryIterator( $map );
        foreach ( $it as $bestand ) {
            if ( !$bestand->isDot( ) && !$bestand->isDir( ) ) {
                $this->bestanden[] = $bestand->getFileInfo( );
            }
        }
    }

    public function getIterator( )
    {
        return new ArrayIterator( $this->bestanden );
    }
}
?>
