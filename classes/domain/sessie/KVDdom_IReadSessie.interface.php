<?php
/**
 * KVDdom_IReadSessie 
 * 
 * @package KVD.dom
 * @subpackage Sessie
 * @version $Id$
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_IReadSessie 
 * 
 * Een UnitOfWork die elementen leest maar nooit wegschrijft. Is de basis voor alle andere UnitOfWork interfaces. 
 * Is mogelijk geen UOW aangezien er geen Work is.
 * @package KVD.dom
 * @subpackage Sessie
 * @since 12 feb 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
interface KVDdom_IReadSessie {

    /**
     * @return KVDdom_GenericIdentityMap
     */
    public function getIdentityMap();

    /**
     * @param mixed $domainObject Ofwel een class-naam van een KVDdom_DomainObject, ofwel een KVDdom_DomainObject.
     * @return KVDdom_Datamapper Een datamapper voor het desbetreffende DomainObject.
     * @throws <b>InvalidArgumentException</b> - Indien de parameter $domainObject geen string of DomainObject is.
	 * @throws <b>KVDdom_DatabaseUnavailableException</b> - Indien de voor de dataMapper gespecifieerde connectie niet bestaat.
     */
    public function getMapper( $domainObject );

    /**
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> - Indien er een probleem bij het registreren is.
     */
    public function registerClean ( $domainObject );
    
    /**
     * Zoek de connectie die bij een bepaalde datamapper hoort.
     * 
     * @param string $dataMapper De naam van een dataMapper.
	 * @return Database Een database connectie.
	 * @throws <b>KVDdom_DatabaseUnavailableException</b> - Indien de voor de dataMapper gespecifieerde connectie niet bestaat.
	 */
	public function getDatabaseConnection ( $dataMapper );
    
    /**
     * @deprecated Gebruik {@link KVDdom_IReadSessie.getLogger}.
     * @return KVDdom_SqlLogger
     */
    public function getSqlLogger ();

    /**
     * @return KVDdom_ISessieLogger
     */
    public function getLogger ();
}
?>
