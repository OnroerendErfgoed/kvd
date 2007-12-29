<?php
/**
 * @package KVD.util
 * @subpackage dimensies
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */
 
/**
 * Utlity om dimensies van de ene maateenheid naar de andere over te zetten
 *
 * @package KVD.util
 * @subpackage dimensies
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
class KVDutil_DimensieConvertor
{
    /**
     * @var array
     */
    private $conversieFactoren;

    public function __construct ()
    {
        $this->conversieFactoren = array (  'km' => array ( 'km' => 1, 'm' => 1000, 'dm' => 10000, 'cm' => 100000, 'mm' => 1000000 ),
                                            'm'  => array ( 'km' => 0.001, 'm' => 1, 'dm' => 10, 'cm' => 100, 'mm' => 1000 ),
                                            'dm' => array ( 'km' => 0.0001, 'm' => 0.1, 'dm' => 1, 'cm' => 10, 'mm' => 100),
                                            'cm' => array ( 'km' => 0.00001, 'm' => 0.01, 'dm' => 0.1, 'cm' => 1, 'mm' => 10),
                                            'mm' => array ( 'km' => 0.000001, 'm' => 0.001, 'dm' => 0.01, 'cm' => 0.1, 'mm' => 1),
                                            'kg' => array ( 'kg' => 1, 'gr' => 1000),
                                            'gr' => array ( 'kg' => 0.001, 'gr' => 1 )
                                          );
    }

    /**
     * @param integer $dimensie Numerieke waarde die moet geconverteerd worden.
     * @param string brondimensie Maateenheid die geconverteerd moet worden.
     * @param string doeldimensie Maateenheid waarnaar geconverteerd  moet worden.
     * @throws Exception
     * @return integer
     */
    public function convertDimensie ( $dimensie , $brondimensie , $doeldimensie )
    {
        if (array_key_exists ( $brondimensie , $this->conversieFactoren)) {
            if (array_key_exists ( $doeldimensie , $this->conversieFactoren[$brondimensie]) ) {
                return $dimensie * $this->conversieFactoren[$brondimensie][$doeldimensie];
            } else {
                $geldigeDimensies = implode ( ', ' , array_keys ( $this->conversieFactoren[$brondimensie] ) );
                $msg =  "Onmogelijk te converteren van $brondimensie naar $doeldimensie. Geldige conversies voor $brondimensie zijn $geldigeDimensies.";
            }
        } else {
            $geldigeDimensies = implode ( ', ' , array_keys ( $this->conversieFactoren ) );
            $msg = "Onmogelijk $brondimensie te converten. Dimensies die kunnen geconverteerd worden zijn $geldigeDimensies.";
        }
        throw new Exception ( $msg );
    }
}

?>
