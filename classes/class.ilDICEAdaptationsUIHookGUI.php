<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

include_once("./Services/UIComponent/classes/class.ilUIHookPluginGUI.php");

/**
* ilHSLUUIDefaultsUIHookGUI class
*
* @author Simon Moor <simon.moor@hslu.ch>
* @version $Id$
* @ingroup ServicesUIComponent
*/
class ilDICEAdaptationsUIHookGUI extends ilUIHookPluginGUI {
	function getHTML($a_comp, $a_part, $a_par = array())
	{
	   global $ilCtrl;
	   
	   if ($a_comp = 'Services/Locator' && $a_part == 'main_locator') {
	   	return array('mode' => ilUIHookPluginGUI::REPLACE, 'html' => '');
	   } else if ($a_part == 'template_get') {

	   }

		return array('mode' => ilUIHookPluginGUI::KEEP, 'html' => '');
	}
}
?>
