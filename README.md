EXT:site-core
---
**A must-have core extension for new TYPO3 applications - especially a life-saver when working with TCA - but also comes handy for any other use-case.**

---

## Installation
Simple `composer req site/site-core`

---

#### Okay, so what can I do now?

**<u>The TCA-Service</u>**

You might know this use-case when creating a custom field in TCA:

```
'input_1' => [
    'label' => 'input_1',
    'config' => [
        'type' => 'input',
    ],
],

Source: https://docs.typo3.org/m/typo3/reference-tca/master/en-us/ColumnsConfig/Type/inputDefault.html?highlight=input
```

While this looks easy, this still could get **more easier** using:

```
use Site\Core\Form\Fields;

...

'input_1' => Fields\Input::make('input_1'),
```

A more relatable situation would be the big **ctrl**-array for **inline-records - IRRE**:

<details>
    <summary>Click to see the big ctrl-array code</summary>

    'ctrl' => [
        'title' => 'Accordion',
        'label' => 'rte',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => 1,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'sortby' => 'sorting',

        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],

        'searchFields' => 'header,rte',
    ],

    'palettes' => [
        'language' => [
            'showitem' => '
                sys_language_uid,
                l10n_parent,
                l10n_diffsource
            ',
        ],

        'timeRestriction' => [
            'showitem' => '
                starttime,
                endtime
            ',
        ],
    ],

    'types' => [
        '1' => [
            'showitem' => '
                --div--;Accordion,
                    header,
                    rte,
                    parentid,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                    hidden,
                    --palette--;;timeRestriction,
            ',
        ],
    ],

    'columns' => [
        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',

            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'special' => 'languages',
                'default' => 0,

                'items' => [
                    [
                        'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                        -1,
                        'flags-multiple',
                    ],
                ],
            ],
        ],

        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],

        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',

            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ],
        ],

        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',

            'config' => [
                'type' => 'check',

                'items' => [
                    '1' => [
                        '0' => 'LLL:EXT:core/locallang_core.xlf:labels.enabled',
                    ],
                ],
            ],
        ],

        'starttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',

            'config' => [
                'type' => 'input',
                'size' => 13,
                'default' => 0,
                'eval' => 'datetime',

                'behaivour' => [
                    'allowLanguageSynchronization' => 1,
                ],
            ],
        ],

        'endtime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',

            'config' => [
                'type' => 'input',
                'size' => 13,
                'default' => 0,
                'eval' => 'datetime',

                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2040),
                ],

                'behaivour' => [
                    'allowLanguageSynchronization' => 1,
                ],
            ],
        ],

        'parentid' => [
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',

                'foreign_table' => 'tt_content',
                'foreign_table_where' => 'AND tt_content.pid=###CURRENT_PID### AND tt_content.sys_language_uid IN (-1,###REC_FIELD_sys_language_uid###)',

                'items' => [
                    ['', 0],
                ],
            ],
        ],
    ],
</details>

<br/>
Instead of doing this inside
```EXT:myext/Configuration/TCA/tx_myvendor_domain_model_accordions```
one would do via the TCA-Service the following approach:

```
use Site\Core\Form\Fields\Input;
use Site\Core\Form\Fields\RTE;
use Site\Core\Service\TCAService;

return TCAService::findConfigByType('Inline', basename(__FILE__, '.php'), '', [
    // Text of an IRRE as a preview of the Inline-Record item / accordion in the BE
    'label' => 'rte',

    // Create new <title>
    'title' => 'Accordion',

    'columns' => [
        'header' => Input::make('Header'),
        'rte' => RTE::make('RTE'),
    ],
]);

```

This would take-off a lot of unnecessary or repeating configuration from the developers means more productive-time.

You may want to look at https://packagist.org/packages/site/site-backend which has 1x inline content element (accordions) and 1x "default" content element (image and RTE).

---

**<u>TCA-Service's auto-content-element-registration-thingy</u>**
Yes. This is the best name you could imagine, and the best thing is: it does what it says!


You can use `TCAService::registerIRREs(__DIR__.'/..', 'tx_typo3skeleton_domain_model_');` in your `EXT:myext/Configuration/TCA/Overrides/tt_content.php` for your custom configured TCA table-files which are placed in `EXT:myext/Configuration/TCA/<table>.php` to register automatically the '`irre_{suffix}_item`'-field to use them inside `EXT:myext/Configuration/TCA/Overrides/ce_{suffix}.php`.

*That sounds very complicated!*
So, let's use a real use-case example.
We configured in the upper-part of the TCA-Service the table/ctrl TCA configuration for our accordion. To create now inline records of an accordion we have to make a new TCA field with the type `inline` (https://docs.typo3.org/m/typo3/reference-tca/master/en-us/ColumnsConfig/Type/Inline.html).

Those are, when calling the `TCAService::registerIRREs`-method automatically registered. It'll iterate through `EXT:myext/Configuration/TCA/<table>.php` files and replaces using the 2nd parameter (which we passed above as `tx_typo3skeleton_domain_model_`) with nothing (an empty string).
What left is 'accordions.php' (from `EXT:myext/Configuration/TCA/tx_typo3skeleton_domain_model_accordions.php`). The `.php` will also be removed. So at the end we got `accordions` now.

That's called the suffix now. TCAService registers then new fields called with the prefix `'irre_' + suffix + '_item'`.
So in our case `irre_accordions_item`. To display that one in the backend now, we would have to make a `EXT:myext/Configuration/TCA/Overrides/ce_accordions.php` where we put the following code:

```
<?php

defined('TYPO3_MODE') || die('Access denied.');

use Site\Core\Service\TCAService;

TCAService::showFields(basename(__FILE__, '.php'), '
    irre_accordions_item,
');
```

All left is the SQL to create the table normally as you would do usually and a TypoScript to make the frontend fluid template. :D

---

We come to two other handy functions of TCAService called `showFields` as we just did above and `columnsOverridesField`.

We have an use-case example for showFields already so we only need to know what columnsOverridesField does.
Basically it's the same one from the `$GLOBALS` - just with a cleaner `API` call rather than writing that ugly `$GLOBALS` always before.

```
<?php

defined('TYPO3_MODE') || die('Access denied.');

use Site\Core\Service\TCAService;

TCAService::showFields(basename(__FILE__, '.php'), '
    ce_rte,
');

TCAService::columnsOverridesField(basename(__FILE__, '.php'), [
    'ce_rte' => [
        'config' => [
            'default' => 'Some default text for the RTE',
        ],
    ],
]);
```

The method itself just put itself into the columnsOverrides into the `$GLOBALS` where TYPO3 itself handles the override.

You also noticed we never hardcoded the filename, that's because if we want to change a filename because we either don't like it or have to rename it for whatever reason, `basename(__FILE__, '.php')` retrieves the current php-script filename and removes the '.php'-suffix at the end.

The `TCAService::loadCEs(__DIR__);` can and should be called only in `EXT:myext/Configuration/TCA/Overrides/tt_content.php`.
This method automatically adds the configured `EXT:myext/Configuration/TCA/Overrides/ce_*.php`-files into TYPO3's select items for content-elements.

Also there's a hook which registers automatically the content-elements into the newContentElement-Wizard of TYPO3 to skip the "Page-TSconfig"-configuration. Laziness = Time-saver! :D

---

A recommendation is to look / overfly not the Classes but the usage where or how to use the EXT:site-core. It can gets really handy.

---

An important note is that it automatically loads dot-env file inside the `EXT:site_core/helpers.php` since it requires from there some pre-configured stuff. But you'll catch/notice those when you read until here and looked into the extension itself! :)

Enjoy the extension in combination with site-backend/-frontend! <3
