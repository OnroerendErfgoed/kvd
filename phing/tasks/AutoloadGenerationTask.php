<?php
require_once( "phing/Task.php");
class AutoloadGenerationTask extends Task
{
    private $classes = array( );

    private $filesets = array( );

    private $destinationFile;

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

    public function setDestFile( PhingFile $file)
    {
        $this->destinationFile = $file;
    }

    public function init( )
    {
        // do nothing
    }

    private function validate( )
    {
        if ( empty($this->filesets) ) {
            throw new BuildException("You must specify a fileset.", $this->getLocation( ));
        }
        if ( !isset( $this->destinationFile ) ) {
            throw new BuildException("You must specify a destination file.", $this->getLocation( ));
        }
    }

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
        $creator = new AutoloadArrayCreator( );
        $creator->create( $this->classes );
        $result = $creator->getResult( );
        $this->writeData( $result );
        $this->log( 'The autoloader contains ' . count( $this->classes ) . ' classes and interfaces.' );
    }

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

    private function writeData( $data )
    {
        file_put_contents($this->destinationFile->getAbsolutePath( ), $data );
    }
}

abstract class AutoloadCreator
{
    protected $result;

    abstract public function create( $data );

    public function getResult( )
    {
        return $this->result;
    }
}

class AutoloadArrayCreator extends AutoloadCreator
{
    public function create( $data )
    {
        $this->result = "<?php \n" . var_export( $data, true ) . "?>";
    }
}
?>
