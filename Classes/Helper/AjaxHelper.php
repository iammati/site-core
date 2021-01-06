<?php

declare(strict_types=1);

namespace Site\Core\Helper;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Mvc\Exception\StopActionException;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * AjaxHelper are mostly used in Ajax classes.
 *
 * @author Mati <mati_01@icloud.com>
 *
 * @todo Rename/Refactor Helper to 'Abstract' maybe?
 */
class AjaxHelper
{
    /**
     * @var ServerRequestInterface
     */
    protected $serverRequest = null;

    /**
     * @var RequestHandlerInterface
     */
    protected $requestHandler = null;

    /**
     * Function to set this Ajax' request property.
     *
     * @param ServerRequestInterface $serverRequest
     *
     * @return void
     */
    public function setServerRequest($serverRequest)
    {
        $this->serverRequest = $serverRequest;
    }

    /**
     * Function to get this Ajax' requestp roperty.
     *
     * @return ServerRequestInterface
     */
    public function getServerRequest()
    {
        return $this->serverRequest;
    }

    /**
     * Function to set this Ajax' handler property.
     *
     * @param RequestHandlerInterface $requestHandler
     *
     * @return void
     */
    public function setRequestHandler($requestHandler)
    {
        $this->requestHandler = $requestHandler;
    }

    /**
     * Function to set this Ajax' handler property.
     *
     * @return void
     */
    public function getRequestHandler()
    {
        return $this->requestHandler;
    }

    /**
     * Redirects the request to another action and / or controller.
     *
     * Redirect will be sent to the client which then performs another request to the new URI.
     *
     * NOTE: This method only supports web requests and will thrown an exception
     * if used with other request types.
     *
     * @param string      $actionName     Name of the action to forward to
     * @param string|null $controllerName Unqualified object name of the controller to forward to. If not specified, the current controller is used.
     * @param string|null $extensionName  Name of the extension containing the controller to forward to. If not specified, the current extension is assumed.
     * @param array|null  $arguments      Arguments to pass to the target action
     * @param int|null    $pageUid        Target page uid. If NULL, the current page uid is used
     * @param int         $delay          (optional) The delay in seconds. Default is no delay.
     * @param int         $statusCode     (optional) The HTTP status code for the redirect. Default is '303 See Other
     *
     * @throws StopActionException
     *
     * @see forward()
     */
    protected function redirect($actionName, $controllerName, $extensionName = null, array $arguments = null, $pageUid = null, $delay = 0, $statusCode = 303)
    {
        $this->uriBuilder->reset()->setCreateAbsoluteUri(true);

        if (MathUtility::canBeInterpretedAsInteger($pageUid)) {
            $this->uriBuilder->setTargetPageUid((int) $pageUid);
        }

        if (GeneralUtility::getIndpEnv('TYPO3_SSL')) {
            $this->uriBuilder->setAbsoluteUriScheme('https');
        }

        $uri = $this->uriBuilder->uriFor($actionName, $arguments, $controllerName, $extensionName);
        $this->redirectToUri($uri, $delay, $statusCode);
    }

    /**
     * Redirects the web request to another uri.
     * NOTE: This method only supports web requests and will thrown an exception if used with other request types.
     *
     * @param mixed $uri        A string representation of a URI
     * @param int   $delay      (optional) The delay in seconds. Default is no delay.
     * @param int   $statusCode (optional) The HTTP status code for the redirect. Default is '303 See Other
     *
     * @throws StopActionException
     */
    protected function redirectToUri($uri, $delay = 0, $statusCode = 303)
    {
        $this->objectManager->get(CacheService::class)->clearCachesOfRegisteredPageIds();

        $uri = $this->addBaseUriIfNecessary($uri);
        $escapedUri = htmlentities($uri, ENT_QUOTES, 'utf-8');

        $this->response->setContent("<html><head><meta http-equiv='refresh' content='' ".(int) $delay.';url='.$escapedUri.'</head></html>');
        $this->response->setStatus($statusCode);
        $this->response->setHeader('Location', (string) $uri);

        // Avoid caching the plugin when we issue a redirect response
        // This means that even when an action is configured as cachable
        // we avoid the plugin to be cached, but keep the page cache untouched
        $contentObject = $this->configurationManager->getContentObject();
        if ($contentObject->getUserObjectType() === ContentObjectRenderer::OBJECTTYPE_USER) {
            $contentObject->convertToUserIntObject();
        }

        throw new StopActionException('redirectToUri', 1476045828);
    }

    /**
     * Adds the base uri if not already in place.
     *
     * @param string $uri The URI
     *
     * @return string
     */
    protected function addBaseUriIfNecessary($uri)
    {
        return GeneralUtility::locationHeaderUrl((string) $uri);
    }
}
