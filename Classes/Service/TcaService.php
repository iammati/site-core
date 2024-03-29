<?php

declare(strict_types=1);

namespace Site\Core\Service;

use Exception;
use Psr\EventDispatcher\EventDispatcherInterface;
use Site\Core\Helper\ConfigHelper;
use Site\Core\Utility\FileUtility;
use Site\Core\Utility\FlashUtility;
use Site\Core\Configuration\Event\AfterCeDefaultTcaRetrievedEvent;
use Symfony\Component\Finder\Finder;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Container\Container;

/**
 * The TcaService provides useful methods and an effective way
 * of adding dynamically new tt_content AND for any other
 * table (e.g. tx_vendor_domain_model_accordion etc.) fields to have
 * it as a developer way more easier registrating those by using predefined
 * configuration files (see EXT:site_core/Configuration/Fields/*.php).
 *
 * It's also possible to edit what a default content element should contain.
 * Stuff like language, appearance (which is useful for custom spaces and or layouts of a CE).
 * Methods to append to an existing frame is also possible such as adding a configuration for
 * the sites-config.
 *
 * @author Mati <mati_01@icloud.com>
 */
class TcaService
{
    public const FILE_REFERENCE_NAMESPACE = '\TYPO3\CMS\Extbase\Domain\Model\FileReference';
    public const OBJECT_STORAGE_FILE_REFERENCE_NAMESPACE = '\TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>';
    protected static string $TCAServiceConfigs = __DIR__.'/../../Configuration/Fields';
    protected static FlashUtility $flashUtility;
    protected static EventDispatcherInterface $eventDispatcher;

    public function __construct()
    {
        $this->flashUtility = GeneralUtility::makeInstance(FlashUtility::class);
    }

    /**
     * Returns the array of a configured TCA type.
     *
     * @param string $type                  Image/Input/Select/... (TCA config-type)
     * @param string $fieldName             Fieldname (column inside your table where it gets saved e.g. ce_header)
     * @param string $label                 Label over your field (e.g. Header)
     * @param array  $additionalConfig      When overwriting the global configuration for extra values
     * @param array  $additionalFieldConfig An optional array for e.g. onChange => 'reload' etc
     *
     * @return array|bool
     * 
     * @deprecated since v3, will be removed in v4
     */
    public static function findConfigByType(
        string $type,
        string $fieldName,
        string $label = '',
        array $additionalConfig = [],
        array $additionalFieldConfig = []
    ): array|bool {
        self::$flashUtility = GeneralUtility::makeInstance(FlashUtility::class);

        $filepath = __DIR__.'/../../Configuration/Fields/'.$type.'.php';
        $realpath = realpath($filepath);

        $field = null;

        if (!realpath(self::$TCAServiceConfigs)) {
            self::$flashUtility->message('TcaService - Missing Configs-Directory', 'Following directory missing: '.self::$TCAServiceConfigs, 2);

            return 'TcaService Error - $TCAServiceConfigs.';
        }

        if (!$realpath) {
            self::$flashUtility->message('TcaService - Type: "'.$type.'".php does not exists', 'Create this file in order to make it working', 3);
        } else {
            $config = include $filepath;

            // If the type is image, then the setting
            // inside the config (only if it exists) the
            // foreign_match_fields-fieldname to the given one
            if ('Image' == $type) {
                $config['config']['foreign_match_fields']['fieldname'] ? $config['config']['foreign_match_fields']['fieldname'] = $fieldName : null;

                if (!empty($additionalConfig)) {
                    foreach ($additionalConfig as $key => $value) {
                        $config['config'][$key] = $value;
                    }
                }
            }

            // Replacing all necessary <{VARIABLES}> inside the Inline.php-
            // file with proper values given by $additionalConfig
            if ('Inline' == $type || 'Model' == $type) {
                $items = $additionalConfig['columns'];
                $nitems = '';

                if (!isset($items)) {
                    self::$flashUtility->message('TcaService - Type: $type.php', 'AdditionalConfig: "columns" not found!', 2);

                    return 'TcaService Error - Inline || Model.';
                }

                // Looping through $items for showitems for IRRE
                $size = count($items);
                $i = 1;
                foreach ($items as $item => $value) {
                    if ($i == $size) {
                        $nitems .= $item;
                    } else {
                        $nitems .= $item.', ';
                        ++$i;
                    }
                }

                // Overwriting placeholder variables inside the Inline.php file
                $config['ctrl']['title'] = $additionalConfig['title'];
                $config['ctrl']['label'] = $additionalConfig['label'];

                $config['ctrl']['searchFields'] = $nitems;

                // Show items
                $config['types']['showitem'] = str_replace('{SHOW_ITEMS}', $nitems, $config['types']['showitem'] ?? '');

                // Tab name
                $tabName = $additionalConfig['title'] ?? 'Item';
                $config['types']['showitem'] = str_replace('{TAB_NAME}', $tabName, $config['types']['showitem']);

                foreach ($additionalConfig['columns'] as $key => $column) {
                    $config['columns'][$key] = $column;
                }
            }

            // If there's no foreign_table in $additionalConfig it will throw a flash message
            // else overwriting it inside $config
            if ('InlineItem' == $type) {
                if (!isset($additionalConfig['foreign_table'])) {
                    new Exception('TcaService - Type: '.$type.'.php - $additionalConfig missing "foreign_table" value');
                }
                $config['config']['foreign_table'] = $additionalConfig['foreign_table'] ?? '';
            }

            // Slug field
            if ('Slug' == $type) {
                if (!isset($additionalConfig['generatorOptions']['fields'])) {
                    new Exception('TcaService - Type: '.$type.'.php - $additionalConfig missing "generatorOptions[fields]" for Slug-field as value');
                }
                $config['config']['generatorOptions']['fields'] = $additionalConfig['generatorOptions']['fields'] ?? '';
            }

            // Setting the key of $field to the configured array (which has been manipulated before)
            $field[$fieldName] = $config;

            // Label if there's no given when calling this static method, it will use the one inside the config file itself
            if ('' != $label) {
                $field[$fieldName]['label'] = $label;
            }

            // And in addition looping $additionalConfig and setting key => value to the one for the field
            if (!empty($additionalConfig)) {
                foreach ($additionalConfig as $a => $b) {
                    $field[$fieldName]['config'][$a] = $b;
                }
            }
        }

        return $field[$fieldName];
    }

    /**
     * Adds the content elements (inside BE as CType) to the select dropdown.
     *
     * @param array  $CTypes              E.g. ['Header Teaser Image' => ce_headerteaserimage].
     * @param string $itemGroupIdentifier Default is 'customelements'. The identifier of the itemGroup to add the select-items.
     */
    public static function addSelectItems(array $CTypes, string $itemGroupIdentifier = 'customelements')
    {
        foreach ($CTypes as $key => $CType) {
            ExtensionManagementUtility::addTcaSelectItem(
                'tt_content',
                'CType',
                [
                    $key,
                    $CType,
                    self::generateIconIdentifier($CType),
                    $itemGroupIdentifier,
                ]
            );
        }
    }

    /**
     * Shows the backend fields when creating a new record.
     *
     * @param string $CType            E.g. 'ce_headerteaserimage' or 'ce_slider'
     * @param string $fields           E.g. 'ce_header;Header,ce_rte;RTE'
     * @param string $additionalFields Optional. Just like the $fields-parameter.
     */
    public static function showFields(string $CType, string $fields, string $additionalFields = '')
    {
        if (!str_ends_with(trim($fields), ',')) {
            $fields .= ',';
        }

        if ('' != $additionalFields) {
            if (!str_ends_with($additionalFields, ',')) {
                $additionalFields .= ',';
            }

            $fields = $fields.$additionalFields;
        }

        $tabName = 'Content Element';

        $defaultShowitem = '
            --div--;{tabName},
                {fields},
                parentid,'.
            '
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:general,
                --palette--;;general,
                --palette--;;headers,
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.appearance,
                --palette--;;frames,
                --palette--;;appearanceLinks,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                --palette--;;language,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
                --palette--;;hidden,
                --palette--;;access,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:categories,
                categories,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:notes,
                rowDescription,
            --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended
        ';

        self::$eventDispatcher = GeneralUtility::makeInstance(EventDispatcherInterface::class);

        $event = new AfterCeDefaultTcaRetrievedEvent();
        $event
            ->setCType($CType)
            ->setShowitem($defaultShowitem)
            ->setFields($fields)
            ->setTabName($tabName)
        ;

        $showitem = static::$eventDispatcher->dispatch($event)->getShowitem();

        $showitem = str_replace('{tabName}', $event->getTabName(), $showitem);
        $showitem = str_replace('{fields}', $event->getFields(), $showitem);

        $GLOBALS['TCA']['tt_content']['types'][$CType]['showitem'] = $showitem;
    }

    /**
     * Adds an additional field for IRRE content-elements.
     * Everything else is the same as the above showFields-method.
     *
     * @method \Site\SiteBackend\Service\TcaService::showFields()
     *
     * @param string $CType  E.g. ce_headerteaserimage or ce_slider
     * @param string $fields E.g. fd_header;Header,fd_rte;RTE,
     */
    public static function showIrreFields(string $CType, string $fields)
    {
        self::showFields($CType, $fields, 'is_irre');

        self::columnsOverridesField($CType, [
            'is_irre' => [
                'config' => [
                    'value' => 1,
                ],
            ],
        ]);
    }

    /**
     * Overrides columns which has been defined inside tt_content.php (e.g. fd_select['config']['items'].
     *
     * @param string $CType     Self-explaning but could be e.g. 'fd_header'.
     * @param array  $overrides Array as like a default tt_content array field configuration
     */
    public static function columnsOverridesField(string $CType, array $overrides)
    {
        $GLOBALS['TCA']['tt_content']['types'][$CType]['columnsOverrides'] = $overrides;
    }

    /**
     * Custom way of adding tt_content palettes.
     *
     * @param string $palette       Unique name of your palette
     * @param string $fields        E.g. 'fd_header;Label,fd_rte;RTE,' etc.
     * @param array  $additionalArr In case of use for additional values
     * @param string $type          Either 'TCA' or 'SiteConfiguration'
     */
    public static function addPalette(
        string $palette,
        string $fields,
        array $additionalArr = [],
        string $type = 'TCA',
        string $table = 'tt_content'
    ) {
        $array = [
            'showitem' => $fields,
        ];

        if (!empty($additionalArr)) {
            $array = array_merge($array, $additionalArr);
        }

        switch ($type) {
            case 'SiteConfiguration':
                $GLOBALS['SiteConfiguration']['site']['palettes'][$palette] = $array;

                break;

            case 'TCA':
                $GLOBALS['TCA'][$table]['palettes'][$palette] = $array;

                break;

            default:
                self::$flashUtility->message('header', 'bodytext', 1);

                break;
        }
    }

    /**
     * @see \TYPO3\CMS\Core\Utility\ExtensionManagementUtility
     *
     * Copy of \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::class.
     * Purpose of this copy into TcaService is to avoid dozen of calls of 'TcaService::findConfigBy(...)'
     * and rather just calling it once which registers everything else by itself, instead of the developer.
     *
     * ===
     *
     * Adding fields to an existing table definition in $GLOBALS['TCA']
     * Adds an array with $GLOBALS['TCA'] column-configuration to the $GLOBALS['TCA']-entry for that table.
     * This function adds the configuration needed for rendering of the field in TCEFORMS - but it does NOT add the field names to the types lists!
     * So to have the fields displayed you must also call fx. addToAllTCAtypes or manually add the fields to the types list.
     * FOR USE IN files in Configuration/TCA/Overrides/*.php . Use in ext_tables.php FILES may break the frontend.
     *
     * @param string $table                The table name of a table already present in $GLOBALS['TCA'] with a columns section
     * @param array  $columnConfigurations The configuration-array with contains the additional columns (typical some fields an extension wants to add)
     */
    public static function addTCAcolumns(string $table, array $columnConfigurations)
    {
        foreach ($columnConfigurations as $fieldName => $config) {
            $type = $config[0];
            $label = $config[1] ?? $type;
            $additionalConfig = $config[2] ?? [];

            $fieldConfig = [
                $fieldName => self::findConfigByType($type, $fieldName, $label, $additionalConfig),
            ];

            if (is_array($GLOBALS['TCA'][$table]) && is_array($GLOBALS['TCA'][$table]['columns'])) {
                $GLOBALS['TCA'][$table]['columns'] = array_merge($GLOBALS['TCA'][$table]['columns'], $fieldConfig);
            }
        }
    }

    /**
     * Adds a new itemGroup to the TCA-CType configuration.
     */
    public static function addItemGroup(string $identifier, string $label)
    {
        $itemGroups = $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups'];
        unset($GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups']);

        $GLOBALS['TCA']['tt_content']['columns']['CType']['config']['itemGroups'] = \array_merge_recursive(
            [
                $identifier => $label,
            ],
            $itemGroups
        );
    }

    /**
     * Returns you the page contents of a domain model by its pid, the given column name and the value for the column in tt_content.
     *
     * @param int|string $pid    PageID
     * @param string     $column Table-column field
     * @param string     $value  Value of the given $column-field
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function findPageContentBy($pid, $column, $value)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tt_content');

        return $queryBuilder
            ->select('*')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('pid', $queryBuilder->createNamedParameter($pid)),
                $queryBuilder->expr()->eq($column, $queryBuilder->createNamedParameter($value))
            )
            ->execute()
        ;
    }

    /**
     * Adds TCA configuuration to a site configuration.
     *
     * @param string $tabName             The name of the tab
     * @param array $fieldConfigurations  An array of field configurations
     */
    public static function addSiteConfigurationTCA(string $tabName, array $fieldConfigurations)
    {
        $fieldNames = [];

        foreach ($fieldConfigurations as $fieldName => $config) {
            $GLOBALS['SiteConfiguration']['site']['columns'][$fieldName] = $config;

            $fieldNames[] = $fieldName;
        }

        $GLOBALS['SiteConfiguration']['site']['types']['0']['showitem'] .= ','."\n".'                --div--;'.$tabName.', '.implode(',', $fieldNames);
    }

    /**
     * Automatically allows records to be saved on standard pages where $key contains 'tx_myext_domain_model_record'.
     *
     * @param string $startsWith
     * @param bool $nameCondition
     */
    public static function allowTablesStartsWith(string $startsWith, bool $nameCondition = true)
    {
        foreach ($GLOBALS['TCA'] as $key => $tca) {
            if (str_starts_with($key, $startsWith) && ($nameCondition ? str_contains($key, '_domain_model_') : true)) {
                ExtensionManagementUtility::allowTableOnStandardPages($key);
            }
        }
    }

    /**
     * The fetcher and a helper function for loadCEs.
     * Can be used outside of TcaService.
     *
     * @param string $dir            path to 'EXT:/Configuration/TCA/Overrides/'
     * @param bool   $applyLocallang condition whether to apply the locallang or not which is not always necessary
     */
    public static function fetchCEs(string $dir, bool $applyLocallang = true): array
    {
        $CTypes = [];

        $finder = Finder::create()->in($dir)->name('ce_*.php')->files()->sortByName();

        foreach ($finder as $file) {
            $fileNameWithExtension = $file->getRelativePathname();
            $fileName = str_replace('.php', '', $fileNameWithExtension);

            if (str_starts_with($fileNameWithExtension, 'ce_')) {
                $CTypes[] = $fileName;
            }
        }

        if ($applyLocallang) {
            foreach ($CTypes as $key => $CType) {
                $identifier = 'Backend.ContentElements:'.getCeByCtype($CType, false);

                $localizedLabel = ll(
                    'site_backend',
                    $identifier
                )['title'] ?? $CType.' - "'.$identifier.'"-localization is not configured';

                $CTypes[$localizedLabel] = $CType;
                unset($CTypes[$key]);
            }
        }

        return $CTypes;
    }

    /**
     * Registers and adds automatically all configured content elements in the same directory
     * starting with the prefix 'ce_'. Those will also get added into the TCA select automatically.
     * SHOULD BE called only in /Configuration/TCA/Overrides/tt_content.php.
     *
     * @param string $dir The current __DIR__ passed
     */
    public static function loadCEs(string $dir, string $itemGroupIdentifier = 'customelements'): void
    {
        $CTypes = self::fetchCEs($dir);
        TcaService::addSelectItems($CTypes, $itemGroupIdentifier);

        foreach ($CTypes as $label => $CType) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['TCA_SERVICE']['loadedCEs'][$CType] = true;
        }
    }

    /**
     * Registers for each detected CType an own icon.
     * To know how the identifier gets generated take a look at the generateIconIdentifier-method.
     *
     * @see generateIconIdentifier()
     */
    public static function registerCEIcons(string $dir, string $extKey): void
    {
        /** @var IconRegistry */
        $iconRegistry = GeneralUtility::makeInstance(IconRegistry::class);

        $finder = Finder::create()->in($dir)->name('ce_*.php')->files()->sortByName();

        foreach ($finder as $file) {
            $fileNameWithExtension = $file->getRelativePathname();
            $fileName = str_replace('.php', '', $fileNameWithExtension);

            $identifier = str_replace('_', '-', $extKey).'-'.str_replace('_', '-', $fileName);
            $fileNameWithoutCE = str_replace('ce_', '', $fileName);

            $svgResourcesPath = 'Resources/Public/Icons/ContentElements/'.$fileNameWithoutCE.'.svg';
            $svgPath = ExtensionManagementUtility::extPath($extKey, $svgResourcesPath);

            if (file_exists($svgPath)) {
                $iconRegistry->registerIcon(
                    $identifier,
                    SvgIconProvider::class,
                    [
                        'source' => 'EXT:'.$extKey.'/'.$svgResourcesPath,
                    ]
                );
            }
        }
    }

    /**
     * Automatically registers IRRE items (e.g. irre_slide_item)
     * into TYPO3's TCA to avoid manually unnecessary registration within the
     * ExtensionManagementUtility - basically skipping this step when the file
     * has been detected by this method.
     *
     * SHOULD BE used inside Configuration/Overrides/TCA/tt_content.php.
     * Otherwise it may break things.
     *
     * @param string $path The path passed as __DIR__.'/..'
     */
    public static function registerIRREs(string $path, string $replacer): void
    {
        $path = realpath($path);

        if (!$path) {
            new Exception('The provided path "'.$path.'" doesn\'t seems to be fine for the TcaService::registerIRREs');
        }

        if (!str_ends_with($replacer, '_')) {
            $replacer .= '_';
        }

        $irrePrefix = ConfigHelper::get('site_backend', 'IRREs.itemPrefix') ?? 'irre_';

        $finder = FileUtility::retrieveFilesByPath($path)->name($replacer.'*.php');

        foreach ($finder as $file) {
            $fileNameWithExtension = $file->getRelativePathname();
            $fileName = str_replace('.php', '', $fileNameWithExtension);

            $origFileName = $fileName;

            $fileName = str_replace('domain_model_', '', $fileName);
            $fileName = str_replace($replacer, $irrePrefix, $fileName);

            $fileName .= '_item';

            $irreConfig = include $file;

            ExtensionManagementUtility::addTCAcolumns('tt_content', [
                $fileName => TcaService::findConfigByType('InlineItem', '', $irreConfig['title'], [
                    'foreign_table' => $origFileName,
                ]),
            ]);

            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['TCA_SERVICE']['loadedIRREs'][$origFileName] = true;
        }
    }

    /**
     * Generates the icon-identifier by the provided CType.
     */
    public static function generateIconIdentifier(string $CType): string
    {
        return 'site-backend-'.str_replace('_', '-', $CType);
    }

    /**
     * Registration of Backend-Previews - iterates automatically to all detected custom configured CEs.
     * Just requires the destination path where to find the ce_*.php-configured TCA files.
     *
     * @param string $dir Path to 'EXT:/Configuration/TCA/Overrides/'
     */
    public static function registerBackendPreviews(string $dir): void
    {
        if (ConfigHelper::get('site_backend', 'Backend.Preview.enabled') === true) {
            $CTypes = self::fetchCEs($dir, false);

            $renderer = ConfigHelper::get('site_backend', 'Backend.Preview.renderer');

            if ($renderer) {
                foreach ($CTypes as $CType) {
                    $GLOBALS['TCA']['tt_content']['types'][$CType]['previewRenderer'] = $renderer;
                }

                $GLOBALS['TCA']['tt_content']['ctrl']['previewRenderer'] = $renderer;
            }
        }
    }

    public static function config2properties(array $fields): array
    {
        $properties = [
            // inputs and texts
            'string' => [],

            // checkboxes
            'int' => [],

            // FileReferences (images, files etc.)
            self::FILE_REFERENCE_NAMESPACE => [],
        ];

        foreach ($fields as $fieldName => $field) {
            $type = $field['config']['type'];

            if ('input' == $type || 'text' == $type) {
                $properties['string'][$fieldName] = "''";
            }

            if ('check' == $type) {
                $properties['int'][$fieldName] = "''";
            }

            if ('inline' == $type) {
                $foreignTable = $field['config']['foreign_table'];
                $maxItems = $field['config']['maxitems'];

                $fileReferenceNamespace = self::FILE_REFERENCE_NAMESPACE;

                if ($maxItems > 1) {
                    $fileReferenceNamespace = self::OBJECT_STORAGE_FILE_REFERENCE_NAMESPACE;
                }

                if ('sys_file_reference' == $foreignTable) {
                    $properties[$fileReferenceNamespace][$fieldName] = 'null';
                } else {
                    $irresPrefix = ConfigHelper::get('site_backend', 'IRREs.prefix');

                    if (str_starts_with($foreignTable, $irresPrefix)) {
                        // @todo finish this
                    }
                }
            }
        }

        return $properties;
    }
}
