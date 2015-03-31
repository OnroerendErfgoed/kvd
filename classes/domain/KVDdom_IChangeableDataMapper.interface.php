<?php
/**
 * @package KVD.dom
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDdom_IChangeableDataMapper
 *
 * Deze class stelt een domainobject in staat zichzelf te laten opslaan in een datasource.
 * Onder een datasource verstaan we een externe bron die gegevens in leven houdt buiten
 * de php request cycle. Dit kan dus een databank, een bestand, een LDAP server,
 * een SOAP gateway of nog iets anders zijn.
 *
 * @package KVD.dom
 * @since 19 okt 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
interface KVDdom_IChangeableDataMapper extends KVDdom_IDataMapper
{
    /**
     * insert
     *
     * Voeg een nieuw object toe aan de datasource.
     * @param KVDdom_ChangeableDomainObject $object
     * @return KVDdom_ChangeableDomainObject
     */
    public function insert( $object );

    /**
     * update
     *
     * Bewerk een object in de datasource.
     * @param KVDdom_ChangeableDomainObject $object
     * @return KVDdom_ChangeableDomainObject
     */
    public function update( $object );

    /**
     * delete
     *
     * Verwijder een object uit de datasource.
     * @param KVDdom_ChangeableDomainObject $object
     * @return KVDdom_ChangeableDomainObject
     */
    public function delete( $object );

}
?>
