<?php
/**
 * @package     PACKAGE
 * @subpackage  SUBPACKAGE
 * @version     $Id$
 * @copyright   2009 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author      Koen Van Daele <koen.vandaele@rwo.vlaanderen.be> 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */


class KVDag_HtmlFilterValidator extends AgaviStringValidator
{

    private $tagsallowed = "";
    /**
     * initialize 
     * 
     * @param AgaviContext  $context 
     * @param array         $parameters 
     * @param array         $arguments 
     * @param array         $errors 
     * @return void
     */
    public function initialize(AgaviContext $context, array $parameters = array(), array $arguments = array(), array $errors = array())
    {
        $this->tagsallowed = $parameters["tags"];
        parent::initialize( $context, $parameters, $arguments, $errors );
    }
    
    
    /**
     * Validates whether the argument is a valid id for a domain object. 
     * 
     * @return  bool    True when valid.
     */
    protected function validate( )
    {   
        $value =& $this->getData( $this->getArgument( ) );
        $value = strip_tags($value);
        return parent::validate();
    }
}

?>
