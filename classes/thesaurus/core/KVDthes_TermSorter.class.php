<?php
/**
 * @package     Kvd.Thes
 * @subpackage  Core
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
 

/**
 * KVDthes_TermSorter 
 * 
 * @package KVD.thes
 * @subpackage Core
 * @since 13 april 2010
 * @copyright 2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Dieter Standaert <dieter.standaert@hp.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

class KVDthes_TermSorter
{
    /**
     * Constante om aan te geven dat we niet willen sorteren. 
     */
    const SORT_UNSORTED = 0;
    
    /**
     * Constante om aan te geven dat we willen sorteren op id. 
     */
    const SORT_ID = 1;

    /**
     * Constante om aan te geven dat we willen sorteren op de term. 
     */
    const SORT_TERM = 2;

    /**
     * Constant om aan te geven dat we willen sorteren op de Qualified Term. 
     */
    const SORT_QUALTERM = 3;

    /**
     * Constante om aan te geven dat we willen sorteren op de SortKey van een term. 
     */
    const SORT_SORTKEY = 4;
   
    /**
     * Map om aan te geven met welke compare methode moet gewerkt worden voor een
     * bepaalde sorteervolgorde.
     */
    public static $methodMap =  array (  self::SORT_UNSORTED => null,
                                        self::SORT_ID => 'compareNumber',
                                        self::SORT_TERM => 'compareString',
                                        self::SORT_QUALTERM => 'compareString',
                                        self::SORT_SORTKEY => 'compareString' );
 
    /**
     * Map om aan te geven met welke compare methode moet gewerkt worden voor een
     * bepaalde sorteervolgorde.
     */
    public static $fieldMap =  array (  self::SORT_UNSORTED => null,
                                        self::SORT_ID => 'getId',
                                        self::SORT_TERM => 'getTerm',
                                        self::SORT_QUALTERM => 'getQualifiedTerm',
                                        self::SORT_SORTKEY => 'getSortKey' );
 
    
    
    
    private $sortMethod;
    
    public function __construct($method)
    {
        $this->sortMethod = $method;
    }
    
    

    /**
     * compareRelations 
     * 
     * @param   string              $comparedMethod     Methode van het domainobject die dient om te vergelijken.
     * @param   KVDthes_Relation    $a 
     * @param   KVDthes_Relation    $b 
     * @return  integer                                 -1, 0 of 1 indien $a respectievelijk kleiner dan, gelijk aan of groter dan $b is.
     */
    public function compareRelations( KVDthes_Relation $a, KVDthes_Relation $b )
    {
        return $this->compareTerms($a->getTerm( ), $b->getTerm( ) );
    }
 
    /**
     * compareTerms 
     * 
     * @param   string              $comparedMethod     Methode van het domainobject die dient om te vergelijken.
     * @param   KVDthes_Term    $a 
     * @param   KVDthes_Term    $b 
     * @return  integer                                 -1, 0 of 1 indien $a respectievelijk kleiner dan, gelijk aan of groter dan $b is.
     */
    public function compareTerms( KVDthes_Term $a, KVDthes_Term $b )
    {
        $compareMethod = self::$methodMap[$this->sortMethod];
        $compareField = self::$fieldMap[$this->sortMethod];
        return $this->$compareMethod ($compareField, $a , $b );
    }   


    /**
     * compareId 
     * 
     * @param   string $method 
     * @param   KVDthes_Relation $a 
     * @param   KVDthes_Relation $b 
     * @return  integer
     */
    public static function compareNumber($method, KVDthes_Term $a, KVDthes_Term $b )
    {
        if ( $a->$method( ) < $b->$method( ) ) return -1;
        if ( $a->$method( ) > $b->$method( ) ) return 1;
        /**
         * Normaal geraken we hier niet aangezien een relatieset niet 2 keer 
         * hetzelfde object kan bevatten.
         * @codeCoverageIgnoreStart
         */
        return 0;
        // @codeCoverageIgnoreEnd
    }
    
        
    /**
     * compareString 
     * 
     * @param   string $method 
     * @param   KVDthes_Relation $a 
     * @param   KVDthes_Relation $b 
     * @return  integer
     */
    public static function compareString($method, KVDthes_Term $a, KVDthes_Term $b )
    {
        return strcmp($a->$method(), $b->$method());    
    }




}

?>

