<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewspickerBundle\Widget;

use Contao\Controller;
use Contao\DataContainer;
use Contao\DC_Table;
use Contao\NewsModel;
use Contao\System;
use Contao\Widget;

class NewspickerWidget extends Widget
{
    /**
     * Submit user input.
     *
     * @var bool
     */
    protected $blnSubmitInput = true;

    /**
     * Template.
     *
     * @var string
     */
    protected $strTemplate = 'be_widget';

    public function generate()
    {
        $container = System::getContainer();
        $values = [];

        // Can be an array
        if (!empty($this->varValue) && null !== ($newsCollection = NewsModel::findMultipleByIds((array) $this->varValue))) {
            Controller::loadDataContainer('tl_news');
            $dc = new DC_Table('tl_news');

            /** @var NewsModel $newsModel */
            foreach ($newsCollection as $newsModel) {
                $values[$newsModel->id] = $this->renderLabel($newsModel->row(), $dc);
            }
        }

        $return = '<input type="hidden" name="'.$this->strName.'" id="ctrl_'.$this->strId.'" value="'.implode(',', array_keys($values)).'">
  <input type="hidden" name="'.$this->strOrderName.'" id="ctrl_'.$this->strOrderId.'" value="'.implode(',', array_keys($values)).'">
  <div class="selector_container">'
//            .((\count($values) > 1) ? '<p class="sort_hint">'.$GLOBALS['TL_LANG']['MSC']['dragItemsHint'].'</p>' : '')
            .' <ul id="sort_'.$this->strId.'" class="'.($this->sorting ? 'sortable' : '').'">';

        foreach ($values as $k => $v) {
            $return .= '<li style="cursor:move;" data-id="'.$k.'">'.$v.'</li>';
        }

        $return .= '</ul>';

        $pickerBuilder = $container->get('contao.picker.builder');

        if (!$pickerBuilder->supportsContext('news')) {
            $return .= '
	<p><button class="tl_submit" disabled>'.$GLOBALS['TL_LANG']['MSC']['changeSelection'].'</button></p>';
        } else {
            $extras = $this->getPickerUrlExtras();

            $return .= '
    <p><a href="'.ampersand($pickerBuilder->getUrl('news', $extras)).'" class="tl_submit" id="picker_'.$this->strName.'">'.$GLOBALS['TL_LANG']['MSC']['changeSelection'].'</a></p>
    <script>
      $("picker_'.$this->strName.'").addEvent("click", function(e) {
        e.preventDefault();
        Backend.openModalSelector({
          "id": "tl_listing",
          "title": '.json_encode($GLOBALS['TL_DCA'][$this->strTable]['fields'][$this->strField]['label'][0]).',
          "url": this.href + document.getElementById("ctrl_'.$this->strId.'").value,
          "callback": function(table, value) {
            new Request.Contao({
              evalScripts: false,
              onSuccess: function(txt, json) {
                $("ctrl_'.$this->strId.'").getParent("div").set("html", json.content);
                json.javascript && Browser.exec(json.javascript);
                $("ctrl_'.$this->strId.'").fireEvent("change");
              }
            }).post({"action":"reloadNewsPicker", "name":"'.$this->strName.'", "value":value.join("\t"), "REQUEST_TOKEN":"'.REQUEST_TOKEN.'"});
          }
        });
      });
    </script>';
        }

        $return = '<div>'.$return.'</div></div>';

        return $return;
    }

    protected function renderLabel(array $arrRow, DataContainer $dc)
    {
        $mode = $GLOBALS['TL_DCA'][$dc->table]['list']['sorting']['mode'] ?? 1;

        if (4 === $mode) {
            $callback = $GLOBALS['TL_DCA'][$dc->table]['list']['sorting']['child_record_callback'];

            if (\is_array($callback)) {
                $this->import($callback[0]);

                return $this->{$callback[0]}->{$callback[1]}($arrRow);
            }

            if (\is_callable($callback)) {
                return $callback($arrRow);
            }
        }

        $labelConfig = &$GLOBALS['TL_DCA'][$dc->table]['list']['label'];
        $label = vsprintf($labelConfig['format'], array_intersect_key($arrRow, array_flip($labelConfig['fields'])));

        if (\is_array($labelConfig['label_callback'])) {
            $this->import($labelConfig['label_callback'][0]);

            return $this->{$labelConfig['label_callback'][0]}->{$labelConfig['label_callback'][1]}($arrRow, $label, $dc, $arrRow);
        }

        if (\is_callable($labelConfig['label_callback'])) {
            return $labelConfig['label_callback']($arrRow, $label, $dc, $arrRow);
        }

        return $label ?: $arrRow['id'];
    }

    protected function validator($input)
    {
        if ($this->hasErrors()) {
            return '';
        }

        if (!$input) {
            if ($this->mandatory) {
                $this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['mandatory'], $this->strLabel));
            }

            return '';
        } elseif (false === strpos($input, ',')) {
            return $this->multiple ? [(int) $input] : (int) $input;
        }

        $value = array_map('intval', array_filter(explode(',', $input)));

        return $this->multiple ? $value : $value[0];
    }

    /**
     * Return the extra parameters for the picker URL.
     *
     * @return array
     */
    protected function getPickerUrlExtras()
    {
        $extras = [];
        $extras['fieldType'] = $this->multiple ? 'checkbox' : 'radio';
        $extras['source'] = $this->strTable.'.'.$this->currentRecord;

        return $extras;
    }
}
