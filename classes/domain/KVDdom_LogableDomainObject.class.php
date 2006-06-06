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

abstract class KVDdom_LogableDomainObject extends KVDdom_ChangeableDomainObject implements KVDdom_Nullable
{

    /**
     * Systemfields object dat eigenaar, versie, e.d. bijhoudt.
     * @var KVDdom_SystemFields
     */
    protected $_systemFields;

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
    }

    /**
     * Markeert dit object als Approved
     *
     * Dit record zal door KVDdom_Sessie worden goedgeurd in de databank bij het verwerken van de UnitOfWork. Dit komt neer op het uitvoeren van een
     */
    protected function markApproved(  )
    {
        $this->_sessie->registerApproved( $this );
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

}
