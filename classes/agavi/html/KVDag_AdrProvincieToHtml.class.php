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
class KVDag_AdrProvincieToHtml extends KVDag_DoToHtml
{
    /**
     * @var KVDdo_AdrProvincie
     */
    private $_provincie;

    /**
     * @param Webcontroller $ctrl
     * @param KVDdo_AdrProvincie $adres
     * @param string $moduleAccessor
     * @param string $actionAccessor
     */
    public function __construct ( $ctrl , $provincie , $moduleAccessor = AG_MODULE_ACCESSOR, $actionAccessor = AG_ACTION_ACCESSOR)
    {
        parent::__construct( $ctrl , $moduleAccessor , $actionAccessor );    
        $this->_provincie = $provincie;
    }

    /**
     * @param array $cssClasses Een array van css-classes die moeten gegeven worden aan tabel-elementen.
     * @return string
     */
    public function toHtml( $cssClasses = null )
    {
        $record = array (   'Id' => $this->_provincie->getId() ,
                            'Naam' => $this->_provincie->getProvincieNaam()
                        );

        $this->_record->genRows ( $record );

        return $this->_record->toHtml( $cssClasses );
    }

}
?>
