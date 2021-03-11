<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewspickerBundle\EventListener\Contao;

use Contao\Controller;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\Database;
use Contao\DataContainer;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use HeimrichHannot\NewspickerBundle\Widget\NewspickerWidget;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @Hook("executePostActions")
 */
class ExecutePostActionsListener
{
    public function __invoke(string $action, DataContainer $dc): void
    {
        if ('reloadNewsPicker' === $action) {
            $database = Database::getInstance();
            $intId = Input::get('id');
            $strField = $dc->inputName = Input::post('name');

            // Handle the keys in "edit multiple" mode
            if ('editAll' == Input::get('act')) {
                $intId = preg_replace('/.*_([0-9a-zA-Z]+)$/', '$1', $strField);
                $strField = preg_replace('/(.*)_[0-9a-zA-Z]+$/', '$1', $strField);
            }

            $dc->field = $strField;

            // The field does not exist
            if (!isset($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField])) {
                System::log('Field "'.$strField.'" does not exist in DCA "'.$dc->table.'"', __METHOD__, TL_ERROR);

                throw new BadRequestHttpException('Bad request');
            }

            $objRow = null;
            $varValue = null;

            // Load the value
            if ('overrideAll' != Input::get('act')) {
                if ($intId > 0 && $database->tableExists($dc->table)) {
                    $objRow = $database->prepare('SELECT * FROM '.$dc->table.' WHERE id=?')
                        ->execute($intId);

                    // The record does not exist
                    if ($objRow->numRows < 1) {
                        $this->log('A record with the ID "'.$intId.'" does not exist in table "'.$dc->table.'"', __METHOD__, TL_ERROR);

                        throw new BadRequestHttpException('Bad request');
                    }

                    $varValue = $objRow->$strField;
                    $dc->activeRecord = $objRow;
                }
            }

            // Call the load_callback
            if (\is_array($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['load_callback'])) {
                foreach ($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField]['load_callback'] as $callback) {
                    if (\is_array($callback)) {
                        $varValue = System::importStatic($callback[0])->{$callback[1]}($varValue, $dc);
                    } elseif (\is_callable($callback)) {
                        $varValue = $callback($varValue, $dc);
                    }
                }
            }

            // Set the new value
            $varValue = Input::post('value', true);

            // Convert the selected values
            if ($varValue) {
                $varValue = StringUtil::trimsplit("\t", $varValue);
                $varValue = serialize($varValue);
            }

            /** @var NewspickerWidget $strClass */
            $strClass = $GLOBALS['BE_FFL']['newsPicker'];

            Controller::loadDataContainer('tl_news');

            /** @var NewspickerWidget $objWidget */
            $objWidget = new $strClass($strClass::getAttributesFromDca($GLOBALS['TL_DCA'][$dc->table]['fields'][$strField], $dc->inputName, $varValue, $strField, $dc->table, $dc));

            throw new ResponseException(new Response($objWidget->generate()));
        }
    }
}
