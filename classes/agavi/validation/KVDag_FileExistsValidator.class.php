<?php
/**
 * @package    KVD.agavi
 * @subpackage validation
 * @copyright  2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */

/**
 * KVDag_FileExistsValidator
 *
 * Validator die controleert of een bepaalde bestandsnaam aanwezig is in één of
 * meer mappen. Van zodra het bestand in één van de mappen voorkomt is de
 * validator tevreden.
 *
 * Parameters:
 * <ul>
 *  <li>'pad' : Ofwel een string met de map waarin gezocht moet worden ofwel
 *  een array met alle mappen waarin gezocht moet worden.</li>
 *  <li>'export' : Indien aanwezig wordt aan deze parameter de filehandle in
 *  read modus meegegeven.</li>
 * </ul>
 *
 * @package    KVD.agavi
 * @subpackage validation
 * @since      30 nov 2010
 * @copyright  2010 {@link http://www.vioe.be Vlaams Instituut voor het Onroerend Erfgoed}
 * @author     Koen Van Daele <koen.vandaele@rwo.vlaanderen.be>
 */
class KVDag_FileExistsValidator extends AgaviValidator
{
    /**
     * validate
     *
     * @return boolean  True when valid
     */
    public function validate(  )
    {
        $val = $this->getData( $this->getArgument( ) );

        if ( !$this->hasParameter( 'pad' ) ) {
            throw new AgaviValidationException( 'U moet een pad opgeven.' );
        }

        $paden = $this->getParameter( 'pad' );

        if ( !is_array( $paden ) ) {
            $paden = array( $paden );
        }

        foreach ( $paden as $pad ) {
            $bestand = $pad . DIRECTORY_SEPARATOR . basename( $val );

            if ( file_exists( $bestand ) ) {
                if ( $this->hasParameter( 'export' ) ) {
                    $this->export( fopen( $bestand, 'rb' ) );
                }
                return true;
            }
        }

        $this->throwError( );
        return false;

    }
}
?>
