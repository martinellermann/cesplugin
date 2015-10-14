<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");

/**
 * User interface hook class
 *
 * @author Alex Killing <alex.killing@gmx.de>
 * @version $Id$
 * @ingroup ServicesUIComponent
 */
class ilCourseEmailSubscriptionUIHookGUI extends ilUIHookPluginGUI
{

    /**
     * @var ilCtrl
     */
    protected $ctrl;

    public function __construct() {
        global $ilCtrl;
        $this->ctrl = $ilCtrl;
    }

    /**
     * Modify GUI objects, before they generate ouput
     *
     * @param $component
     * @param $part
     * @param array $contextElements
     */
    public function modifyGUI($component, $part, $contextElements = array())
    {
        // currently only implemented for $ilTabsGUI

        // tabs hook
        // note that you currently do not get information in $a_comp
        // here. So you need to use general GET/POST information
        // like $_GET["baseClass"], $ilCtrl->getCmdClass/getCmd
        // to determine the context.
        if ($part == "tabs" && $this->isInCourseGUI())
        {
            /** @var ilTabsGUI $tabs */
            $tabs = $contextElements["tabs"];
//            $contextElements["tabs"]->addTab("test", "test", "test");
            $this->ctrl->saveParameterByClass('ilCourseEmailSubscriptionGUI', 'ref_id');
            $tabs->addTab('courseSubscription', 'Mitglieder Einschreiben', $this->ctrl->getLinkTargetByClass(array('ilUIPluginRouterGUI', 'ilCourseEmailSubscriptionGUI'), 'show'));
//            $tabs->setTabActive('tab_view_content');
//            var_dump($tabs->getActiveTab());
        }
//        if ($part == 'tabs')
//        {
//            echo "<br><pre>";
//            var_dump($this->ctrl->getCallHistory());
//            echo "</pre>";
//        }

    }

    /**
     * @return bool returns true iff we are currently in the context of a coues object.
     */
    protected function isInCourseGUI() {
        foreach($this->ctrl->getCallHistory() as $GUIClassesArray) {
            if($GUIClassesArray['class'] == 'ilObjCourseGUI')
                return true;
        }
        return false;
    }

}