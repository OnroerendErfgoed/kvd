<?php
/**
 * @package KVD.ag.domHtml
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @version $Id$
 */
    
/**
 * @package KVD.ag.domHtml
 * @author Koen Van Daele <koen.vandaele@lin.vlaanderen.be>
 * @since 1.0.0
 */
abstract class KVDag_DoToHtml
{
    /**
     * @var KVDag_SingleRecordToHtml
     */
    protected $_record;

    /**
     * @var string
     */
    protected $moduleAccessor;

    /**
     * @var string
     */
    protected $actionAccessor;
    
    /**
     * @param Webcontroller $ctrl
     * @param string $moduleAccessor
     * @param string $actionAccessor
     */
    public function __construct ( $ctrl , $moduleAccessor = AG_MODULE_ACCESSOR , $actionAccessor = AG_ACTION_ACCESSOR )
    {
        $this->_record = new KVDag_SingleRecordToHtml ( $ctrl );
        $this->moduleAccessor = $moduleAccessor;
        $this->actionAccessor = $actionAccessor;
    }

    /**
     * @return string
     */
    abstract public function toHtml( $cssClasses = null );
    
}
?>
