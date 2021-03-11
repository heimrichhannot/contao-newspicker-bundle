<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\NewspickerBundle\Picker;

use Contao\CoreBundle\Picker\PickerConfig;
use Contao\NewsBundle\Picker\NewsPickerProvider;

class OnlyNewsPickerProvider extends NewsPickerProvider
{
    public function getName(): string
    {
        return 'onlyNewsPicker';
    }

    public function supportsContext($context): bool
    {
        return 'news' === $context && parent::supportsContext('link');
    }

    public function getDcaAttributes(PickerConfig $config): array
    {
        $attributes = ['fieldType' => 'radio'];

        if ($fieldType = $config->getExtra('fieldType')) {
            $attributes['fieldType'] = $fieldType;
        }

        if ($source = $config->getExtra('source')) {
            $attributes['preserveRecord'] = $source;
        }

        if ($this->supportsValue($config)) {
            $attributes['value'] = array_map('intval', explode(',', $config->getValue()));
        }

        return $attributes;
    }

    public function supportsValue(PickerConfig $config): bool
    {
        foreach (explode(',', $config->getValue()) as $id) {
            if (!is_numeric($id)) {
                return false;
            }
        }

        return true;
    }

    public function convertDcaValue(PickerConfig $config, $value): string
    {
        return (int) $value;
    }
}
