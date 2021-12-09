<?php

declare(strict_types=1);

namespace Site\Core\Http\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Site\Core\Service\AjaxService;
use Site\Core\Utility\StrUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This Middleware takes care of any incoming AJAX-requests - whether GET or POST,
 * all getting catched and handled if there's a given Ajax-Configuration which fits
 * to the requested URI.
 *
 * @see \Site\Core\Service\AjaxService::class
 */
class AjaxMiddleware implements MiddlewareInterface
{
    /**
     * Process method called internally by TYPO3 CMS within the MiddlewareInterface.
     *
     * This method will try to resolve the requested AJAX by accessing the process-method of a found
     * and configured AJAX-class. Those configuration can be defined by using the
     * AjaxService (\Site\Core\Service\AjaxService::class), which can register such configs.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $normalizedParams = $request->getAttribute('normalizedParams');
        $uri = $normalizedParams->getRequestUri();

        if (StrUtility::startsWith($uri, '/ajax')) {
            $postParams = $request->getParsedBody();
            $queryParams = $request->getQueryParams();

            $path = str_replace('/ajax/', '', $request->getUri()->getPath());

            if (StrUtility::endsWith($path, '/')) {
                $path = mb_substr($path, 0, -1);
            }

            $ajaxId = $queryParams['vendor'].'/'.$queryParams['ajax'];

            $method = 'process';

            $thisAjaxCfg = false;
            $ajaxConfigIdentifiers = GeneralUtility::makeInstance(AjaxService::class)->findAll();

            foreach ($ajaxConfigIdentifiers as $identifier => $ajaxConfigs) {
                foreach ($ajaxConfigs as $key => $cfg) {
                    if (str_contains($key, '-')) {
                        $splittedKey = explode('-', explode('/', $key)[1]);

                        if (explode('-', $queryParams['ajax'])[0] == $splittedKey[0] && '*' == $splittedKey[1]) {
                            if (isset($cfg['overwriteDynamicMethod']) && $cfg['overwriteDynamicMethod']) {
                                $method = $cfg['overwriteDynamicMethod'];
                            } else {
                                $method = (!empty(explode('-', $queryParams['ajax'])[1]) ? explode('-', $queryParams['ajax'])[1] : 'process');
                            }

                            $thisAjaxCfg = $cfg;
                        }
                    } else {
                        if ($ajaxId === $key) {
                            $thisAjaxCfg = $cfg;
                        }
                    }
                }
            }

            if (!$thisAjaxCfg) {
                $thisAjaxCfg = $ajaxConfigs[$ajaxId];
            }

            $classInstance = GeneralUtility::makeInstance($thisAjaxCfg['target'], ...($thisAjaxCfg['args'] ?? []));

            $classInstance->setServerRequest($request);
            $classInstance->setRequestHandler($handler);

            return $classInstance->{$method}([
                'post' => $postParams,
                'query' => $queryParams,
            ]);
        }

        return $handler->handle($request);
    }
}
