<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */
 
/**
 * DomainObjects die gelogd kunnen worden.
 *
 * Het loggen houdt in dat er voor elk record een gebruiker, wijziginsdatum en versie wordt bijgehouden. 
 * Van elke wijziging wordt dan ook nog bijgehouden of ze al dan niet is goedgekeurd door de redactie.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 2005
 */
abstract class KVDdom_LogableDomainObject extends KVDdom_ChangeableDomainObject implements KVDdom_Nullable
{
    /**
     * Systemfields object dat eigenaar, versie, e.d. bijhoudt.
     * @var KVDdom_RedigeerbareSystemFields
     */
    protected $_systemFields;

    /**
     * @var KVDdom_DomainObjectCollection
     */
    protected $_geschiedenis;

    /**
     * currentRecord 
     * 
     * True indien het record uit de hoofdtabellen komt, false indien het geladen werd uit de log tabellen.
     * @var boolean
     */
    protected $currentRecord;

    /**
     * @param KVDdom_Sessie $sessie 
     * @param integer $id
     * @param KVDdom_RedigeerbareSystemFields $systemFields
     */
    public function __construct ( $id , $sessie , $systemFields = null, $currentRecord = true )
    {
        $this->id = $id;
        $this->_sessie = $sessie;
        if ($systemFields === null) {
            $this->_systemFields = new KVDdom_RedigeerbareSystemFields($this->_sessie->getGebruiker()->getGebruikersNaam());
        } else {
            $this->_systemFields = $systemFields;
        }
        $this->currentRecord = $currentRecord;
        if ( $this->isCurrentRecord( ) ) {
            $this->markClean( );
        }
        $this->_geschiedenis = self::PLACEHOLDER;
    }

    
    
    /**
     * Geef het SystemFields object van dit DomainObject terug
     * @return KVDdom_RedigeerbareSystemFields
     */
    public function getSystemFields()
    {
        return $this->_systemFields;
    }

    /**
     * hasSystemFields 
     * 
     * @return boolean
     */
    public function hasSystemFields( )
    {
        return true;
    }

    /**
     * @return boolean
     */
    public function isNull( )
    {
        return false;
    }

    /**
     * isCurrentRecord 
     * 
     * True indien het record uit de hoofdtabellen komt, false indien het geladen werd uit de log tabellen.
     * @return boolean
     */
    public function isCurrentRecord( )
    {
        return $this->currentRecord;
    }

    /**
     * @return KVDdom_DomainObjectCollection
     */
    public function getGeschiedenis( )
    {
        if ( $this->_geschiedenis === self::PLACEHOLDER ) {
            $mapper = $this->_sessie->getMapper( $this->getClass( ) );
            $this->_geschiedenis = $mapper->findLogAll( $this->id );
        }
        return $this->_geschiedenis;
    }

}
