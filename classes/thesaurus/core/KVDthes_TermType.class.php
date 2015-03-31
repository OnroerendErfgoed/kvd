<?php
/**
 * @package    KVD.thes
 * @subpackage core
 * @copyright  2009-2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDthes_TermType
 *
 * @package    KVD.thes
 * @subpackage core
 * @since      16 apr 2009
 * @copyright  2009-2011 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDthes_TermType extends KVDdom_ValueDomainObject
{
    /**
     * __construct
     *
     * @param   string  $id
     * @param   string  $type
     * @return  KVDthes_TermType
     */
    public function __construct( $id, $type = 'Onbepaald' )
    {
        $this->id = $id;
        $this->type=$type;
    }

    /**
     * getType
     *
     * @return  string
     */
    public function getType( )
    {
        return $this->type;
    }

    /**
     * getOmschrijving
     *
     * @return  string
     */
    public function getOmschrijving( )
    {
        return $this->type;
    }

    /**
     * newNull
     *
     * @return  KVDthes_TermType
     */
    public static function newNull( )
    {
        return new KVDthes_TermType( 'ND', 'Non Descriptor');
    }

}
?>
