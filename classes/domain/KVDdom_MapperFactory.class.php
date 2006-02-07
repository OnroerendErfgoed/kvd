<?php
/**
 * @package KVD.dom
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id: KVDdom_MapperFactory.class.php,v 1.1 2006/01/12 14:46:02 Koen Exp $
 */

/**
 * Factory voor KVDdom_DataMappers
 *
 * Kan extended worden om andere mappers te kunnen gebruiken
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
     * @var string
     */
    private $mapperDir;
    
    /**
     * @param KVDdom_Sessie $sessie Wordt doorgegeven aan de mapper omwille van de Uow en de connection.
     * @param string $mapperDir Directory waar de mappers gevonden kunnen worden.
     */
    public function __construct ( $sessie , $mapperDir = '' )
    {
        $this->_sessie = $sessie;
        $this->mapperDir = $mapperDir;
    }
    
    /**
     * @param string $teMappenClass Naam van de class waarvoor een mapper moet aangemaakt worden.
     * @return KVDdom_AbstractMapper Een concrete implementatie van een KVDdom_AbstractMapper.
     * @throws Exception - Indien er geen mapper gevonden werd
     * @todo Zorgen dat er verschillende exceptions gesmeten worden afhankelijk van het probleem.
     */
    public function createMapper ( $teMappenClass )
    {
        try {
            $classMapper = $teMappenClass . 'DataMapper';
            $classMapperFile = $this->mapperDir . $teMappenClass . 'DM.class.php';
            if (!file_exists($classMapperFile)) {
                throw new Exception ("Bestand $classMapperFile bestaat niet" , 1);
            }
            require_once ($classMapperFile);
            if (!class_exists($classMapper)) {
                throw new Exception ("Class $classMapper bestaat niet" , 2);
            }
            return new $classMapper ($this->_sessie);
        } catch (Exception $e) {
            $message = "Er werd geen mapper voor class $teMappenClass gevonden.";
            if ($e->getCode() == 1 || $e->getCode() == 2) {
                $message .= " Reden: {$e->getMessage()}.";
            }
            throw new Exception ($message);
        }
    }
}
?>
