<?php
/**
 * @package     KVD.dom
 * @subpackage  fields
 * @version     $Id$
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDdom_Fields_AbstractField 
 * 
 * @package     KVD.dom
 * @subpackage  fields
 * @since       11 feb 2010
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDdom_Fields_AbstractField
{
    /**
     * dom 
     * 
     * @var KVDdom_DomainObject
     */
    protected $dom;

    /**
     * name 
     * 
     * @var string
     */
    protected $name;

    /**
     * value 
     * 
     * @var mixed
     */
    protected $value;

    /**
     * __construct 
     * 
     * @param   KVDdom_DomainObject     $dom    Object waartoe het veld behoort 
     * @param   string                  $name   Naam van het veld           
     * @return  void
     */
    public function __construct( KVDdom_DomainObject $dom, $name )
    {
        $this->dom = $dom;
        $this->name = $name;
    }

    /**
     * getName 
     * 
     * Naam van het veld
     * @return string
     */
    public function getName( )
    {
        return $this->name;
    }

    /**
     * getValue 
     * 
     * De huidige waarde van het veld
     * @return mixed
     */
    abstract public function getValue(  );

    /**
     * setValue 
     * 
     * Stel een nieuwe waarde voor het veld. Voor een meervoudig veld zal dit 
     * alle veldwaarden vervangen.
     * @param   mixed   $value
     * @return  void
     */
    abstract public function setValue( $value  );

    /**
     * initializeValue 
     * 
     * Methode om een veld een startwaarde toe te kennen.
     * Dit zal er niet toe leiden dat het object als
     * dirty gemarkeerd wordt.
     * @param   mixed $value 
     * @return  void
     */
    abstract public function initializeValue( $value );
}
?>
