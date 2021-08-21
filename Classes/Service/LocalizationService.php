<?php

declare(strict_types=1);

namespace Site\Core\Service;

use Site\Core\Helper\ConfigHelper;
use Site\Core\Utility\ExceptionUtility;
use Site\Core\Utility\StrUtility;
use Symfony\Component\Finder\Finder;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Http\ApplicationType;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LocalizationService
{
    protected string $extKey = '';
    protected array $destinations = [];
    protected BackendUserService $backendUserService;

    public function __construct()
    {
        $this->backendUserService = GeneralUtility::makeInstance(BackendUserService::class);
    }

    /**
     * Defines the extension key of this.
     *
     * @param string $extKey the extension key for this instanced localization-service
     */
    public function setExtKey(string $extKey)
    {
        $this->extKey = $extKey;
    }

    /**
     * Retrieves the extension-key of this instances localization-service.
     */
    public function getExtKey(): string
    {
        return $this->extKey;
    }

    /**
     * Adds a destination-path, with an identifier, where localization/translated files are left.
     */
    public function addDestination(string $identifier, string $destination)
    {
        if (isset($this->destinations[$identifier])) {
            return false;
        }

        $this->destinations[$identifier] = $destination;
    }

    /**
     * Retrieves a configured localization by the given identifier.
     */
    public function findConfigByIdentifier(string $identifier): bool
    {
        return $this->getLocalizationService()[$identifier] ?? false;
    }

    /**
     * Registration of a locallization.
     * Since TYPO3 doesn't requires such thing, it's necessary to register,
     * so the global ll-helper function knows where to find what - e.g. where to search
     * for the localized-php files as a given identifier.
     *
     * @param string $extKey      The key of the extension
     * @param array  $definitions An array of definitions
     *
     * @see Take a look in EXT:/helpers.php for more.
     */
    public function register(string $extKey, array $definitions)
    {
        if (isset($this->getLocalizationService()[$extKey])) {
            return false;
        }

        $this->setExtKey($extKey);

        $finder = new Finder();

        foreach ($definitions as $identifier => $definition) {
            $identifier = $identifier;

            if (is_numeric($identifier) || is_int($identifier) || 0 === $identifier) {
                unset($definitions[$identifier]);

                $identifier = 'default';
            }

            $extPath = GeneralUtility::getFileAbsFileName('typo3conf/ext/'.$extKey).'/';
            $definitionPath = $extPath.$definition;

            $localizations = [];
            $finder->files()->in($definitionPath)->name('*.php');

            foreach ($finder as $file) {
                $languageIdentifier = explode('/', $file->getRelativePath())[0];
                $localizations[$languageIdentifier] = include_once $file->getRealPath();
            }

            $this->addDestination($identifier, $definition);

            $definitions[$identifier] = $definition;
        }

        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['LOCALIZATION_SERVICE'][$extKey] = [
            'definitions' => $definitions,
            'localizations' => $localizations,
        ];
    }

    /**
     * Checks if the given $extKey-string has been registered yet or not.
     */
    public function has(string $extKey): bool
    {
        if (isset($this->getLocalizationService()[$extKey])) {
            return true;
        }

        return false;
    }

    /**
     * This is the actual logic which finds a locallized / translated string
     * by the given $extKey-string as the targeted $key-string.
     *
     * @throws ExceptionUtility
     */
    public function findByKey(string $extKey, string $key, string $twoLetterIsoCode = ''): mixed
    {
        $backendExt = env('BACKEND_EXT') ?: 'BACKEND_EXT';
        $localizationType = strtolower(ConfigHelper::get($backendExt, 'localizationType') ?? 'custom');

        $localizedStr = '';

        switch ($localizationType) {
            case 'xliff':
                $key = str_replace('.', '/', $key);
                $key = str_replace(':', '.xlf:', $key);
                $input = 'LLL:EXT:'.$extKey.'/Resources/Private/Language/'.$key;
                $localizedStr = $this->getLanguageService()->sL($input);

                break;

            case 'custom':
                $language = $twoLetterIsoCode ?: $this->getLanguage();

                if (!isset($this->getLocalizationService()[$extKey])) {
                    $localizedStr = 'EXT:'.$extKey.' has not been configured yet for the LocalizationService!';
                } else {
                    $config = $this->getLocalizationService()[$extKey];
                    $localizations = $config['localizations'];

                    if (StrUtility::contains($key, '.') || StrUtility::contains($key, ':')) {
                        $explodedKey = explode('.', $key);
                        $implodedKey = implode('/', $explodedKey);

                        $explodedPathLabel = explode(':', $implodedKey);

                        $extPath = ExtensionManagementUtility::extPath($extKey);

                        foreach ($config['definitions'] as $definition) {
                            $path = $extPath.$definition.$language.'/'.$explodedPathLabel[0].'.php';

                            if (!file_exists($path)) {
                                ExceptionUtility::throw(
                                    'LocalizationService: The "'.$path.'" localization-file does not exists!',
                                    1628704798
                                );
                            }

                            $locallizedArr = include $path;
                            $locallizedKey = $locallizedArr[$explodedPathLabel[1]];

                            if (isset($locallizedKey)) {
                                $localizedStr = $locallizedKey;
                            }
                        }
                    } else {
                        if (isset($localizations[$language][$key])) {
                            $localizedStr = $localizations[$language][$key];
                        } else {
                            ExceptionUtility::throw('LocalizationService: The "'.$extKey.'" localizations does not contain a key for "'.$key.'" for language "'.$language.'".');
                        }
                    }
                }

                break;

            default:
                ExceptionUtility::throw(
                    sprintf(
                        'There must be a configured localizationType to either "xliff" or "custom" inside EXT:%s/Config.php:localizationType value to handle whether you want to use XLIFF or EXT:%s\'s custom ll-function!',
                        $backendExt,
                        $backendExt
                    )
                );

                break;
        }

        return $localizedStr;
    }

    public function findAllLocalizations()
    {
        return $this->getLocalizationService();
    }

    protected function getLocalizationService()
    {
        return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['site_core']['LOCALIZATION_SERVICE'];
    }

    protected function getBEUser()
    {
        if (null === $GLOBALS['BE_USER']) {
            Bootstrap::initializeBackendUser();
        }

        return $GLOBALS['BE_USER'];
    }

    protected function getLanguage(): string
    {
        $lang = 'en';

        if (ApplicationType::fromRequest(serverRequest())->isBackend()) {
            $uc = unserialize($this->getBEUser()->user['uc'] ?? '');
            $lang = $uc['lang'] ?: 'en';

            if ($lang == 'default') {
                $lang = 'en';
            }
        }

        return $lang;
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'] ?? GeneralUtility::makeInstance(LanguageService::class);
    }
}
