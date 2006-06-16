<?php
/**
 * @package KVD.agavi
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Dit is een helper-class om een domainObject snel te kunnen renderen naar html.
 * 
 * @package KVD.agavi
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDag_DomainObjectToHtml {

    /**
     * @var WebController
     */
    private $_controller;

    /**
     * @var KVDhtml_LinkHelper
     */
    private $_htmlLinkHelper;
    
    /**
     * @var KVDhtml_TableHelper
     */
    private $_htmlTableHelper;

    /**
     * @var string
     */
    private $standardModule;

    /**
     * @var array
     */
    private $config;
    
    /**
     * @param WebController $ctrl
     * @param array $config
     */
    public function __construct ( $ctrl , $standardModule, $config)
    {
        $this->_controller = $ctrl;

        $this->_htmlTableHelper = New KVDhtml_TableHelper();
        $this->_htmlTableHelper->setLijst(false);
        
        $this->_htmlLinkHelper = New KVDhtml_LinkHelper();

        $this->standardModule = $standardModule;
        
        $this->config = $config;
    }

    /**
     * Controleert of de huidige gebruiker de nodige credential heeft.
     * @param array $action
     * @return boolean
     */
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
     * Controleer of het domainObject Null is.
     * Indien het domainObject de interface KVDdom_Nullable niet implementeert wordt er van uitgegaan dat het niet null is.
     * @param KVDdom_DomainObject $domainObject
     * @return true
     */
    private function checkDomainObjectIsNull ( $domainObject )
    {
        if ( !$domainObject instanceof KVDdom_Nullable ) {
            return false;
        } else {
            return $domainObject->isNull( );
        }
    }

    /**
     * Controleer of aan de null voorwaarde voldaan is door $action['leeg'] te raadplegen.
     * Indien deze waarde niet ingesteld is wordt er verwacht dat de actie enkel moet doorgaan indien het domainObject niet leeg is.
     * @param array $action
     * @param KVDdom_DomainObject $domainObject
     * @return boolean Geeft aan of de actie moet getoond worden.
     */
    private function checkForNull( &$action, $domainObject )
    {
        if ( !isset( $action['leeg'] ) ) {
            $action['leeg'] = false;
        }
        try {
            $domainObjectElement = $this->getDomainObjectElement ( $action, $domainObject );
            return $action['leeg'] == $this->checkDomainObjectIsNull( $domainObjectElement );
        } catch ( UnexpectedValueException $e ) {
            return $action['leeg'] == true;
        }
    }

    /**
     * Controleer of deze actie al dan niet voor gecontroleerde records moet uitgevoerd worden.
     * @param array $action
     * @param KVDdom_DomainObject $domainObject
     * @return boolean Geeft aan of de actie moet getoond worden.
     */
    private function checkForGecontroleerd ( &$action, $domainObject )
    {
        if ( !isset( $action['gecontroleerd'] ) ) {
            return true;
        }
        if ( !$domainObject instanceof KVDdom_LogableDomainObject ) {
            throw new InvalidArgumentException ( 'Ongeldige configuratie. Het te controleren object is geen logbaar domainObject en heeft dus geen gecontroleerd status.');
        }
        return $action['gecontroleerd'] == $domainObject->getSystemFields( )->getGecontroleerd( );
    }

    /**
     * Controleer of deze actie enkel voor het huidige record moet uitgevoerd worden.
     * @param array $action
     * @param KVDdom_DomainObject $domainObject
     * @return boolean Geeft aan of de actie moet getoond worden.
     */
    private function checkForCurrentRecord( &$action, $domainObject )
    {
        if ( !isset( $action['currentRecord'] ) ) {
            return true;
        }
        if ( !$domainObject instanceof KVDdom_LogableDomainObject ) {
            throw new InvalidArgumentException ( 'Ongeldige configuratie. Het te controleren object is geen logbaar domainObject en heeft dus geen isCurrentRecord status.');
        }
        return $action['currentRecord'] == $domainObject->getSystemFields( )->isCurrentRecord( );
    }

    /**
     * @param array $action
     * @param KVDdom_DomainObject $domainObject
     * @return KVDdom_DomainObject
     */
    private function getDomainObjectElement( &$action , $domainObject )
    {
        if ( !isset( $action['domainObjectElement'] ) ) {
            throw new InvalidArgumentException( 'Ongeldige configuratie. Er moet een element zijn dat aangeeft welk domainObject verantwoordelijk is voor de koppeling. De actie die niet verwerkt kon worden: ' .$action['action']);
        }
        $returnObject = $this->getDataForFieldString( $domainObject , $action['domainObjectElement'] );
        if ( !$returnObject instanceof KVDdom_DomainObject ) {
            throw new UnexpectedValueException ( 'Het gevraagde domainObjectElement ' . $action['domainObjectElement'] . ' is zelf geen domainObject!');
        }
        return $returnObject;
    }

    /**
     * @param array $action
     * @return array
     */
    private function determineAction (  &$action , $domainObject )
    {
        if ( !is_array (  $action['action'] ) ) {
            $action['action'] = array ( AG_MODULE_ACCESSOR => $this->standardModule,
                                        AG_ACTION_ACCESSOR => $action['action']);
        }
        if ( isset( $action['needsId']) && $action['needsId'] == true ){
            if ( isset( $action['idField']) ) {
                $id = $this->getDataForFieldString( $domainObject , $action['idField']);
            } else {
                $id = $this->getDomainObjectElement( $action, $domainObject )->getId( );
            }
            $action['action']['id'] = $id;
        }
        return $action['action'];
    }

    /**
     * @param array $action
     * @return string
     */
    private function determineTarget( &$action )
    {
        return isset ( $action['target'] ) ? $action['target'] : '';
    }

    /**
     * @param KVDdom_DomainObject $domainObject
     * @param boolean $generateActions Geeft aan of de actions ook gegenereerd moeten worden of enkel de veldwaarden.
     */
    public function genAllForDomainObject( $domainObject , $generateActions = true)
    {
        $this->_htmlTableHelper->clearRows( );
        $this->_htmlTableHelper->setHeaders(  array_keys( $this->config) );
        $this->genRowsForDomainObject( $domainObject , $generateActions );
        $this->setSystemFields( $domainObject );
    }

    /**
     * @param KVDdom_DomainObject $domainObject
     * @param boolean $generateActions Geeft aan of de actions ook gegenereerd moeten worden of enkel de veldwaarden.
     */
    private function genRowsForDomainObject( $domainObject , $generateActions = true)
    {
        foreach ( $this->config as &$fieldConfig ) {
            $row = array( );
            try {
                $row[] = $this->getDataForFieldString( $domainObject, $fieldConfig['field']);
            } catch ( RuntimeException $e ) {
                $row[] = 'Onbepaald';
            }
            if ( $generateActions && isset( $fieldConfig['actions'] ) ) {
                $row[] = $this->getLinks( $domainObject, $fieldConfig['actions'] );
            }
            $this->_htmlTableHelper->addRow( $row );
        }
    }

    /**
     * @param KVDdom_DomainObject $domainObject
     * @param string $fieldString
     * @throws <b>RuntimeException</b> Indien de gevraagde data niet geleverd kon worden.
     */
    private function getDataForFieldString( $domainObject, $fieldString)
    {
        $fields = explode(  '.',$fieldString );
        foreach ( $fields as $field) {
            if ( $domainObject instanceof KVDdom_DomainObject ) {
                $domainObject = $domainObject->$field(   );
            } else {
                throw new RuntimeException ( 'U probeert een waarde van een veld te bekomen dat geen waarde heeft en ook geen NullObject is.');
            }
        }
        return $domainObject;
    }

    /**
     * @param KVDdom_DomainObject $domainObject
     * @param array $actions
     * @return array
     */
    private function getLinks( $domainObject, &$actions )
    {
        if ( !is_array( $actions ) ) {
            throw new InvalidArgumentException( 'Functie getLinks moet een parameter actions van het type array ontvangen!' );
        }
        $links = array( );
        foreach( $actions as &$action ) {
            if ( $this->checkCredential( $action ) ) {
                if (    $this->checkForNull( $action, $domainObject )  && 
                        $this->checkForCurrentRecord( $action, $domainObject ) &&
                        $this->checkForGecontroleerd( $action, $domainObject ) ) {
                    $links[$action['naam']] = $this->genLinkFromAction( $action , $domainObject);
                }
            }
        }
        return implode ( ' ' , $links );
    }

    /**
     * @param array $action
     * @param KVDdom_DomainObject $domainObject
     * @return string Html voorstelling van een link.
     */
    public function genLinkFromAction( &$action , $domainObject)
    {
        $parameters = $this->determineAction( $action , $domainObject);
        $target = $this->determineTarget( $action );
        $url = $this->_controller->genURL( null, $parameters );
        return $this->_htmlLinkHelper->genHtmlLink( $url,
                                                    $action['naam'],
                                                    $action['titel'],
                                                    '',
                                                    $target);
    }
    

    /**
     * Stel een verwijzing in naar het KVDdom_SystemFields object dat bij dit record hoort zodat er status-informatie kan afgedrukt worden.
     * @param KVDdom_LogableDomainObject
     */
    public function setSystemFields ( $domainObject )
    {
        if ( !$domainObject instanceof KVDdom_LogableDomainObject ) {
            return;
        }
        if ( $domainObject->isVerwijderd( )) {
            $recordSystemFields = '<span class="verwijderd">Verwijderd record</span>';
        } else {
            $systemFields = $domainObject->getSystemFields( );
            $recordUpdater = $this->getUpdaterLink( $systemFields->getGebruikersNaam( ) );
            $recordUpdateDatum = $systemFields->getBewerktOp();
            $recordVersie = $systemFields->getVersie();
            $recordGecontroleerd = $systemFields->getGecontroleerd() ? 'Gecontroleerd' : 'Nog niet gecontroleerd';
            $recordSystemFields = "Laatste wijziging door $recordUpdater op $recordUpdateDatum<br />Versie $recordVersie - $recordGecontroleerd";
        }
        $this->_htmlTableHelper->setFooter ( $recordSystemFields );
    }

    private function getUpdaterLink( $gebruikersNaam )
    {
        $parameters = array (   AG_MODULE_ACCESSOR =>   'Gebruiker',
                                AG_ACTION_ACCESSOR =>   'Gebruiker.TonenByGebruikersNaam',
                                'gebruikersNaam'   =>   $gebruikersNaam);
        $url = $this->_controller->genURL( null, $parameters );
        return $this->_htmlLinkHelper->genHtmlLink( $url,
                                                    $gebruikersNaam,
                                                    'De gebruiker bekijken');
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
