<?php

declare(strict_types=1);

namespace Site\Core\Service;

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class ModelService
{
    protected string $templatePath = 'Classes/Service/MODEL_TEMPLATE';

    /**
     * @param string $extKey     Key of your extension where the given Model should be created
     * @param string $namespace  Namespace of the model
     * @param string $modelName  Classname of the model
     * @param array  $properties Model properties e.g: $properties = ['string' => ['header' => '''']]
     */
    public static function generate(string $extKey, string $namespace, string $modelName, array $properties)
    {
        $modelPath = ExtensionManagementUtility::extPath($extKey).'Classes/Domain/Model/'.$modelName.'.php';

        if (!file_exists($modelPath)) {
            $flashUtility = new \Site\Core\Utility\FlashUtility();

            $mainPath = ExtensionManagementUtility::extPath('site_core');

            $templateFile = $mainPath.'Classes/Service/MODEL_TEMPLATE';
            $templateContent = file_get_contents($templateFile);

            $domainFolder = ExtensionManagementUtility::extPath($extKey).'Classes/Domain';
            $modelFolder = ExtensionManagementUtility::extPath($extKey).'Classes/Domain/Model';

            if (!is_dir($domainFolder) || !is_dir($modelFolder)) {
                return $flashUtility->message('Core (EXT:site_core) - Model Service', 'There\'s no such folder: $domainFolder AND/OR $modelFolder', 1);
            }

            $newModelFile = fopen($modelPath, 'w')
            or $flashUtility->message('Core (EXT:site_core) - Model Service', 'Couldn\'t create file - permissions?', 2);

            $content = self::updateContent($extKey, $templateContent, $namespace, $modelName, $properties);
            fwrite($newModelFile, $content);

            fclose($newModelFile);
        }
    }

    public static function updateContent(string $extKey, string $content, string $namespace, string $modelName, array $properties)
    {
        $content = str_replace('{EXTENSION_NAME}', $extKey, $content);
        $content = str_replace('{NAMESPACE}', $namespace, $content);
        $content = str_replace('{CLASS_NAME}', $modelName, $content);
        $content = str_replace('{COPYRIGHT_YEAR}', date('Y'), $content);

        $dataContent = '';

        foreach ($properties as $varType => $propertyDatas) {
            foreach ($propertyDatas as $property => $value) {
                $dataContent .= "
    protected {$varType} $".$property.' = '.$value.';
    ';
            }
        }

        foreach ($properties as $varType => $propertyDatas) {
            $i = 0;

            foreach ($propertyDatas as $property => $value) {
                ++$i;

                $setterGetter = '
    public function set'.ucfirst($property)."({$varType} \${$property}): self
    {
        \$this->{$property} = \${$property};

        return \$this;
    }

    public function get".ucfirst($property)."(): {$varType}
    {
        return \$this->{$property};
    }";
                if ($i === count($propertyDatas)) {
                    $setterGetter = "\n".$setterGetter;
                }

                $dataContent .= $setterGetter;
            }
        }

        if ($modelName === 'Ttcontent') {
            $content = str_replace('\TYPO3\CMS\Extbase\DomainObject\AbstractEntity', '\Site\SiteBackend\Domain\Model\BaseTtcontent', $content);
        }

        $dataContent = str_replace('$ ', '', $dataContent);
        $dataContent = str_replace('$ this', '$this', $dataContent);

        return str_replace('{CLASS_CONTENT}', $dataContent, $content);
    }
}
