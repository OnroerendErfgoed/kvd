<?php
/**
 * KVDdom_ILogSessie 
 * 
 * @package KVD.dom
 * @subpackage Sessie
 * @since 12 feb 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_ILogSessie 
 * 
 * UnitOfWork voor objecten die kunnen gelezen en geschreven worden waarvoor er ook aan versiebeheer wordt gedaan.
 * @package KVD.dom
 * @subpackage Sessie
 * @since 12 feb 2007
 * @copyright 2004-2007 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
interface KVDdom_ILogSessie extends KVDdom_IWriteSessie {

    /**
     * De eigenaar van de sessie.
     *
     * Is belangrijk omdat de DataMappers dit nodig hebben om vast te stellen wie de wijzigingen doorvoert zodat ze kunnen gelogd worden.
     * @return KVDdom_Gebruiker
     */
    public function getGebruiker()
    {
        if ( $this->_gebruiker === null ) {
            $gebruikerMapper = $this->getGebruikerMapper();
            $this->_gebruiker = $gebruikerMapper->findById ( $this->gebruikerId );
        }
        return $this->_gebruiker;
    }

    /**
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> - Indien er een probleem bij het registreren is.
     * @throws <b>LogicException</b> - Indien er geprobeerd wordt een object goed te keuren dat niet goedgekeurd mag worden.
     */
    public function registerApproved ( $domainObject );

    /**
     * @param KVDdom_DomainObject $domainObject
     * @throws <b>Exception</b> - Indien er een probleem bij het registreren is.
     * @throws <b>LogicException</b> - Indien er geprobeerd wordt een object goed te keuren dat niet goedgekeurd mag worden.
     */
    public function registerHistoryCleared( $domainObject );
    
    /**
     * @return array Een array met 5 keys ( 'insert' , 'update' , 'delete' , 'approved', 'historyCleared' ) die het aantal affected records bevatten.
     * @throws <b>ConcurrencyException</b> - Indien er een versieconflict is bij het verwerken van de sessie.
     */
    public function commit();
  }
?>
