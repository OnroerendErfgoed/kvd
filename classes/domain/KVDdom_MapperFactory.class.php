<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * Factory voor KVDdom_DataMappers
 *
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 * @since 1.0.0
 */
class KVDdom_MapperFactory
{
    /**
     * @var KVDdom_Sessie
     */
    protected $sessie;

    /**
     * @var array
     */
    private $mapperDirs;

    /**
     * parameters
     *
     * @var array
     */
    private $parameters;

    /**
     * @param KVDdom_Sessie $sessie Wordt doorgegeven aan de mapper omwille van de Uow en de connection.
     * @param array $mapperDirs Directories waar de mappers gevonden kunnen worden.
     * @param array $parameters Parameters die moeten doorgegeven worden aan een mapper wanneer die wordt aangemaakt.
     *                          Associatieve array met als key de naam van de mapper en als value het parameter array
     *                          (dat zelf ook een associatieve array is).
     */
    public function __construct ( $sessie , $mapperDirs , $parameters = array( ) )
    {
        $this->_sessie = $sessie;
        $this->mapperDirs = $mapperDirs;
        $this->parameters = $parameters;
    }

    /**
     * @param   string $teMappenClass Naam van de class waarvoor een mapper moet aangemaakt worden.
     * @todo    inladen van mapper geeft problemen door gewijzigde agavi config.
     * @return  KVDdom_AbstractMapper Een concrete implementatie van een KVDdom_AbstractMapper.
     * @throws <b>RuntimeException</b> - Indien er geen mapper gevonden werd
     */
    public function createMapper ( $teMappenClass , $type = null )
    {
        $classMapper = $this->getClassMapper (  $teMappenClass , $type );

        if ( !class_exists( $classMapper) ) {
            $this->loadClassMapperFile (  $classMapper );
        }

        if (!class_exists($classMapper)) {
            $message = "Er werd geen mapper voor class $teMappenClass gevonden. Class $classMapper werd niet gevonden. Doorzochte directories: \n";
            $message .= implode( $this->mapperDirs , ",\n" );
            throw new RuntimeException ( $message  );
        }
        $parameters = array_key_exists( $classMapper , $this->parameters ) ? $this->parameters[$classMapper] : array( );
        // @todo: dit moet herzien worden en is enkel een tijdelijke fix totdat de problemen met de agavi config opgelost zijn.
        if ( isset( $parameters['mapperParameters'] ) )  {
            $parameters = $parameters['mapperParameters'];
        }
        return new $classMapper ($this->_sessie , $parameters );
    }

    /**
     * @param string $classMapper Naam van de datamapper die geladen moet worden.
     * @return void
     */
    private function loadClassMapperFile ( $classMapper )
    {
        foreach ( $this->mapperDirs as $mapperDir) {
            if ( substr( $mapperDir , -1) != '/' ) {
                $mapperDir .= '/';
            }

            $classMapperFile = $mapperDir . $classMapper . '.class.php';
            if ( file_exists( $classMapperFile ) ) {
                require_once( $classMapperFile );
            }
        }
    }

    /**
     * @param string $teMappenClass Naam van de class waarvoor de naam van de mapper gedetermineerd moet worden.
     * @return string Naam van de datamapper voor de te mappen class.
     * @throws <b>InvalidArgumentException</b> - Indien de naam van de te mappen class ongeldig is.
     */
    private function getClassMapper ( $teMappenClass , $type = null )
    {
        $underscorePos = strpos( $teMappenClass , '_');
        if ( $underscorePos === false ) {
            throw new InvalidArgumentException ( "Ongeldige DomainObject naam: $teMappenClass. Er kon geen prefix gedetermineerd worden. Er moet een underscore aanwezig zijn.");
        }
        $prefix = substr ( $teMappenClass, 0, $underscorePos );
        $suffix = substr ( $teMappenClass , $underscorePos );
        $prefix = str_replace ( 'do', 'dm', $prefix);

        $classMapper = $prefix . $suffix;

        if ( $type !== null ) {
            $classMapper .= ucfirst( $type );
        }

        return $classMapper;
    }
}
?>
