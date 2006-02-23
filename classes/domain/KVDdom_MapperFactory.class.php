<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */

/**
 * Factory voor KVDdom_DataMappers
 *
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 * @todo Eventueel zorgen dat mappers zich ook in een submap van de mapperdir kunnen bevinden.
 */
class KVDdom_MapperFactory
{
    /**
     * @var KVDdom_Sessie
     */
    protected $_sessie;

    /**
     * @var array
     */
    private $mapperDirs;
    
    /**
     * @param KVDdom_Sessie $sessie Wordt doorgegeven aan de mapper omwille van de Uow en de connection.
     * @param array $mapperDirs Directories waar de mappers gevonden kunnen worden.
     */
    public function __construct ( $sessie , $mapperDirs )
    {
        $this->_sessie = $sessie;
        $this->mapperDirs = $mapperDirs;
    }
    
    /**
     * @param string $teMappenClass Naam van de class waarvoor een mapper moet aangemaakt worden.
     * @return KVDdom_AbstractMapper Een concrete implementatie van een KVDdom_AbstractMapper.
     * @throws Exception - Indien er geen mapper gevonden werd
     * @todo Zorgen dat er verschillende exceptions gesmeten worden afhankelijk van het probleem.
     */
    public function createMapper ( $teMappenClass )
    {
        $classMapper = $this->getClassMapper (  $teMappenClass );
            
        foreach ( $this->mapperDirs as $mapperDir) {
            if ( substr( $mapperDir , -1) != '/' ) {
                $mapperDir .= '/';
            }
                
            $classMapperFile = $mapperDir . $classMapper . '.class.php';
            if ( file_exists( $classMapperFile ) ) {
                require_once( $classMapperFile );
            }
        }
            
        if (!class_exists($classMapper)) {
            $message = "Er werd geen mapper voor class $teMappenClass gevonden. Class $classMapper werd niet gevonden. Doorzochte directories: \n";
            $message .= implode( $this->mapperDirs , ",\n" );
            throw new Exception ( $message  );
        }
        return new $classMapper ($this->_sessie);
    }

    /**
     * @param string $teMappenClass Naam van de class waarvoor de naam van de mapper gedetermineerd moet worden.
     * @return string Naam van de datamapper voor de te mappen class.
     * @throws <b>Exception</b> - Indien de naam van de te mappen class ongeldig is.
     */
    private function getClassMapper ( $teMappenClass )
    {
        $underscorePos = strpos( $teMappenClass , '_');
        if ( $underscorePos === false ) {
            throw new Exception ( "Ongeldige DomainObject naam: $teMappenClass. Er kon geen prefix gedetermineerd worden. Er moet een underscore aanwezig zijn.");
        }
        $prefix = substr ( $teMappenClass, 0, $underscorePos );
        $suffix = substr ( $teMappenClass , $underscorePos );
        $prefix = str_replace ( 'do', 'dm', $prefix);

        $classMapper = $prefix . $suffix;

        return $classMapper;
    }
}
?>
