<?php

return [
    'frontend' => [
        'site-core/ajax' => [
            'target' => \Site\Core\Http\Middleware\AjaxMiddleware::class,

            'after' => [
                'typo3/cms-frontend/authentication',
            ],
        ],
    ],
];
