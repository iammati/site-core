<?php

declare(strict_types=1);

namespace Site\Core\Form\Element;

use Site\Core\Service\TcaService;
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

        $identifier = 'Backend.ContentElements:'.getCeByCtype($CType, false);
        $label = ll('site_backend', $identifier)['title'] ?? $identifier;

        $iconIdentifier = TcaService::generateIconIdentifier($CType);

        $result['html'] = StandaloneViewUtility::render(
            [
                'Templates' => 'EXT:site_core/Resources/Private/Backend/',
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
