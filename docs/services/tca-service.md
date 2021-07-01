## Creating a new Content-Element

Assuming your backend / custom extension of your project is called <a href="https://github.com/iammati/site-backend" target="_blank">site_backend</a>.

We will create a RTE (Rich-Text-Editor) Content-Element in this example.

<div class="note">
    To understand how to render this Content-Element using the custom CTypes-Rendering instead of the typical TypoScript configuration, it's recommended to check that out here: <a href="../../rendering/ctypes/">Frontend-Rendering — CTypes</a>.
</div>

#### Creation of the new TCA field called `fd_rte`.

`fd` is a simple shortcut for `field`.

Create the `EXT:site_backend/Configuration/TCA/Overrides/tt_content.php` file and add the following snippet:

```
<?php

use Site\Core\Form\Fields;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

// Adds custom fields to the 'tt_content' table
ExtensionManagementUtility::addTCAcolumns('tt_content', [
    'fd_rte' => Fields\Rte::make('Label of the RTE'),
]);
```

to configure the new `fd_rte` field with a valid TCA array-configuration.

After the `Label of the RTE` (first argument) one can also pass an optional array which will be handled as a normal TCA configuration. It'll overwrite the default configuration of a TcaService-Field.

#### newContentElement-Wizard registration

Go into `EXT:site_backend/Configuration/TCA/Overrides/` and create a new file prefixed with `ce_`, e.g. `ce_rte.php`.

<div class="note">
    Note: Files must start with "ce_" inside the above path to make the TcaService detect and automatically load them — site-core will automatically add a new tab inside the wizard called "Elements" to render the detected elements and displays them.
</div>

Add the following code to the new created `ce_rte.php` file:

```
<?php

use Site\Core\Service\TcaService;

/**
 * Technically the same as
 * doing something like this:
 *
 * $GLOBALS['TCA']['tt_content']['types']['ce_rte']['showitem'] = 'fd_rte';
 *
 * just the fact that you would need to write all other default tabs such as general palette etc. either
 * by yourself or appending to the provided by TYPO3 one.
 */

TcaService::showFields(basename(__FILE__, '.php'), '
    ce_rte,
');

/**
 * TcaService::showFields provides by default a custom configured Content-Element tab and renders by default all
 * TYPO3 default tabs such as general, appearance, frame etc.
 * In addition, the second argument will only rendered inside the Content-Element tab.
 */
```

You can remove the comments. Just added for explanation what the actual functionality behind `showFields` is.

To make site-core use the newContentElement-Wizard hook provided by TYPO3, add the following snippet into your `EXT:site_backend/Configuration/TCA/Overrides/tt_content.php` file:

```
// Loads all in this __DIR__ configured CTypes starting with 'ce_'.
TcaService::loadCEs(__DIR__);
```

The newContentElement-Wizard should look like this now:

<figure>
    <img class="img-fluid" src="../tca-service-newcewizard.png" />

    <figcaption>The newContentElement-Wizard</figcaption>
</figure>

The `TcaService::showFields` method will render your pre-defined TCA fields, which you've defined before inside the `EXT:site_backend/Configuration/TCA/Overrides/tt_content.php` file.

Creating a new `ce_rte` should look like this now:

<figure>
    <img class="img-fluid" src="../tca-service-showfields.png" />

    <figcaption>View of creating a new ce_rte</figcaption>
</figure>

<!-- #### Localization of fields and title/description of the Content-Element

By default the extension `site_backend` provides a default localized structure.
In case you should miss it, take a look at the following figure:

<figure>
    <img class="img-fluid" src="../tca-service-localization.png" />

    <figcaption>File-Structure for localization</figcaption>
</figure> -->
