<?php
/**
 * @package KVD.util
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDutil_DownloadModel
 *
 * Class die alle bestanden in een bepaalde map weergeeft.
 * @package KVD.util
 * @since 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDutil_DownloadModel implements IteratorAggregate
{
    /**
     * map
     *
     * @var string
     */
    private $map;

    /**
     * bestanden
     *
     * @var array
     */
    private $bestanden = array( );

    /**
     * __construct
     *
     * @param string $map
     * @return void
     */
    public function __construct ( $map )
    {
        if ( !file_exists( $map ) || !is_dir ( $map ) ) {
            throw new InvalidArgumentException ( $map . ' is geen geldige map.' );
        }
        $this->genBestanden( $map );
    }

    /**
     * genBestanden
     *
     * @param string $map
     * @return void
     */
    private function genBestanden( $map )
    {
        $it = new DirectoryIterator( $map );
        foreach ( $it as $bestand ) {
            if ( !$bestand->isDot( ) && !$bestand->isDir( ) ) {
                $this->bestanden[] = $bestand->getFileInfo( );
            }
        }
        uasort( $this->bestanden, array( $this, 'cmpSPLFileInfo' ) );
    }

    /**
     * cmpSPLFileInfo
     *
     * @param   SPLFileInfo     $info1
     * @param   SPLFileInfo     $info2
     * @return  integer         -1, 0 of 1
     */
    private function cmpSPLFileInfo( SPLFileInfo $info1, SPLFileInfo $info2 )
    {
        return strcmp( $info1->getFileName( ), $info2->getFileName( ) );
    }

    /**
     * getIterator
     *
     * @return ArrayIterator
     */
    public function getIterator( )
    {
        return new ArrayIterator( $this->bestanden );
    }
}
?>
