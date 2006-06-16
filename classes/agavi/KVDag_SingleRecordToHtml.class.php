<?php
/**
 * @package KVD.agavi
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Dit is een helper-class om een record van een tabel snel te kunnen renderen naar html.
 * 
 * @package KVD.agavi
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 * @deprecated
 */
class KVDag_SingleRecordToHtml {

    /**
     * @var WebController
     */
    protected $_controller;

    /**
     * @var KVDhtml_LinkHelper
     */
    protected $_htmlLinkHelper;
    /**
     * @var KVDhtml_TableHelper
     */
    protected $_htmlTableHelper;

    /**
     * @param WebController $ctrl
     */
    public function __construct ( $ctrl )
    {
        $this->_controller = $ctrl;
        
        $this->_htmlTableHelper = New KVDhtml_TableHelper();
        $this->_htmlTableHelper->setLijst(false);
        
        $this->_htmlLinkHelper = New KVDhtml_LinkHelper();
    }

    /**
     * Stel de headers in.
     *
     * Bedoel voor records die speciale functionaliteit nodig hebben. Gebruik zo veel mogelijk genRows(). Mogelijk wordt deze functie in de toekomst 'geprivatiseerd'.
     * @param array $headers
     */
    public function setHeaders ( $headers )
    {
        $this->_htmlTableHelper->setHeaders ( $headers );
    }

    /**
     * Stel de te tonen rijen in.
     * 
     * Bedoel voor records die speciale functionaliteit nodig hebben. Gebruik zo veel mogelijk genRows(). Mogelijk wordt deze functie in de toekomst 'geprivatiseerd'.
     * @param array $rows
     */
    public function setRows ( $rows )
    {
        foreach ($rows as &$row) {
            if ( is_array ($row) && array_key_exists ( 'actions' , $row) ) {
                foreach ($row['actions'] as &$action) {
                    if ( $this->checkCredential( $action) ) {
                        $parameters = $action['action'];
                        $row[] = $this->_htmlLinkHelper->genHtmlLink (  $this->_controller->genURL(null, $parameters),
                                                                        $action['naam'],
                                                                        $action['titel']);
                    }
                }
                unset ($row['actions']);
            }
            
        }
        $this->_htmlTableHelper->setRows ( $rows );
    }

    private function checkCredential( &$action )
    {
        if ( !array_key_exists( 'credential', $action) ) {
            return true;
        } else {
            if ( !( $this->_controller->getContext( )->getUser( ) instanceof BasicSecurityUser) ) {
                throw new Exception ( 'U kunt enkel maar credentials toewijzen indien er met een BasicSecurityUser gewerkt wordt.');
            }
            if ( $this->_controller->getContext( )->getUser( )->hasCredential( $action['credential'] ) ) {
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Genereer de rijen van het record.
     *
     * $recordConfig is een array dat aan bepaalde regels moet voldoen:
     *  - De array sleutels zullen gebruikt worden als headers.
     *  - De array waarden zijn ofwel een waarde, ofwel een array.
     *  - Indien de array-waarde een waarde is, bv. 'test' dan wordt dit beschouwd als de veldwaarde.
     *  - Indien de array-waarde een array is, wordt er op zoek gegaan naar een array-key 'actions'. Alle andere array-keys blijven gewoon behouden. De 'actions' worden omgezet naar html links.
     * Een voorbeeld van $recordConfig:
     * <code>
     *  $recordConfig = array ( 'Id' => 15,
     *                          'Naam' => 'Rubens',
     *                          'Voornaam' => 'Pieter Paul',
     *                          'Adres' => array ( 'omschrijving' => 'Groenplaats 15, 2000 Antwerpen',
     *                                             'actions' => array ( 0 => array (  'action' => array (  'module'    => 'Gebruiker',
     *                                                                                                      'action'    => 'AdresTonen',
     *                                                                                                      'id'        => 245),
     *                                                                                 'naam' => 'Tonen',
     *                                                                                 'titel' => 'Het adres van deze persoon tonen',
     *                                                                                 'credential' => 'Raadpleger'),
     *                                                                  1 => array (    'action' => array ( 'module'    => 'Gebruiker',
     *                                                                                                      'action'    => 'AdresBewerken',
     *                                                                                                      'id'        => 245),
     *                                                                                  'naam'  =>  'Bewerken',
     *                                                                                  'titel' =>  'Het adres bewerken.',
     *                                                                                  'credential' => 'Invoerder')
     *                                                                )
     *                                           )
     *                          )
     * </code>
     * @param array $recordConfig
     */
    public function genRows ( $recordConfig )
    {
       $this->setHeaders ( array_keys ( $recordConfig) );
       $this->setRows ( array_values ( $recordConfig) );
    }

    /**
     * Stel een verwijzing in naar het KVDdom_SystemFields object dat bij dit record hoort zodat er status-informatie kan afgedrukt worden.
     * @param KVDdom_SystemFields
     */
    public function setSystemFields ( $systemFields = null )
    {
        $recordUpdater = $systemFields->getGebruikersNaam( );
        $recordUpdateDatum = $systemFields->getBewerktOp();
        $recordVersie = $systemFields->getVersie();
        $recordGecontroleerd = $systemFields->getGecontroleerd() ? 'Goedgekeurd' : 'Nog niet gecontroleerd';
        $recordSystemFields = "Laatste wijziging door $recordUpdater op $recordUpdateDatum.<br />Versie $recordVersie. $recordGecontroleerd.";
        $this->_htmlTableHelper->setFooter ( $recordSystemFields );
    }

    /**
     * @param array $cssClasses
     * @see KVDhtml_TableHelper::setCssClasses()
     * @return string
     */
    public function toHtml ( $cssClasses = null )
    {
        if (!is_null($cssClasses)) {
            $this->_htmlTableHelper->setCssClasses($cssClasses);
        }
        return $this->_htmlTableHelper->toHtml();
    }

}
?>
