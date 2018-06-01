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
	function getHTML($a_comp, $a_part, $a_par = array()) {
	   global $DIC;
	   
	   if ($a_comp = 'Services/Locator' && $a_part == 'main_locator') {
	   	return array('mode' => ilUIHookPluginGUI::REPLACE, 'html' => '');
	   } else if ($a_part == 'template_get') {

	   }

		return array('mode' => ilUIHookPluginGUI::KEEP, 'html' => '');
	}
	
	function modifyGUI($a_comp, $a_part, $a_par = array()) {
		if ($a_part == "tabs") {
			global $DIC;
			
			$ref_id=(int)$_GET['ref_id'];
			
			if($_GET['baseClass']=='ilMailGUI' AND ((int)$_GET['mail_id']!=0) OR $_GET['cmd']=='mailUser' OR $_GET['cmdClass']=='ilmailformgui' OR $_GET['ref']=='mail'){
				//we are in emails
				$a_par["tabs"]->setBackTarget($lng->txt("back"),'ilias.php?cmdClass=ilmailfoldergui&baseClass=ilMailGUI');
				return "";
			}
			if($_GET['baseClass']=='ilPersonalDesktopGUI' AND ((int)$_GET['wsp_id']!=0)){
				//we are in arbeitsraum
				return "";
			}
			
			if($ref_id==0) return "";
			
			$parent_id=$DIC->repositoryTree()->getParentId($ref_id);
			$object=ilObjectFactory::getInstanceByRefId($ref_id, false);
			
			if ($object) {
				$obj_type=$object->getType();
			} else {
				$obj_type = "";
			}
			
			$obj_types_with_backlinks=array('blog','book','cat','dbk','dcl','exc','file','fold','frm','glo','grp','htlm','mcst','mep','qpl','sahs','svy','tst','webr','wiki','xstr');
			
			if(count($a_par["tabs"]->target)>0 AND in_array($obj_type,$obj_types_with_backlinks)) {
				// This function only works with a hslu-patch
				if(method_exists($ilTabs,'hasBackTarget')) {
					if($ilTabs->hasBackTarget()) {
						return "";
					}
				}
				
				$parentobject=ilObjectFactory::getInstanceByRefId($parent_id);
				$parent_type = $parentobject->getType();
				
				if($parent_type != 'cat' || $parent_type != 'root') {
					$explorer = new ilRepositoryExplorer($parent_id);
					$back_link = $explorer->buildLinkTarget($parent_id, $parent_type);
					$DIC->tabs()->setBackTarget($DIC->language()->txt("back"),$back_link);
				}
			}
		}
	}
}
?>
