# Marshaller Bundle

[Marshaller](http://gnugat.github.io/marshaller) integration in [Symfony](http://symfony.com).

## Installation

MarshallerBundle can be installed using [Composer](http://getcomposer.org/):

    composer require "gnugat/marshaller-bundle:~1.0"

We then need to register it in our application:

```php
<?php
// File: app/AppKernel.php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Gnugat\MarshallerBundle\GnugatMarshallerBundle(),
        );
        // ...
    }

    // ...
}
```

## Simple conversion

Let's take the following object:

```php
<?php
// File: src/AppBundle/Entity/Article.php

namespace AppBundle\Entity;

class Article
{
    public function __construct($title, $content)
    {
        $this->title = $title;
        $this->content = $content;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }
}
```

If we want to convert it to the following format:

```php
array(
    'title' => 'Nobody expects...',
    'content' => '... The Spanish Inquisition!',
);
```

Then we have first to create a `MarshallerStrategy`:

```php
<?php
// File: src/AppBundle/Marshaller/ArticleMarshaller.php

use AppBundle\Entity\Article;
use Gnugat\Marshaller\MarshallerStrategy;

class ArticleMarshaller implements MarshallerStrategy
{
    public function supports($toMarshal, $category = null)
    {
        return $toMarshal instanceof Article;
    }

    public function marshal($toMarshal)
    {
        return array(
            'title' => $toMarshal->getTitle(),
            'content' => $toMarshal->getContent(),
        );
    }
}
```

The second step is to define it as a service:

```
# File: app/config/services.yml
services:
    app.article_marshaller:
        class: AppBundle\Marshaller\ArticleMarshaller
        tags:
            - { name: gnugat_marshaller }
```

> **Note**: Thanks to the `gnugat_marshaller` tag, the `ArticleMarshaller` will
> be registered in the main `gnugat_marshaller.marshaller` service.

Finally we can actually convert the object:

```php
<?php
// File: src/AppBundle/Controller/ArticleController.php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ArtcileController extends Controller
{
    /**
     * @Route("/api/v1/articles")
     * @Method({"GET"})
     */
    public function listAction()
    {
        $articles = $this->get('app.article_repository')->findAll();

        return new JsonResponse($this->get('gnugat_marshaller.marshaller')->marshal($articles), 200);
    }

    /**
     * @Route("/api/v1/articles/{id}")
     * @Method({"GET"})
     */
    public function viewAction(Article $article)
    {
        return new JsonResponse($this->get('gnugat_marshaller.marshaller')->marshal($article), 200);
    }
}
```

> **Note**: `gnugat_marshaller.marshaller` can also call the `ArticleMarshaller`
> on `Article` collections.

## Partial conversions

If we need to convert `Article` into the following format:

```php
array('title' => 'Nobody expects...');
```

Then we first have to define a new `MarshallStrategy`:

```php
// File: src/AppBundle/Marshaller/ArticleMarshaller.php

use AppBundle\Entity\Article;
use Gnugat\Marshaller\MarshallStrategy;

class PartialArticleMarshaller implements MarshallStrategy
{
    public function supports($toMarshal, $category = null)
    {
        return $toMarshal instanceof Article && 'partial' === $category;
    }

    public function marshal($toMarshal)
    {
        return array(
            'title' => $toMarshal->getTitle(),
        );
    }
}
```

Since this `MarshallerStrategy` has a more restrictive `support` condition, we'd
like it to be checked before `ArticleMarshaller`. This can be done by registering
`PartialArticleMarshaller` with a higher priority than `ArticleMarshaller`
(in this case with a priority higher than 0):

```php
# File: app/config/services.yml
services:
    app.article_marshaller:
        class: AppBundle\Marshaller\PartialArticleMarshaller
        tags:
            - { name: gnugat_marshaller, priority: 1 }
```

Finally we can call `Marshaller`, for the `partial` category:

```php
<?php
// File: src/AppBundle/Controller/ArticleController.php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ArtcileController extends Controller
{
    /**
     * @Route("/api/v1/articles")
     * @Method({"GET"})
     */
    public function listAction()
    {
        $articles = $this->get('app.article_repository')->findAll();

        return new JsonResponse($this->get('gnugat_marshaller.marshaller')->marshal($articles 'partial'), 200);
    }

    /**
     * @Route("/api/v1/articles/{id}")
     * @Method({"GET"})
     */
    public function viewAction(Article $article)
    {
        return new JsonResponse($this->get('gnugat_marshaller.marshaller')->marshal($article), 200);
    }
}
```

## Further documentation

You can see the current and past versions using one of the following:

* the `git tag` command
* the [releases page on Github](https://github.com/gnugat/marshaller/releases)
* the file listing the [changes between versions](CHANGELOG.md)

You can find more documentation at the following links:

* [copyright and MIT license](LICENSE)
* [versioning and branching models](VERSIONING.md)
* [contribution instructions](CONTRIBUTING.md)
