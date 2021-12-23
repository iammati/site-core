## Frontend Rendering Engine — FRE

Using the <a href="https://github.com/iammati/site-frontend" target="_blank">site_frontend</a> TYPO3 extension,
your content elements will automatically be resolved, template paths won't be required or any kind of TypoScript like this:

```
tt_content.ce_rte =< lib.contentElement
tt_content.ce_rte {
    templateName = Rte

    dataProcessing {
       // ...
    }
}
```

at all.

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

**Requirements for another way instead of dataProcessing:**

* Entity (formerly known as Model)
* Repository

<details>
    <summary>
        Accordions — Entity
    </summary>

    ```
    <?php

    declare(strict_types=1);

    namespace Site\SiteBackend\Domain\Model;

    use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

    class Accordions extends AbstractEntity
    {
        protected string $header = '';

        public function setHeader(string $header): self
        {
            $this->header = $header;

            return $this;
        }

        public function getHeader(): string
        {
            return $this->header;
        }
    }
    ```

    Example code of an Accordion entity.
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

    /** Empty repository as placeholder to get magic methods just like findAll etc. */
    class AccordionsRepository extends Repository
    {
    }
    ```

    Example code of an Accordion repository.
    <div class="mb-1"></div>

    <div class="note">
        The repository can stay empty with no custom methods at all. It extends the default repository provided by TYPO3 which offers the main functionality e.g. `findAll` or `findByUid` which are essential for `site/site-core`.
    </div>
</details>

---

**Auto-Mapping of IRREs works only if the representative repository-class has been found. Otherwise nothing happens at all since you may use TypoScript or any other kind of rendering.**

The entities/repositories are able to be created automatically. This feature can be disabled though inside `EXT:site_frontend/Configuration/Config.php` under `ContentElements.rendering.autoGenerateModelRepos` since it's risky.

By default it's false and only respected if it's present inside your `Config.php` file + set to `true`.

---

If you have all requirements, you can create a new `EXT:site_frontend/Classes/Configuration/Listener/<Name of your CType>RenderingListener.php` (e.g. `EXT:site_frontend/Classes/Configuration/Listener/RteRenderingListener.php`) file and add the following code now:

<details>
    <summary>
        RteRenderingListener
    </summary>

    ```
    <?php

    declare(strict_types=1);

    namespace Site\Frontend\Configuration\Listener;

    use Site\Frontend\Configuration\Event\CTypeRenderingEvent;
    use TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper;
    use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

    class RteRenderingListener
    {
        protected DataMapper $dataMapper;
        protected ContentObjectRenderer $cObj;

        public function __construct(DataMapper $dataMapper)
        {
            $this->dataMapper = $dataMapper;
        }

        public function __invoke(CTypeRenderingEvent $event)
        {
            $this->cObj = $event->getCObj();

            $data = $this->cObj->data;
            $CType = $data['CType'];

            if ($CType === 'ce_rte') {
                $data['fd_rte'] = str_replace('<3', '❤️', $data['fd_rte']);
            }

            $this->cObj->data = $data;

            return $this->cObj;
        }
    }
    ```
</details>

<details>
    <summary>
        Services.yaml
    </summary>

    ```
    Site\Frontend\Configuration\Listener\RteRenderingListener:
      tags:
        - name: event.listener
          identifier: 'site-frontend/rendering-listener/rte'
          event: Site\Frontend\Configuration\Event\RteRenderingEvent
    ```
</details>

The custom `RteRenderingListener` event will transform – in case its CType equals `ce_rte` and your SQL/TCA column is named `fd_rte` (where `fd` stands for `field`) – '<3' into the heart-emoji now. (:

#### Automapping of IRRE fields from TCA to frontend view (template)

As mentioned above, IRREs are automatically mapped to e.g. `{data.irre_accordions_items}` inside your template.
