<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewspickerBundle\Widget;

use Contao\DataContainer;
use Contao\NewsModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Widget;

class NewspickerWidget extends Widget
{
    protected $blnSubmitInput = true;
    protected $blnForAttribute = true;
    protected $strTemplate = 'be_widget';

    public function generate(): string
    {
        return sprintf(
            '<input type="text" name="%s" id="ctrl_%s" class="tl_custom_widget%s" value="%s">',
            $this->name,
            $this->id,
            ($this->class ? ' '.$this->class : ''),
            StringUtil::specialchars($this->value)
        );
    }

//    /**
//     * Template.
//     *
//     * @var string
//     */
//    protected $strTemplate = 'be_widget';

//    public function generate()
//    {
//        $container = System::getContainer();
//        $values = [];
//
//        // Can be an array
//        if (!empty($this->varValue) && null !== ($newsCollection = NewsModel::findMultipleByIds((array) $this->varValue))) {
//            $dataContainer = 'DC_'.$GLOBALS['TL_DCA']['tl_news']['config']['dataContainer'];
//            $dc = new $dataContainer('tl_news');
//
//            /** @var NewsModel $newsModel */
//            foreach ($newsCollection as $newsModel) {
//                $values[$newsModel->id] = $this->renderLabel($newsModel->row(), $dc);
//            }
//        }
//
//        $return = '<input type="hidden" name="'.$this->strName.'" id="ctrl_'.$this->strId.'" value="'.implode(',', array_keys($values)).'">
//  <input type="hidden" name="'.$this->strOrderName.'" id="ctrl_'.$this->strOrderId.'" value="'.implode(',', array_keys($values)).'">
//  <div class="selector_container">'.((\count($values) > 1) ? '
//    <p class="sort_hint">'.$GLOBALS['TL_LANG']['MSC']['dragItemsHint'].'</p>' : '').'
//    <ul id="sort_'.$this->strId.'" class="'.($this->sorting ? 'sortable' : '').'">';
//
//        foreach ($values as $k => $v) {
//            $return .= '<li style="cursor:move;" data-id="'.$k.'">'.$v.'</li>';
//        }
//
//        $return .= '</ul>';
//        $pickerBuilder = $container->get('contao.picker.builder');
//
//        if (!$pickerBuilder->supportsContext('news')) {
//            $return .= '
//	<p><button class="tl_submit" disabled>'.$GLOBALS['TL_LANG']['MSC']['changeSelection'].'</button></p>';
//        } else {
//            $extras = ['fieldType' => $this->fieldType];
//
//            if (\is_array($this->rootNodes)) {
//                $extras['rootNodes'] = array_values($this->rootNodes);
//            }
//
//            $return .= '
//	<p><a href="'.ampersand($pickerBuilder->getUrl('node', $extras)).'" class="tl_submit" id="pt_'.$this->strName.'">'.$GLOBALS['TL_LANG']['MSC']['changeSelection'].'</a></p>
//	<script>
//	  $("pt_'.$this->strName.'").addEvent("click", function(e) {
//		e.preventDefault();
//		Backend.openModalSelector({
//		  "id": "tl_listing",
//		  "title": "'.StringUtil::specialchars(str_replace("'", "\\'", $GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['label'][0])).'",
//		  "url": this.href + document.getElementById("ctrl_'.$this->strId.'").value,
//		  "callback": function(table, value) {
//			new Request.Contao({
//			  evalScripts: false,
//			  onSuccess: function(txt, json) {
//				$("ctrl_'.$this->strId.'").getParent("div").set("html", json.content);
//				json.javascript && Browser.exec(json.javascript);
//			  }
//			}).post({"action":"reloadNodePickerWidget", "name":"'.$this->strId.'", "value":value.join("\t"), "REQUEST_TOKEN":"'.REQUEST_TOKEN.'"});
//		  }
//		});
//	  });
//	</script>
//	<script>Backend.makeMultiSrcSortable("sort_'.$this->strId.'", "ctrl_'.$this->strId.'", "ctrl_'.$this->strId.'")</script>';
//        }
//
//        $return = '<div>'.$return.'</div></div>';
//
//        return $return;
//    }
//
//    protected function renderLabel(array $arrRow, DataContainer $dc)
//    {
//        $mode = $GLOBALS['TL_DCA'][$dc->table]['list']['sorting']['mode'] ?? 1;
//
//        if (4 === $mode) {
//            $callback = $GLOBALS['TL_DCA'][$dc->table]['list']['sorting']['child_record_callback'];
//
//            if (\is_array($callback)) {
//                $this->import($callback[0]);
//
//                return $this->{$callback[0]}->{$callback[1]}($arrRow);
//            }
//
//            if (\is_callable($callback)) {
//                return $callback($arrRow);
//            }
//        }
//
//        $labelConfig = &$GLOBALS['TL_DCA'][$dc->table]['list']['label'];
//        $label = vsprintf($labelConfig['format'], array_intersect_key($arrRow, array_flip($labelConfig['fields'])));
//
//        if (\is_array($labelConfig['label_callback'])) {
//            $this->import($labelConfig['label_callback'][0]);
//
//            return $this->{$labelConfig['label_callback'][0]}->{$labelConfig['label_callback'][1]}($arrRow, $label, $dc, $arrRow);
//        }
//
//        if (\is_callable($labelConfig['label_callback'])) {
//            return $labelConfig['label_callback']($arrRow, $label, $dc, $arrRow);
//        }
//
//        return $label ?: $arrRow['id'];
//    }
}
