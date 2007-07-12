<?php
class KVDdom_NullGebruiker implements KVDdom_Gebruiker
{
    public function getGebruikersNaam( )
    {
        return 'anoniem';
    }

    public function getWachtwoord( )
    {
        throw new LogicException( 'NullGebruiker heeft geen wachtwoord.');
    }

    public function setGebruikersNaam( $naam )
    {
        throw new LogicException( 'NullGebruiker kan niet bewerkt worden.');
    }

    public function setWachtwoord( $wachtwoord )
    {
        throw new LogicException( 'NullGebruiker kan niet bewerkt worden.');
    }

    public function getOmschrijving( )
    {
        return 'anoniem';
    }

    public function getId( )
    {
        return null;
    }

    public function getClass( )
    {
        return get_class( $this );
    }
}
?>
