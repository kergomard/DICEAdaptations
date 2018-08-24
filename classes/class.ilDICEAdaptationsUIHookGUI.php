<?php

/* Copyright (c) 1998-2010 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * ilHSLUUIDefaultsUIHookGUI class
 *
 * @author Stephan Winiker <stephan.winiker@hslu.ch>
 * @version $Id$
 * @ingroup ServicesUIComponent
 */
class ilDICEAdaptationsUIHookGUI extends ilUIHookPluginGUI
{
    private static $blocks = [
        'ressources' => [
            'class' => 'ressources',
            'ref_id' => '221',
            'text' => 'Search Resources'
        ],
        'motivational' => [
            'class' => 'motivation',
            'ref_id' => '167',
            'text' => 'Get Active'
        ]
    ];
    function getHTML($a_comp, $a_part, $a_par = array())
    {
        global $DIC;
        if ($a_comp == 'Services/Locator' && $a_part == 'main_locator') {
            return array(
                'mode' => ilUIHookPluginGUI::REPLACE,
                'html' => ''
            );
        } else if ($a_comp == 'Services/Container' && $a_part == 'right_column' && !ilObjUser::_isAnonymous($DIC->user()->getId())) {
            return array(
                'mode' => ilUIHookPluginGUI::PREPEND,
                'html' => $this->getBlockHTML(self::$blocks)
            );
        }

        return array(
            'mode' => ilUIHookPluginGUI::KEEP,
            'html' => ''
        );
    }

    function modifyGUI($a_comp, $a_part, $a_par = array())
    {
        if ($a_part == "tabs") {
            global $DIC;

            $ref_id = (int) $_GET['ref_id'];

            $classes = [];

            foreach ($DIC->ctrl()->getCallHistory() as $call) {
                $classes[] = $call['class'];
            }

            if ($_GET['baseClass'] == 'ilPersonalDesktopGUI' && ((int) $_GET['wsp_id'] != 0) || array_search('ilObjRoleGUI', $classes) !== false || $ref_id == 0) {
                // We are in the Personal Desktop, in the root note, or in the roleGUI and we do nothing
            } else if ($_GET['baseClass'] == 'ilMailGUI' and ((int) $_GET['mail_id'] != 0) || $_GET['cmd'] == 'mailUser' || $_GET['cmdClass'] == 'ilmailformgui' || $_GET['ref'] == 'mail') {
                // We are in emails and simply set a fixed back link
                $a_par["tabs"]->setBackTarget($DIC->language()
                    ->txt("back"), 'ilias.php?cmdClass=ilmailfoldergui&baseClass=ilMailGUI');
                return "";
            } else {
                $parent_id = $DIC->repositoryTree()->getParentId($ref_id);
                $object = ilObjectFactory::getInstanceByRefId($ref_id, false);

                if ($object) {
                    $obj_type = $object->getType();
                } else {
                    $obj_type = "";
                }

                $obj_types_with_backlinks = array(
                    'blog',
                    'book',
                    'cat',
                    'dbk',
                    'dcl',
                    'exc',
                    'file',
                    'fold',
                    'frm',
                    'glo',
                    'grp',
                    'htlm',
                    'mcst',
                    'mep',
                    'qpl',
                    'sahs',
                    'svy',
                    'tst',
                    'webr',
                    'wiki',
                    'xstr'
                );

                if (count($a_par["tabs"]->target) > 0 and in_array($obj_type, $obj_types_with_backlinks)) {
                    // This function only works with a hslu-patch
                    if (! method_exists($DIC->tabs(), 'hasBackTarget') || ! $DIC->tabs()->hasBackTarget()) {
                        $parentobject = ilObjectFactory::getInstanceByRefId($parent_id);
                        $parent_type = $parentobject->getType();

                        if ($parent_type != 'cat' || $parent_type != 'root') {
                            $explorer = new ilRepositoryExplorer($parent_id);
                            $back_link = $explorer->buildLinkTarget($parent_id, $parent_type);
                            $DIC->tabs()->setBackTarget($DIC->language()
                                ->txt("back"), $back_link);
                        }
                    }
                }
            }
        }
    }
    
    function getBlockHTML($blocks) {
        global $DIC;
        $DIC->ui()->mainTemplate()->addCss($this->getPluginObject()->getStyleSheetLocation('diceblocks.css'));
        $html = '';
        
        foreach ($blocks as $block) {
            $tpl = $this->getPluginObject()->getTemplate("default/tpl.blocks.html");
            $tpl->setVariable('CLASS', $block['class']);
            
            $obj = ilObjectFactory::getInstanceByRefId($block['ref_id']);
            $type = $obj->getType();
            $explorer = new ilRepositoryExplorer($block['ref_id']);
            $link = $explorer->buildLinkTarget($block['ref_id'], $type);    
            $tpl->setVariable('LINK', $link);
            
            $tpl->setVariable('TEXT', $block['text']);
            $html .= $tpl->get();
        }
        
        return $html;
    }
}
?>
