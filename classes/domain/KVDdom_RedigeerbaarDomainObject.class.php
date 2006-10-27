<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */
 
/**
 * DomainObjects die gelogd kunnen worden.
 *
 * Het loggen houdt in dat er voor elk record een gebruiker, wijziginsdatum en versie wordt bijgehouden. Van elke wijziging wordt dan ook nog bijgehouden of ze al dan niet is goedgekeurd door de redactie.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */

abstract class KVDdom_LogableDomainObject extends KVDdom_ChangeableDomainObject implements KVDdom_Nullable, KVDdom_Verwijderbaar
{

    /**
     * Systemfields object dat eigenaar, versie, e.d. bijhoudt.
     * @var KVDdom_SystemFields
     */
    protected $_systemFields;

    /**
     * @var KVDdom_DomainObjectCollection
     */
    protected $_geschiedenis;

    /**
     * @param KVDdom_Sessie $sessie 
     * @param integer $id
     * @param KVDdom_SystemFields $systemFields
     */
    public function __construct ( $id , $sessie , $systemFields = null)
    {
        parent::__construct ($id , $sessie );
        $this->id = $id;
        $this->_sessie = $sessie;
        if ($systemFields === null) {
            $this->_systemFields = new KVDdom_SystemFields($this->_sessie->getGebruiker()->getGebruikersNaam());
        } else {
            $this->_systemFields = $systemFields;
        }
        if ( $this->_systemFields->isCurrentRecord( ) ) {
            $this->markClean( );
        }
        $this->_geschiedenis = self::PLACEHOLDER;
    }

    /**
     * Markeert dit object als Approved
     *
     * Dit record zal door KVDdom_Sessie worden goedgeurd in de databank bij het verwerken van de UnitOfWork.
     */
    protected function markApproved(  )
    {
        $this->_sessie->registerApproved( $this );
    }

    /**
     * Markeert dit object als HistoryCleared
     *
     * Dit geschiedenis van dit record zal door KVDdom_Sessie worden doorgevoerdb in de databank bij het verwerken van de UnitOfWork.
     */
    protected function markHistoryCleared(  )
    {
        $this->_sessie->registerHistoryCleared( $this );
    }
    
    
    /**
     * Geef het SystemFields object van dit DomainObject terug
     */
    public function getSystemFields()
    {
        return $this->_systemFields;
    }

    /**
     * Keur het domeinobject goed.
     */
    public function approve( )
    {
        $this->markApproved( );
        $this->_systemFields->setApproved( );
    }

    /**
     * @return boolean
     */
    public function isNull( )
    {
        return false;
    }

    /**
     * @return boolean
     */
    public function isVerwijderd( )
    {
        return false;
    }

    /**
     * Verwijder de geschiedenis van het object
     */
    public function verwijderGeschiedenis( )
    {
        $this->_geschiedenis = new KVDdom_DomainObjectCollection( array( ) );
        $this->markHistoryCleared( );
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

    /**
     * @param KVDdom_LogableDomainObject
     */
    abstract public function updateToPreviousVersion ( $previous );

    /**
     * Controleer of het mogelijk is het huidige DomainObject te updaten naar de vorige versie.
     * @param KVDdom_LogableDomainObject
     */
    protected function checkPreviousVersion( $previous )
    {
        if ( !$previous->getClass( ) === $this->getClass( ) ) {
            throw new LogicException ( 'Kan enkel update naar een vorige versie van mezelf!' );
        }
        if ( !$this->getSystemFields( )->isCurrentRecord( ) && !$this->isNull( ) ) {
            throw new LogicException ( 'Dit object is niet de huidige versie of een verwijderde versie en kan dus niet geupdate worden!');
        }
        if ( $previous->getSystemFields( )->isCurrentRecord( ) ) {
            throw new LogicException ( 'Er kan enkel geupate worden naar oude versies! ');
        }
    }
    

}
