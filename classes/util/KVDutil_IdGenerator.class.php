<?php
/**
 * @package   KVD.util
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * Simpele class die id's kan genereren
 *
 * @package   KVD.util
 * @since     28 maart 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author    Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDutil_IdGenerator
{
    /**
     * id
     *
     * @var integer
     */
    private $id;

    /**
     * __construct
     *
     * @param integer $start
     * @return void
     */
    public function __construct( $start = 0 )
    {
        $this->id = $start;
    }

    /**
     * next
     *
     * @return integer
     */
    public function next( )
    {
        return $this->id++;
    }
}
?>
