<?php
/**
 * @package KVD.util
 * @subpackage dimensies
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Utlity die namen en afkortingen van dimensies bevat.
 *
 * @package KVD.util
 * @subpackage dimensies
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDutil_DimensieNaamAfkorting
{
    /**
     * @var array
     */
    private $namenEnAfkorting;

    public function __construct ()
    {
        $this->namenEnAfkortingen = array ( 'lengte' => 'L',
                                            'breedte' => 'B',
                                            'hoogte' => 'H',
                                            'dikte' => 'D',
                                            'diameter' => 'Diam',
                                            'gewicht' => 'G',
                                          );
    }

    /**
     * @param string $dimensieNaam Soort dimensie (bv. lengte).
     * @return string
     */
    public function convertDimensieNaamNaarAfkorting ( $dimensieNaam )
    {
        if (isset ( $this->namenEnAfkortingen[$dimensieNaam] ) ) {
            return $this->namenEnAfkortingen[$dimensieNaam];
        } else {
            $geldigeDimensies = implode ( ', ' , array_keys ( $this->namenEnAfkortingen ) );
            $msg = "Onmogelijk $dimensieNaam te converten. Dimensienamen waarvoor een afkorting bestaat zijn $geldigeDimensies.";
            throw new Exception ( $msg );
        }
    }

    /**
     * @param string $dimensieAfkorting Afkorting van een soort dimensie (bv. L).
     * @return string
     */
    public function convertDimensieAfkortingNaarNaam ( $dimensieAfkorting )
    {
        if ( in_array ( $dimensieAfkorting , $this->namenEnAfkortingen ) ) {
            $key = array_keys ( $this->namenEnAfkortingen , $dimensieAfkorting );
            return $key[0];
        } else {
            $geldigeDimensies = implode ( ', ' , array_values ( $this->namenEnAfkortingen ) );
            $msg = "Onmogelijk $dimensieAfkorting te converten. Dimensieafkortingen waarvoor een naam bestaat zijn $geldigeDimensies.";
            throw new Exception ( $msg );
        }
    }
}
?>
