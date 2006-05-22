<?php
/**
 * @package KVD.agavi.domHtml
 * @subpackage Adres
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */
    
/**
 * @package KVD.agavi.domHtml
 * @subpackage Adres
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDag_AdrStraatToHtml extends KVDag_DoToHtml
{
    /**
     * @var KVDdo_AdrStraat
     */
    private $_straat;

    /**
     * @param Webcontroller $ctrl
     * @param KVDdo_AdrStraat $straat
     * @param string $moduleAccessor
     * @param string $actionAccessor
     */
    public function __construct ( $ctrl , $straat , $moduleAccessor = AG_MODULE_ACCESSOR, $actionAccessor = AG_ACTION_ACCESSOR)
    {
        parent::__construct( $ctrl , $moduleAccessor , $actionAccessor );    
        $this->_straat = $straat;
    }

    /**
     * @param array $cssClasses Een array van css-classes die moeten gegeven worden aan tabel-elementen.
     * @return string
     */
    public function toHtml( $cssClasses = null )
    {
        $gemeenteRow = $this->toHtmlGemeenteRow( $this->_straat->getGemeente( ) );
        $record = array (   'Id' => $this->_straat->getId() ,
                            'StraatNaam' => $this->_straat->getStraatNaam(),
                            'StraatLabel' => $this->_straat->getStraatLabel( ),
                            'Gemeente' => $gemeenteRow
                        );

        $this->_record->genRows ( $record );

        return $this->_record->toHtml( $cssClasses );
    }

    private function toHtmlGemeenteRow( $gemeente )
    {
        $action = array (   $this->moduleAccessor => 'Adres',
                            $this->actionAccessor => 'GemeenteTonen',
                            'id' => $gemeente->getId( ) );
        $gemeenteActions = array( );
        $gemeenteActions[] = array (   'action'    => $action,
                                       'naam'      => 'Tonen',
                                       'titel'     => 'Deze gemeente tonen',
                                       'credential'=> 'Raadpleger');
        $gemeenteRow = array ( 'omschrijving'  => $gemeente->getGemeenteNaam( ),
                                'actions'       => $gemeenteActions);

        return $gemeenteRow;
    }

}
?>
