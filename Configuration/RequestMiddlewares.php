<?php

return [
    'frontend' => [
        'site-core/ajax' => [
            'target' => \Site\Core\Middleware\AjaxMiddleware::class,

            'after' => [
                'typo3/cms-frontend/authentication',
            ],
        ],
    ],
];
