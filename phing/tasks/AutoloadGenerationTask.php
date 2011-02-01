<?php
/**
 * @package phing
 * @version $Id$
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */

require_once( "phing/Task.php");

/**
 * AutoloadGenerationTask 
 * 
 * @package phing
 * @since 6 feb 2008
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class AutoloadGenerationTask extends Task
{
    /**
     * classes 
     * 
     * @var array
     */
    private $classes = array( );

    /**
     * filesets 
     * 
     * @var array
     */
    private $filesets = array( );

    /**
     * destinationFile 
     * 
     * @var string
     */
    private $destinationFile;

    /**
     * outputType 
     * 
     * @var string
     */
    private $outputType;

    /**
     * outputName 
     * 
     * @var string
     */
    private $outputName;

    /**
     * Nested creator, creates a FileSet for this task
     *
     * @access  public
     * @return  object  The created fileset object
     */
    public function createFileSet() {
        $num = array_push($this->filesets, new FileSet());
        return $this->filesets[$num-1];
    }

    /**
     * setDestFile 
     * 
     * @param PhingFile $file 
     * @return void
     */
    public function setDestFile( PhingFile $file)
    {
        $this->destinationFile = $file;
    }

    /**
     * setOutputType 
     * 
     * @param string $type 
     * @return void
     */
    public function setOutputType( $type )
    {
        $this->outputType = $type;
    }

    /**
     * setOutputName 
     * 
     * @param string $name 
     * @return void
     */
    public function setOutputName( $name )
    {
        $this->outputName = $name;
    }

    /**
     * init 
     * 
     * @return void
     */
    public function init( )
    {
        // do nothing
    }

    /**
     * Validate input. Check if filesets, destinationfile and outputname are present.
     * Set outputtype to default value 'Array' if not present.
     * Set registerAutoloader to default value 'True' if outputType is function 
     * and value is not set.
     * 
     * @throws BuildException - If certain parameters are not set.
     * @return void
     */
    private function validate( )
    {
        if ( empty($this->filesets) ) {
            throw new BuildException("You must specify a fileset.", $this->getLocation( ));
        }
        if ( !isset( $this->destinationFile ) ) {
            throw new BuildException("You must specify a destination file.", $this->getLocation( ));
        }
        if ( !isset( $this->outputType ) ) {
            $this->outputType = 'Array';
        }
        if ( !isset( $this->outputName ) ) {
            throw new BuildException("You must specify a name for the output variable or function.", $this->getLocation( ));
        }
    }

    /**
     * Generate the autoloader. 
     * 
     * @return void
     */
    public function main( )
    {
        $this->validate( );

        $this->log( 'Autoloader is being generated.');

        foreach($this->filesets as $fs) {
	        $files = $fs->getDirectoryScanner($this->project)->getIncludedFiles();
	        foreach($files as $filename) {
	            $f = new PhingFile($fs->getDir($this->project), $filename);
                $this->classes = array_merge( $this->classes, $this->getClassesFromFile( $f->getAbsolutePath( )));
	        }
        }
        $creator = AutoloadCreator::factory( $this->outputType, $this->outputName );
        $creator->create( $this->classes );
        $result = $creator->getResult( );
        $this->writeData( $result );
        $this->log( 'The autoloader contains ' . count( $this->classes ) . ' classes and interfaces.' );
    }

    /**
     * Get all classes and interfaces from a file
     * 
     * @param string $path 
     * @return array An array of class names and interface names found.
     */
    private function getClassesFromFile( $path )
    {
        $classes = array( );
        $tokens = token_get_all( file_get_contents( $path ) );
        $getNext = false;
        foreach ( $tokens as $token ) {
            if ( is_array( $token ) ) {
                if ( !$getNext && $token[0] == T_CLASS || $token[0] == T_INTERFACE ) {
                    $getNext = true;
                } else if ( $getNext && is_array( $token ) && $token[0] == T_STRING ) {
                    $classes[$token[1]] = $path;
                    $getNext = false;
                }
            }
        }
        return $classes;
    }

    /**
     * Write data to the destination file.
     * 
     * @param string $data 
     * @throws BuildException - If the destination file could not be written.
     * @return void
     */
    private function writeData( $data )
    {
        if ( !@file_put_contents($this->destinationFile->getAbsolutePath( ), $data ) ) {
            throw new BuildException( 'Unable to write to the specified destination file.' );
        }
    }
}

/**
 * AutoloadCreator 
 * 
 * @package phing
 * @subpackage 
 * @since 6 feb 2008
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
abstract class AutoloadCreator
{
    /**
     * result 
     * 
     * @var string
     */
    protected $result;

    /**
     * Name to use for the output variable or function.
     * 
     * @var string
     */
    protected $outputName;

    /**
     * create 
     * 
     * @param array $classes 
     * @return void
     */
    abstract public function create( array $classes );

    /**
     * __construct 
     * 
     * @param string $outputName 
     * @return void
     */
    public function __construct( $outputName )
    {
        $this->setOutputName( $outputName );
    }

    /**
     * getResult 
     * 
     * @return string
     */
    public function getResult( )
    {
        return $this->result;
    }

    /**
     * setOutputName 
     * 
     * @param string $name 
     * @return void
     */
    public function setOutputName( $name )
    {
        $this->outputName = $name;
    }

    /**
     * factory 
     * 
     * @param string $type          Type of creator to return. Can be 'Array', 'Function' 
     * , 'RegisteredFunction' or 'AgaviXml'. Defaults to 'Array'.
     * @param string $outputName    Name for the output variable or function
     * @return AutoloadCreator
     */
    public static function factory( $type , $outputName)
    {
        switch ( $type ) {
            case 'Function':
                return new AutoloadFunctionCreator( $outputName );
                break;
            case 'RegisteredFunction':
                return new AutoloadRegisteredFunctionCreator( $outputName );
                break;
            case 'AgaviXml':
                return new AutoloadAgaviXmlCreator( $outputName );
                break;
            case 'Array':
            default:
                return new AutoloadArrayCreator( $outputName );
                break;
        }
    }

}

/**
 * AutoloadArrayCreator 
 * 
 * @package phing
 * @since 6 feb 2008
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class AutoloadArrayCreator extends AutoloadCreator
{
    /**
     * create 
     * 
     * @param array $classes 
     * @return void
     */
    public function create( array $classes )
    {
        $this->result = "<?php \n\${$this->outputName}=" . var_export( $classes, true ) . "?>";
    }
}

/**
 * AutoloadFunctionCreator 
 * 
 * @package phing
 * @since 6 feb 2008
 * @copyright 2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class AutoloadFunctionCreator extends AutoloadCreator
{
    /**
     * create 
     * 
     * @param array $classes 
     * @return void
     */
    public function create( array $classes )
    {
        $this->result = 
        "<?php \n " .
        "function {$this->outputName}( \$class )  {\n" . 
        "\tstatic \$autoloads = " .var_export( $classes, true) . ";\n" .
        "\tif(isset(\$autoloads[\$class])) {\n" .
        "\t\trequire(\$autoloads[\$class]);\n".
        "\t}\n".
        "}\n".
        "?>";
    }
}

/**
 * AutoloadRegisteredFunctionCreator 
 * 
 * @package     phing
 * @since       20 jan 2010
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class AutoloadRegisteredFunctionCreator extends AutoloadCreator
{
    /**
     * create 
     * 
     * @param array $classes 
     * @return void
     */
    public function create( array $classes )
    {
        $this->result = 
        "<?php \n " .
        "function {$this->outputName}( \$class )  {\n" . 
        "\tstatic \$autoloads = " .var_export( $classes, true) . ";\n" .
        "\tif(isset(\$autoloads[\$class])) {\n" .
        "\t\trequire(\$autoloads[\$class]);\n".
        "\t}\n".
        "}\n".
        "spl_autoload_register(\"{$this->outputName}\");\n".
        "?>";
    }
}

/**
 * AutoloadAgaviXmlCreator 
 * 
 * @package     phing
 * @since       31 jan 2010
 * @copyright   2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */
class AutoloadAgaviXmlCreator extends AutoloadCreator
{
    /**
     * create 
     * 
     * @param array $classes 
     * @return void
     */
    public function create( array $classes )
    {
        $res = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $res .= '<ae:configurations  xmlns="http://agavi.org/agavi/config/parts/autoload/1.0" xmlns:ae="http://agavi.org/agavi/config/global/envelope/1.0">' . "\n";
        $res .= '<ae:configuration>' . "\n";
        $res .= '<autoloads>' . "\n";
        foreach ( $classes as $class => $file) {
            $res .= '<autoload name="' . $class . '"><![CDATA[' . $file . ']]></autoload>' . "\n";
        }
        $res .= '</autoloads>' . "\n";
        $res .= '</ae:configuration>' . "\n";
        $res .= '</ae:configurations>';
        $this->result = $res;
    }
}
?>
