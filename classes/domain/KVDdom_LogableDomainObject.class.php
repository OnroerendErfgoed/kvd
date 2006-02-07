<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDdom_LogableDomainObject.class.php,v 1.1 2006/01/12 14:46:02 Koen Exp $
 */
 
/**
 * DomainObjects die gelogd kunnen worden.
 *
 * Het loggen houdt in dat er voor elk record een gebruiker, wijziginsdatum en versie wordt bijgehouden. Van elke wijziging wordt dan ook nog bijgehouden of ze al dan niet is goedgekeurd door de redactie.
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */

abstract class KVDdom_LogableDomainObject extends KVDdom_ChangeableDomainObject
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
        if ($systemFields === null) {
            $this->_systemFields = new KVDdom_SystemFields($this->_sessie->getGebruiker());
        } else {
            $this->_systemFields = $systemFields;
        }
    }
    
    /**
     * Geef het SystemFields object van dit DomainObject terug
     */
    public function getSystemFields()
    {
        return $this->_systemFields;
    }

}
