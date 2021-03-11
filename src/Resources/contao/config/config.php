<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

/*
 * Hooks
 */
$GLOBALS['TL_HOOKS']['executePostActions']['huh_newspicker'] = [\HeimrichHannot\NewspickerBundle\EventListener\Contao\ExecutePostActionsListener::class, '__invoke'];

/*
 * Widgets
 */
$GLOBALS['BE_FFL']['newsPicker'] = \HeimrichHannot\NewspickerBundle\Widget\NewspickerWidget::class;
