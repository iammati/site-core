<?php

declare(strict_types=1);

namespace Site\Core\Form\Element;

use Site\Core\Service\TCAService;
use Site\Core\Utility\StandaloneViewUtility;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Core\Environment;

/**
 * This custom form-field just displays dynamically the current CType (locallized)
 * and the path to its icon.
 */
class CtypeNameElement extends AbstractFormElement
{
    public function render()
    {
        // Custom TCA properties and other data can be found in $this->data, for example the above
        // parameters are available in $this->data['parameterArray']['fieldConf']['config']['parameters']
        $result = $this->initializeResultArray();

        $uid = $this->data['vanillaUid'];
        $CType = $this->data['recordTypeValue'];

        /** @var ApplicationContext */
        $envContext = Environment::getContext();

        // $slIdentifier = 'LLL:EXT:'.getenv('BACKEND_EXT').'/Resources/Private/Language/Backend/ContentElements/CTypes/locallang.xlf:'.$CType;
        // $label = $this->getLanguageService()->sL($slIdentifier);

        // if (!$label) {
        //     $label = $slIdentifier;
        // }

        $identifier = 'Backend.ContentElements:'.str_replace('ce_', '', $CType);
        $label = ll(getenv('BACKEND_EXT'), $identifier)['title'] ?? $identifier;

        $iconIdentifier = TCAService::generateIconIdentifier($CType);

        $result['html'] = StandaloneViewUtility::render(
            [
                'Templates' => 'EXT:'.getenv('CORE_EXT').'/Resources/Private/Backend/',
            ],
            'Form/Element/CtypeName',
            [
                'data' => [
                    'uid' => $uid,

                    'CType' => $CType,
                    'CTypeLabel' => $label,
                    'iconIdentifier' => $iconIdentifier,

                    'isDevelopment' => (bool) $envContext->isDevelopment(),
                ],
            ]
        );

        return $result;
    }
}
