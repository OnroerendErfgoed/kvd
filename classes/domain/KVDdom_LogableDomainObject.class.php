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
     * @var KVDdom_SystemFields
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
     * @param KVDdom_SystemFields $systemFields
     */
    public function __construct ( $id , $sessie , $systemFields = null, $currentRecord = true )
    {
        $this->id = $id;
        $this->_sessie = $sessie;
        if ($systemFields === null) {
            $this->_systemFields = new KVDdom_SystemFields($this->_sessie->getGebruiker()->getGebruikersNaam());
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
     * Dit geschiedenis van dit record zal door KVDdom_Sessie worden doorgevoerd in de databank bij het verwerken van de UnitOfWork.
     */
    protected function markConfirmDelete(  )
    {
        $this->_sessie->registerConfirmDelete( $this );
    }

    /**
     * markUndoDelete 
     * 
     * Markeert dit object als UndoDelete.
     */
    protected function markUndoDelete( )
    {
        $this->_sessie->registerUndoDelete( $this );
    }
    
    
    
    /**
     * Geef het SystemFields object van dit DomainObject terug
     */
    public function getSystemFields()
    {
        return $this->_systemFields;
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

    /**
     * Controleer of het mogelijk is het huidige DomainObject te updaten naar de vorige versie.
     * @param KVDdom_LogableDomainObject
     */
    protected function checkPreviousVersion( $previous )
    {
        if ( !$previous->getClass( ) === $this->getClass( ) ) {
            throw new LogicException ( 'Kan enkel update naar een vorige versie van mezelf!' );
        }
        if ( !$this->isCurrentRecord( ) ) {
            throw new LogicException ( 'Dit object is niet de huidige versie en kan dus niet geupdate worden!');
        }
        if ( $previous->isCurrentRecord( ) ) {
            throw new LogicException ( 'Er kan enkel geupate worden naar oude versies! ');
        }
    }
    

}
