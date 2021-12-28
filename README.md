# EXT:site-core - A TYPO3 Extension to use solid APIs during development

## Features
- Simplified the way to [create way faster new Content-Elements](https://site-core.readthedocs.io/en/main/services/tca-service/)
- An easier way of rendering custom Content-Elements via ["Frontend-Rendering-Engine"](https://site-core.readthedocs.io/en/main/rendering/engine/) (also PSR-14 Events!)
- Includes a bunch of custom utility-classes as services
- Will always support LTS versions of both TYPO3 and PHP - currently v11.5 and v8.1

## Why would I need this
I've developed this extension primarily only the due to the TcaService class since I struggled/didn't enjoy copy&paste TCA arrays - especially those `Table properties (ctrl)` arrays. That one big array you need for the usage of inline records, which you place inside e.g. `EXT:site_core/Configuration/TCA/Overrides/my_domain_model.php` and basically returns an array of configured ctrl stuff.

Turns out stuff like that (ctrl-array)...

<details>
  <summary>An example of the big, big ctrl-array</summary>
  
  ```php
  <?php

  return [
      'ctrl' => [
          'title' => 'LLL:EXT:site_core/Resources/Private/Language/locallang_db.xlf:tx_sitecore_domain_model_test',
          'label' => 'title',
          'tstamp' => 'tstamp',
          'crdate' => 'crdate',
          'cruser_id' => 'cruser_id',
          'versioningWS' => true,
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
          'searchFields' => 'title',
          'iconfile' => 'EXT:site_core/Resources/Public/Icons/tx_sitecore_domain_model_test.gif'
      ],
      'interface' => [
          'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title',
      ],
      'types' => [
          '1' => ['showitem' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, title, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, starttime, endtime'],
      ],
      'columns' => [
          'sys_language_uid' => [
              'exclude' => true,
              'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
              'config' => [
                  'type' => 'select',
                  'renderType' => 'selectSingle',
                  'special' => 'languages',
                  'items' => [
                      [
                          'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.allLanguages',
                          -1,
                          'flags-multiple'
                      ]
                  ],
                  'default' => 0,
              ],
          ],
          'l10n_parent' => [
              'displayCond' => 'FIELD:sys_language_uid:>:0',
              'exclude' => true,
              'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
              'config' => [
                  'type' => 'select',
                  'renderType' => 'selectSingle',
                  'default' => 0,
                  'items' => [
                      ['', 0],
                  ],
                  'foreign_table' => 'tx_sitecore_domain_model_test',
                  'foreign_table_where' => 'AND {#tx_sitecore_domain_model_test}.{#pid}=###CURRENT_PID### AND {#tx_sitecore_domain_model_test}.{#sys_language_uid} IN (-1,0)',
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
              'exclude' => true,
              'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.visible',
              'config' => [
                  'type' => 'check',
                  'renderType' => 'checkboxToggle',
                  'items' => [
                      [
                          0 => '',
                          1 => '',
                          'invertStateDisplay' => true
                      ]
                  ],
              ],
          ],
          'starttime' => [
              'exclude' => true,
              'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
              'config' => [
                  'type' => 'input',
                  'renderType' => 'inputDateTime',
                  'eval' => 'datetime,int',
                  'default' => 0,
                  'behaviour' => [
                      'allowLanguageSynchronization' => true
                  ]
              ],
          ],
          'endtime' => [
              'exclude' => true,
              'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
              'config' => [
                  'type' => 'input',
                  'renderType' => 'inputDateTime',
                  'eval' => 'datetime,int',
                  'default' => 0,
                  'range' => [
                      'upper' => mktime(0, 0, 0, 1, 1, 2038)
                  ],
                  'behaviour' => [
                      'allowLanguageSynchronization' => true
                  ]
              ],
          ],
          'sorting' => [
              'label' => 'sorting',
              'config' => [
                  'type' => 'passthrough',
              ]
          ],
          'title' => [
              'exclude' => true,
              'label' => 'LLL:EXT:site_core/Resources/Private/Language/locallang_db.xlf:tx_sitecore_domain_model_test.title',
              'config' => [
                  'type' => 'input',
                  'size' => 30,
                  'eval' => 'trim'
              ],
          ],
      ],
  ];
  ```

  **Note:** This example configuration covers only one field (the _title_) yet.

  <div class="mb-1"></div>
</details>

...could be way easier using `site/site-core`'s TcaService:

<details>
  <summary>TcaService way</summary>

  ```php
  <?php

  use Site\Core\Form\Fields;

  return Fields\Inline::make('Accordion', [
      'label' => 'rte',

      'columns' => [
          'header' => Fields\Input::make('Header'),
          'rte' => Fields\RTE::make('RTE'),
          'file' => Fields\File::make('File', [
              'fieldName' => 'file'
          ]),
          'image' => Fields\Image::make('Image', [
              'fieldName' => 'image'
          ]),
          'subaccords' => Fields\InlineItem::make('sub accordsss', [
              'config' => [
                  'foreign_table' => 'tx_sitebackend_domain_model_accordions'
              ]
          ]),
      ],
  ]);
  ```

  **Noticed** that this simplified configuration holds 5 fields already and is way more understandable than the other?
</details>

## Installation

`composer req site/site-core`

## Documentation

For documentation please head over to [site-core.readthedocs.io](https://site-core.readthedocs.io/)

## License

MIT

For more information please see [LICENSE](https://github.com/iammati/site-core/blob/main/LICENSE)
