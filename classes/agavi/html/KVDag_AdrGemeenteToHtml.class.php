<?php
/**
 * @package KVD.agavi.domHtml
 * @subpackage Adres
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */
    
/**
 * @package KVD.ag.domHtml
 * @subpackage Adres
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDag_AdrGemeenteToHtml extends KVDag_DoToHtml
{
    /**
     * @var KVDdo_AdrGemeente
     */
    private $_gemeente;

    /**
     * @param Webcontroller $ctrl
     * @param KVDdo_AdrGemeente $gemeente
     * @param string $moduleAccessor
     * @param string $actionAccessor
     */
    public function __construct ( $ctrl , $gemeente , $moduleAccessor = AG_MODULE_ACCESSOR, $actionAccessor = AG_ACTION_ACCESSOR)
    {
        parent::__construct( $ctrl , $moduleAccessor , $actionAccessor );    
        $this->_gemeente = $gemeente;
    }

    /**
     * @param array $cssClasses Een array van css-classes die moeten gegeven worden aan tabel-elementen.
     * @return string
     */
    public function toHtml( $cssClasses = null )
    {
        $provincieRow = $this->toHtmlProvincieRow( $this->_gemeente->getProvincie( ) );
        $record = array (   'Id' => $this->_gemeente->getId() ,
                            'Naam' => $this->_gemeente->getGemeenteNaam(),
                            'Provincie' => $provincieRow
                        );

        $this->_record->genRows ( $record );

        return $this->_record->toHtml( $cssClasses );
    }

    private function toHtmlProvincieRow( $provincie )
    {
        $action = array (   $this->moduleAccessor => 'Adres',
                            $this->actionAccessor => 'ProvincieTonen',
                            'id' => $provincie->getId( ) );
        $provincieActions = array( );
        $provincieActions[] = array (   'action'    => $action,
                                        'naam'      => 'Tonen',
                                        'titel'     => 'Deze provincie tonen',
                                        'credential'=> 'Raadpleger');
        $provincieRow = array ( 'omschrijving'  => $provincie->getProvincieNaam( ),
                                'actions'       => $provincieActions);

        return $provincieRow;
    }

}
?>
