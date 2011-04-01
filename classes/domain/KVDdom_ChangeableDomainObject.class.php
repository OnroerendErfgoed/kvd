<?php
/**
 * @package     KVD.dom
 * @version     $Id$
 * @copyright   2006-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
 
/**
 * KVDdom_ChangeableDomainObject 
 * 
 * DomainObjects die gewijzigd kunnen worden.
 * Het wijzigen van een DomainObject gaat altijd via de UnitOfWork die in het KVDdom_Sessie object zit.
 * @package     KVD.dom
 * @since       2006
 * @copyright   2006-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDdom_ChangeableDomainObject implements KVDdom_DomainObject, KVDdom_Nullable 
{

    /**
     * Een constante om aan te geven dat een bepaald veld nog geladen moet worden.
     */
    const PLACEHOLDER = "TE LADEN";

    /**
     * Id nummer van het domain-object
     * @var integer
     */
    protected $id;
    
    /**
     * Verwijzing naar het KVDdom_Sessie object
     * @var KVDdom_Sessie
     */
    protected $_sessie;

    /**
     * systemFields 
     * 
     * @var KVDdom_ChangeableSystemFields
     */
    protected $systemFields;

    /**
     * fields 
     * 
     * Een optionele array van @link{KVDdom_Fields_AbstractField} objecten.
     * @var     array
     */
    protected $fields = array( );
    
    /**
     * Maak het KVDdom_DomainObject
     * @param integer $id Id dat aan het nieuwe KVDdom_DomainObject moet gegeven worden.
     * @param KVDdom_Sessie $sessie 
     * @param KVDdom_ChangeableSystemFields $systemFields
     */
    public function __construct ( $id , $sessie , $systemFields = null) 
    {
        $this->_sessie = $sessie;
        $this->id = $id;
        $this->systemFields = $systemFields;
        $this->configureFields( );
        $this->markClean();
    }

    /**
     * Geeft het Id nummer van dit object terug.
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Geef het type van een DomainObject terug. Onder andere nodig om de (@link KVDdom_DataMapper) te kunnen vinden.
     * @return string
     */
    public function getClass()
    {
        return get_class( $this );
    }

    /**
     * configureFields 
     * 
     * Deze methode dient om de velden te configurern
     * Methode die moet overschreven worden in concrete 
     * domainobjecten.
     * @return  boolean Is het configureren geslaagd of niet. 
     */
    protected function configureFields()
    {
        return true;
    }

    /**
     * initializeFields 
     * 
     * Stel de startwaarden in voor elk veld. Dit zal er NIET toe leiden dat 
     * een object als dirty gemarkeerd wordt.
     * @param   array   $data   Een array met als sleutel de naam van een veld 
     *                          en als waarde de startwaarde voor dat veld.
     * @return  void
     */
    protected function initializeFields( array $data )
    {
        foreach ( $data as $key => $val ) {
            if ( isset( $this->fields[$key] ) ) {
                $this->fields[$key]->initializeValue( $val );
            } else {
                throw new KVDdom_Fields_Exception( 
                    sprintf( 'U probeert een startwaarde in te stellen voor een niet bestaand veld ( %s ).',
                    $key ) );
            }
        }
    }

    /**
     * __call 
     * 
     * Deze methode probeert te detecteren of er een magische get, set, add, 
     * remove of clear methode wordt aangeroepen.
     *
     * @since   maart 2010
     * @param   string  $name   Naam van de methode die werd aangeroepen.
     * @param   array   $args   Argumenten die werden meegegeven aan de 
     *                          methode.
     * @return void
     */
    public function __call($name, array $args )
    {
		$matches = array();
		if(preg_match('/^(get|set|add|remove|clear)(.+)$/', $name, $matches)) {
			$property = strtolower(preg_replace('/((?<!\A)[A-Z])/u', '_$1', $matches[2]));
            if ( $matches[1] == 'add' || $matches[1] == 'remove' ) {
                $property = $this->pluralize( $property );
                if ( !$property ) {
                    throw new KVDdom_Fields_Exception( 'U probeert een bewerking 
                        uit te voeren op een collection, maar de naam van de collection 
                        kon niet gevonden worden. Mogelijk moet u de pluralize methode aanpassen.' );
                }
            }
            if ( !isset( $this->fields[$property] ) ) {
                throw new KVDdom_Fields_Exception ( 'U probeert een bewerking uit te voeren met het veld ' 
                                                    . $property . ', maar dit veld bestaat niet.' );
            }
            switch ($matches[1]) {
                case 'get':
                    return $this->fields[$property]->getValue( );
                case 'set':
                    return $this->fields[$property]->setValue($args[0]);
                case 'clear':
                    return $this->fields[$property]->clear( );
                case 'add':
                    return $this->fields[$property]->add( $args[0] );
                case 'remove':
                    return $this->fields[$property]->remove( $args[0] );
            }
		} else {
            throw new KVDdom_Exception( 'U probeert een methode ' . $name . ' op te roepen die niet bestaat.' );
        }
    }

    /**
     * pluralize 
     * 
     * @since   maart 2010
     * @param   string  $property   Enkelvoudige property naam waarvoor een 
     *                              meervoud moet gevonden worden.
     * @return  mixed   string of boolean. Ofwel de meervoudige naam van de 
     *                  property of false indien er geen meervoud kon gevonden 
     *                  worden voor de naam.
     */
    protected function pluralize( $property )
    {
        return false;
    }

    /**
     * markFieldAsDirty 
     * 
     * Geef aan dat een veld gewijzigd is. Deze methode mag enkel aangeroepen 
     * worden door een field zelf. Om dit te bewijzen geeft het field zichzelf 
     * mee als argument. Zonder deze methode zouden we de markDirty methode 
     * public moeten maken en dat wouden we verhinderen.
     *
     * @since   23 maart 2010
     * @param   KVDdom_Fields_AbstractField $field 
     * @throws  KVDdom_Fields_Exception
     * @return  void
     */
    public function markFieldAsDirty( KVDdom_Fields_AbstractField $field )
    {
        if ( isset( $this->fields[$field->getName(  )] ) ) {
            $this->markDirty( );
        } else {
            throw new KVDdom_Fields_Exception( 'U probeert een niet-bestaand veld als dirty te markeren!' );
        }
    }
    
    /**
     * Markeert dit object als Clean
     *
     * Dit record zal door (@link KVDdom_Sessie) niet worden opgeslaan bij het verwerken van de UnitOfWork.
     */
    protected function markClean()
    {
       $this->_sessie->registerClean($this);
    }

    /**
     * Markeert dit object als New
     *
     * Dit record zal door KVDdom_Sessie worden opgeslaan bij het verwerken van de UnitOfWork. Dit komt neer op het uitvoeren van een SQL INSERT statement.
     */
    protected function markNew()
    {
        $this->_sessie->registerNew($this);
    }

    /**
     * Markeert dit object als Dirty
     *
     * Dit record zal door KVDdom_Sessie worden opgeslaan bij het verwerken van de UnitOfWork. Dit komt neer op het uitvoeren van een SQL UPDATE statement.
     */
    protected function markDirty()
    {
        $this->_sessie->registerDirty($this);
    }

    /**
     * Markeert dit object als Removed
     *
     * Dit record zal door KVDdom_Sessie worden verwijderd uit de databank bij het verwerken van de UnitOfWork. Dit komt neer op het uitvoeren van een SQL DELETE statement.
     */
    protected function markRemoved()
    {
        $this->_sessie->registerRemoved($this);
    }

    /**
     * Maakt een nieuw KVDdom_ChangeableDomainObject aan dat niet uit de databank wordt geladen
     *
     * Deze methode werd uit de interface gehaald omdat sommige subtypes een 
     * create methode moeten kunnen gebruiken die ook andere parameters kan 
     * aanvaarden. Momenteel kon dit niet en dit leidde tot E_STRICT fouten.
     * @param   integer         $id     Het Id nummer voor het nieuwe object. Meestal wordt dit 
     *                                  aangereikt door de DataMapper die er voor moet zorgen dat 
     *                                  dit nummer uniek is binnen het type object.
     * @param   KVDdom_Sessie   $sessie Het sessie object. Of een ander object dat de 
     *                                  Unit Of Work implementeert
     * @return KVDdom_ChangeableDomainObject
     */
    /*
	static function create( $id , $sessie )
    {
        throw new Exception ( 'This method should only be called on a concrete implementation.' );
    }
    */

    /**
     * Verwijdert dit object uit het domein
     */
    abstract function remove();

    /**
     * @return boolean
     */
    public function isNull( )
    {
        return false;
    }

    /**
     * hasSystemFields 
     * 
     * @return boolean
     */
    public function hasSystemFields( )
    {
        return !is_null( $this->systemFields );
    }

    /**
     * getSystemFields 
     * 
     * Geeft het systemFields object terug of null indien er geen is.
     * @return KVDdom_ChangeableSystemFields
     */
    public function getSystemFields( )
    {
        return $this->systemFields;
    }


    /**
     * __toString 
     * 
     * @return string
     */
    public function __toString( )
    {
        return $this->getOmschrijving( );
    }

}
?>
