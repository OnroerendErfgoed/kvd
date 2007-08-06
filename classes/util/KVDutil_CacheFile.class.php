<?php
/**
 * @package KVD.util
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Class om data te cachen in een gewone file.
 *
 * Overgenomen uit Advanced Php Programming van George Schlossnagle.
 * @package KVD.util
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @author George Schlossnagle
 * @since 1.0.0
 */
class KVDutil_CacheFile
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var string
     */
    private $tempFilename;

    /**
     * @var integer
     */
    private $expiration;

    /**
     * @var resource
     */
    private $fileHandle;

    /**
     * @param string $filename
     * @param mixed $expirtation Integer of false indien geen expirtation.
     */
    public function __construct( $filename, $expiration = false )
    {
        $this->filename = $filename;
        $this->tempFilename = "$filename.". getmypid( );
        $this->expiration = $expiration;
    }

    /**
     * @param string $buffer
     * @return boolean True indien de data werd weggeschreven, false indien dit niet mogelijk was.
     */
    public function put ( $buffer )
    {
        if ( ( $this->fileHandle = fopen( $this->tempFilename , 'w') ) == false ) {
            return false;
        }
        fwrite ( $this->fileHandle , $buffer );
        fclose (  $this->fileHandle );
        rename (  $this->tempFilename , $this->filename );
        return true;
    }

    /**
     * @return mixed De gecachete data of false indien de cache verlopen is.
     */
    public function get( ) {
        if ( $this->expiration) {
            $stat = @stat (  $this->filename );
            if ( $stat['mtime'] ) {
                if ( time( ) > $stat['mtime'] + $this->expiration ) {
                    unlink ( $this->filename );
                    return false;
                }
            }
        }
        return @file_get_contents( $this->filename );
    }

    public function remove( ) {
        @unlink( $this->filename );
    }
}
?>
