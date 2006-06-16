<?php
/**
 * @package KVD.agavi
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id
 */

/**
 * @package KVD.agavi
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
abstract class KVDag_AbstractHelper
{
    /**
     * @param KVDdom_DomainObject $domainObject
     * @param string $fieldString
     * @throws <b>RuntimeException</b> Indien de gevraagde data niet geleverd kon worden.
     */
    protected function getDataForFieldString( $domainObject, $fieldString)
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
     * @param array $action
     * @param KVDdom_DomainObject $domainObject
     * @return string Html voorstelling van een link.
     */
    protected function genLinkFromAction( $action , $domainObject)
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
     * @param mixed $action Kan een string of een array zijn.
     * @return array
     */
    protected function determineAction (  $action , $domainObject )
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
    protected function determineTarget( $action )
    {
        return isset ( $action['target'] ) ? $action['target'] : '';
    }

    /**
     * Controleer of deze actie al dan niet voor gecontroleerde records moet uitgevoerd worden.
     * @param array $action
     * @param KVDdom_DomainObject $domainObject
     * @return boolean Geeft aan of de actie moet getoond worden.
     */
    protected function checkForGecontroleerd ( $action, $domainObject )
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
    protected function checkForCurrentRecord( &$action, $domainObject )
    {
        if ( !isset( $action['currentRecord'] ) ) {
            return true;
        }
        if ( !$domainObject instanceof KVDdom_LogableDomainObject ) {
            throw new InvalidArgumentException ( 'Ongeldige configuratie. Het te controleren object is geen logbaar domainObject en heeft dus geen isCurrentRecord status.');
        }
        return $action['currentRecord'] == $domainObject->getSystemFields( )->isCurrentRecord( );
    }
}
?>
