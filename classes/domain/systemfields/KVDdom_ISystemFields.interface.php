<?php
interface KVDdom_ISystemFields
{
    public function setCreated( $gebruiker );

    public function setUpdated( $gebruiker );

    public function setApproved( $gebruiker );

    public function getAttribute( $attribute );
}
?>
