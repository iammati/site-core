<?php

declare(strict_types=1);

namespace Site\Core\Hook;

use TYPO3\CMS\Backend\Template\Components\ButtonBar;
use TYPO3\CMS\Backend\Template\Components\Buttons\InputButton;
use TYPO3\CMS\Core\Imaging;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Add an extra save and close button at the end.
 *
 * @author Goran Medakovic https://github.com/avionbg/save_close_ce
 * @author Mati <mati_01@icloud.com>
 */
class SaveCloseHook
{
    /**
     * Adds the "save and close"-button to TYPO3's doktype-header.
     */
    public function addButton($params, &$buttonBar): array
    {
        $buttons = $params['buttons'];

        if (!isset($buttons[ButtonBar::BUTTON_POSITION_LEFT][2])) {
            return $buttons;
        }

        $saveButton = $buttons[ButtonBar::BUTTON_POSITION_LEFT][2][0];

        if ($saveButton instanceof InputButton) {
            $iconFactory = GeneralUtility::makeInstance(Imaging\IconFactory::class);

            $saveCloseButton = $buttonBar->makeInputButton()
                ->setName('_saveandclosedok')
                ->setValue('1')
                ->setForm($saveButton->getForm())
                ->setTitle($this->getLanguageService()->sL('LLL:EXT:core/Resources/Private/Language/locallang_core.xlf:rm.saveCloseDoc'))
                ->setIcon($iconFactory->getIcon('actions-document-save-close', Imaging\Icon::SIZE_SMALL))
                ->setShowLabelText(true)
            ;

            $buttons[ButtonBar::BUTTON_POSITION_LEFT][2][] = $saveCloseButton;
        }

        return $buttons;
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
