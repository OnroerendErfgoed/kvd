<?php
/**
 * @package    KVD.thes
 * @subpackage core
 * @copyright  2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * Null object voor een KVDthes_Thesaurus
 *
 * @package    KVD.thes
 * @subpackage core
 * @since      19 maart 2007
 * @copyright  2004-2008 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDthes_NullThesaurus extends KVDthes_Thesaurus
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct( )
    {
        $this->id = 0;
        $this->naam = 'Onbepaald';
        $this->language = 'Nederlands';
    }

    /**
     * isNull
     *
     * @return boolean
     */
    public function isNull()
    {
        return true;
    }
}
?>
