<?php
/**
 * @package KVD.dom
 * @subpackage systemfields
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDdom_NullSystemFieldsMapper
 *
 * @package KVD.dom
 * @subpackage systemfields
 * @since 1 jul 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDdom_NullSystemFieldsMapper extends KVDdom_AbstractSystemFieldsMapper
{
    /**
     * doLoadSystemFields
     *
     * @param StdClass $row
     * @param string $prefix
     * @return KVDdom_NullSystemFields
     */
    public function doLoadSystemFields( $row , $prefix = null )
    {
        return KVDdom_ChangeableSystemFields::newNull( );
    }

    /**
     * newNull
     *
     * @param integer $versie
     * @return KVDdom_NullSystemFields
     */
    public function newNull( $versie = 0)
    {
        return KVDdom_ChangeableSystemFields::newNull( );
    }

    /**
     * updateSystemFields
     *
     * @param KVdom_DomainObject $domainObject
     * @param string $gebruiker
     * @return void
     */
    public function updateSystemFields( KVDdom_DomainObject $domainObject , $gebruiker=null)
    {
        return;
    }
}
?>
