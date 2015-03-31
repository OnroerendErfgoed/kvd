<?php
/**
 * @package     KVD.dom
 * @subpackage  fields
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDdom_Fields_SingleField
 *
 * @package     KVD.dom
 * @subpackage  fields
 * @since       11 feb 2010
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDdom_Fields_SingleField extends KVDdom_Fields_AbstractField
{
    /**
     * default
     *
     * De standaard waarde die een veld heeft als het niet ingevuld is.
     * @var mixed
     */
    protected $default;

    /**
     * type
     *
     * Het type van het veld. Wordt voorlopig nog niet gebruikt, maar kan
     * toegevoegd worden in de toekomst.
     * @var string
     */
    protected $type;

    /**
     * __construct
     *
     * @param   KVDdom_DomainObject     $dom
     * @param   string                  $name
     * @param   mixed                   $default
     * @param   string                  $type
     * @return  void
     */
    public function __construct( KVDdom_DomainObject $dom, $name, $default = null, $type = null )
    {
        parent::__construct( $dom, $name );
        $this->default = $default;
        $this->type = $type;
    }


    /**
     * getDefaultValue
     *
     * @return mixed
     */
    public function getDefaultValue( )
    {
        return $this->default;
    }

    /**
     * getType
     *
     * @return string
     */
    public function getType( )
    {
        return $this->type;
    }

    /**
     * getValue
     *
     * @return mixed
     */
    public function getValue( )
    {
        return ( $this->value != null ? $this->value : $this->default);
    }

    /**
     * setValue
     *
     * @param   mixed $value
     * @return  void
     */
    public function setValue($value)
    {
        if ( $value !== $this->value ) {
            $this->value = $value;
            $this->dom->markFieldAsDirty($this);
        }
    }

    /**
     * initializeValue
     *
     * @param   mixed $value
     * @return  void
     */
    public function initializeValue( $value )
    {
        $this->value = $value;
    }
}
?>
