<?php
/**
 * DomainObject voor KeuzeLijsten en andere simpele objecten.
 *
 * @package     KVD.dom
 * @version     $Id$
 * @copyright   2006-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * DomainObject voor o.a. KeuzeLijsten.
 *
 * Een class die door alle value domein-objecten van een applicatie geerfd wordt. 
 * Meestal zijn dit de waarden die in de keuzelijsten zitten. 
 * DataMappers voor deze objecten zouden in DataMappers van de objecten die verwijzen naar de keuzelijst moeten zitten, 
 * tenzij het om een keuzelijst gaat die door veel verschillende tabellen geraadpleegd wordt. Dan wordt er een aparte DM aangemaakt.
 * Ingewikkelde keuzelijsten die ook nog veel andere data bevatten gebruiken best de KVDdom_ReadonlyDomainObject class als superclass.
 * @package     KVD.dom
 * @since       1.0.0
 * @copyright   2006-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDdom_ValueDomainObject implements KVDdom_DomainObject
{
    /**
     * Id nummer van het domainObject
     * @var integer
     */
    protected $id;

    /**
     * fields 
     * 
     * Een optionele array van @link{KVDdom_Fields_AbstractField} objecten.
     * @var     array
     */
    protected $fields = array( );

    /**
     * @param integer $id Id nummer van het object.
     */
    public function __construct ( $id )
    {
        $this->id = $id;
        $this->configureFields( );
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
     * Geef het type van een Domain Object terug. Onder andere nodig om de DataMapper te kunnen vinden.
     * @return string
     */
    public function getClass()
    {
        return get_class( $this );
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

    /**
     * configureFields 
     * 
     * Deze methode dient om de velden te configurern
     * Methode die mag overschreven worden in concrete 
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
            if (  $matches[1] == 'add' || $matches[1] == 'remove' ) {
                $property = $this->pluralize($property);
                if (!$property) {
                    throw new KVDdom_Fields_Exception(  'U probeert een bewerking 
                    uit te voeren op een collection, maar de naam van de collection 
                    kon niet gevonden worden. Mogelijk moet u de pluralize methode aanpassen.' );
                }
            }
            if ( !isset( $this->fields[$property] ) ) {
                throw new KVDdom_Fields_Exception ( 'U probeert een bewerking uit te voeren met het veld ' 
                                                    . $property . ', maar dit veld bestaat niet.' );
            }
            switch ( $matches[1]) {
                case 'get':
                    return $this->fields[$property]->getValue();
                case 'set':
                    return $this->fields[$property]->setValue($args[0]);
                case 'clear':
                    return $this->fields[$property]->clear();
                case 'add':
                    return $this->fields[$property]->add($args[0]);
                case 'remove':
                    return $this->fields[$property]->remove($args[0]);
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
     * Deze methode is nodig om de fields correct te laten werken, maar doet 
     * bij dit soort object eigenlijk niets aangezien dit object nooit dirty 
     * kan zijn.
     *
     * @since   24 maart 2010
     * @param   KVDdom_Fields_AbstractField $field 
     * @throws  KVDdom_Fields_Exception
     * @return  void
     */
    public function markFieldAsDirty( KVDdom_Fields_AbstractField $field )
    {
        if ( isset( $this->fields[$field->getName(  )] ) ) {
            //ok, do nothing
        } else {
            throw new KVDdom_Fields_Exception( 'U probeert een niet-bestaand veld als dirty te markeren!' );
        }
    }
    
}
?>
