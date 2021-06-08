# Contao Newspicker Bundle

This bundle adds an newsPicker inputType to contao.

![](docs/img/screenshot_widget.png)

![](docs/img/screenshot_picker.png)

## Usage

### Setup

Install with composer or contao manager

### Usage in dca

```php
// Single news picker
$GLOBALS['TL_DCA']['fields']['newsSelect'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['newsSelect'],
    'inputType' => 'newsPicker',
    'eval'      => [
        'tl_class'  => 'clr',
        'multiple' => false,
    ],
    'sql'       => "int(10) unsigned NOT NULL default '0'",
];

// Multiple news picker
$GLOBALS['TL_DCA']['fields']['newsSelect'] = [
    'label'     => &$GLOBALS['TL_LANG']['tl_content']['newsSelect'],
    'inputType' => 'newsPicker',
    'eval'      => [
        'tl_class'  => 'clr',
        'multiple' => true,
    ],
    'sql'       => "blob NULL",
];
```



