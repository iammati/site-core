## Frontend Rendering Engine — FRE

Using the <a href="https://github.com/iammati/site-frontend" target="_blank">site_frontend</a> TYPO3 extension,
your Content-Elements will automatically resolve template paths and won't require you to write any kind of TypoScript like this:

```
tt_content.ce_rte =< lib.contentElement
tt_content.ce_rte {
    templateName = Rte

    dataProcessing {
       // ...
    }
}
```

Instead you can use PHP and the _Frontend-Rendering-Engine_ provided by the <a href="https://github.com/iammati/site-frontend" target="_blank">site_frontend</a> extension.

#### Where are the template files located?

Take a look into `EXT:site_frontend/Configuration/Config.php` under the `ContentElements.rootPaths` section.
By default the value is equal to `EXT:site_frontend/Resources/Private/Fluid/Content/Templates/` for normal Content-Elements.

For IRREs (Inline Relational Record Editing) Content-Elements - such as an accordion, a slider or tiles - the path is `EXT:site_frontend/Resources/Private/Fluid/Content/Templates/IRREs/`.

#### How to do dataProcessing just like in TypoScript?

In TypoScript you would usually do something like this:

```
dataProcessing {
    10 = TYPO3\CMS\Frontend\DataProcessing\DatabaseQueryProcessor
    10 {
        if.isTrue.field = irre_accordions_item

        table = tx_sitebackend_domain_model_accordions
        pidInList = this

        where.field = uid
        where.intval = 1
        where.dataWrap = parentid = |

        as = accordions
    }
}
```

to fetch the raw database-table records (rows) of an inline field, e.g. for accordions of a Content-Element.

---

Cons for doing it like that are:

* Raw table values - means, there's no DataMapper which is called by TYPO3 which would identifiy a field being a FileReference or another IRRE etc.
* Each IRRE field you'd like to fetch would require this kind of `dataProcessing`.

While the pros using the FRE are:

* Automatically map within your model
* Customize the fetched records by listening within a `RenderingEvent`
* Data is passed to your view (fluidtemplate) as `data.<TCA / SQL fieldname>` e.g. `data.irres_accordions_item`

---

**Requirements:**

* Model (formerly also known as Entity)
* Repository

<details>
    <summary>
        Accordions — Model
    </summary>

    ```
    <?php

    declare(strict_types=1);

    namespace Site\SiteBackend\Domain\Model;

    use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

    class Accordions extends AbstractEntity
    {
        protected string $header = '';

        public function setHeader(string $header)
        {
            $this->header = $header;
        }

        public function getHeader(): string
        {
            return $this->header;
        }
    }
    ```

    Example code of an Accordions model.
    <div class="mb-1"></div>
</details>

<details>
    <summary>
        Accordions — Repository
    </summary>

    ```
    <?php

    declare(strict_types=1);

    namespace Site\SiteBackend\Domain\Repository;

    use TYPO3\CMS\Extbase\Persistence\Repository;

    class AccordionsRepository extends Repository
    {
    }
    ```

    Example code of an Accordions repository.
    <div class="mb-1"></div>

    <div class="note">
        The repository can stay empty with no custom methods at all. It extends the default repository provided by TYPO3 which offers the main functionality.
    </div>
</details>

---

**Auto-Mapping of IRREs works only if the representative repository-class has been found. Otherwise nothing happens at all since you may use TypoScript or any other kind of rendering.**

Since <a href="https://github.com/iammati/site-core" target="_blank">site-core</a> 2.0 the models and repositories are automatically created if the files are not present in your system. This feature can be disabled though inside `EXT:site_frontend/Configuration/Config.php` under `ContentElements.rendering.autoGenerateModelRepos`.

By default it's false and only respected if it's present inside your `Config.php` file.

---

If you have all requirements, you can create a new `EXT:site_frontend/Classes/Event/Rendering/<Name of your CType>RenderingEvent.php` (e.g. `EXT:site_frontend/Classes/Event/Rendering/RteRenderingEvent.php`) file and add the following code now:

```
<?php

declare(strict_types=1);

namespace Site\Frontend\Event\Rendering;

use Site\Frontend\Interfaces\CTypeRenderingInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class RteRenderingEvent implements CTypeRenderingInterface
{
    /**
     * Called before the HTML output of a record has been rendered.
     */
    public function beforeRendering(ContentObjectRenderer &$cObj)
    {
        // ...
    }

    /**
     * Called after the HTML output of a record has been rendered.
     */
    public function afterRendering(ContentObjectRenderer &$cObj)
    {
        // ...
    }
```

Those events are handled by the site-backend extension. You can use/manipulate the `ContentObjectRenderer $cObj` object within your custom Rendering-Event now.

#### Automapping of IRRE fields from TCA to frontend view (template)

As mentioned above, IRREs are automatically mapped to e.g. `{data.irre_accordions_items}` inside your template.
