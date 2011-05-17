<?php
/**
 * @package    KVD.util
 * @subpackage xml
 * @version    $Id$
 * @copyright  2008-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be> 
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

/**
 * KVDutil_Xml_DomainObjectProcessor
 * 
 * Abstracte class die een aantal algemene zaken die een xml processor moet 
 * kunnen regelt. Bedoeling is dat er concrete implementaties geschreven worden 
 * die van een stuk xml een geldig domainobject maken of er een geldige 
 * bewerking mee doen.
 *
 * @package    KVD.util
 * @subpackage xml
 * @since      15 feb 2008
 * @copyright  2008-2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @author     Bram Goessens <bram.goessens@rwo.vlaanderen.be>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class KVDutil_Xml_DomainObjectProcessor
{
    /**
     * Er is een waarschuwing die dringend moet behandeld worden of zware 
     * gevolgen kan hebben.
     *
     * @var string 
     */
    const WARNING_HOOG = 'HOOG';

    /**
     * Er is een waarschuwing die best zou aangepakt worden, maar mogelijk ook 
     * genegeerd kan worden.
     *
     * @var string
     */
    const WARNING_LAAG = 'LAAG';

    /**
     * sessie 
     * 
     * @var KVDdom_IWriteSessie
     */
    protected $sessie;

    /**
     * xml 
     * 
     * @var SimpleXMLElement
     */
    protected $xml = null;


    /**
     * warnings 
     * 
     * @var array
     */
    protected $warnings;

    /**
     * @param   KVDdom_IWriteSessie     $sessie 
     */
    public function __construct ( KVDdom_IWriteSessie $sessie )
    {
        $this->sessie = $sessie;
        $this->warnings = array( );
    }

    /**
     * setXml 
     * 
     * @param   SimpleXMLElement $xml 
     * @return  void
     */
    public function setXml( SimpleXMLElement $xml )
    {
        $this->xml = $xml;
    }

    /**
     * checkXml 
     * 
     * @throws <b>KVDutil_Xml_Exception</b> - Indien er geen geldig xml element is.
     */
    protected function checkXml( )
    {
        if ( is_null( $this->xml ) || !$this->xml instanceof SimpleXMLElement ) {
            throw new KVDutil_Xml_Exception ( 'Er is geen geldig xml object ingesteld.');
        }
    }

    /**
     * process 
     * 
     * @return KVDdom_DomainObject
     */
    abstract public function process( );
    
    /**
     * addWarning 
     * 
     * @param   string  $warning 
     * @return  void
     */
    protected function addWarning( $warning , $priority = self::WARNING_HOOG )
    {
        $this->warnings[] = '['.$priority.']'. $warning;
    }

    /**
     * getWarnings 
     * 
     * @return  array
     */
    public function getWarnings( )
    {
        return $this->warnings;
    }

    /**
     * hasWarnings 
     * 
     * @return  boolean
     */
    public function hasWarnings( )
    {
        return count( $this->warnings ) > 0;
    }
}
?>
