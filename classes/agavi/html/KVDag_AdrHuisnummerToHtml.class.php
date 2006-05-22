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
class KVDag_AdrHuisnummerToHtml extends KVDag_DoToHtml
{
    /**
     * @var KVDdo_AdrHuisnummer
     */
    private $_huisnummer;

    /**
     * @param Webcontroller $ctrl
     * @param KVDdo_AdrHuisnummer $huisnummer
     * @param string $moduleAccessor
     * @param string $actionAccessor
     */
    public function __construct ( $ctrl , $huisnummer , $moduleAccessor = AG_MODULE_ACCESSOR, $actionAccessor = AG_ACTION_ACCESSOR)
    {
        parent::__construct( $ctrl , $moduleAccessor , $actionAccessor );    
        $this->_huisnummer = $huisnummer;
    }

    /**
     * @param array $cssClasses Een array van css-classes die moeten gegeven worden aan tabel-elementen.
     * @return string
     */
    public function toHtml( $cssClasses = null )
    {
        $gemeenteRow = $this->toHtmlGemeenteRow( $this->_huisnummer->getStraat( )->getGemeente( ) );
        $straatRow = $this->toHtmlStraatRow ( $this->_huisnummer->getStraat( ) );
        $record = array (   'Id' => $this->_huisnummer->getId() ,
                            'Huisnummer' => $this->_huisnummer->getHuisnummer(),
                            'Straat' => $straatRow,
                            'Postcode' => $this->_huisnummer->getPostcode( ),
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

    private function toHtmlStraatRow( $straat )
    {
        $action = array (   $this->moduleAccessor => 'Adres',
                            $this->actionAccessor => 'StraatTonen',
                            'id' => $straat->getId( ) );
        $straatActions = array( );
        $straatActions[] = array (  'action'    => $action,
                                    'naam'      => 'Tonen',
                                    'titel'     => 'Deze straat tonen',
                                    'credential'=> 'Raadpleger');
        $straatRow = array ( 'omschrijving'  => $straat->getStraatLabel( ),
                             'actions'       => $straatActions);

        return $straatRow;
    }

}
?>
