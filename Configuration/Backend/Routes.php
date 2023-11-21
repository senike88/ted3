<?php

return [
    // deprecated
    'ted3_wizard_browse_links' => [
        'path' => '/ted3/link/browser',
        //   'path' => '/wizard/link/browse',
        'target' => \DS\Ted3\Controller\LinkBrowserController::class . '::mainAction'
    ],
    'ted3_new_content_element_wizard' => [
        'path' => '/ted3/content/wizard/new',
        'target' => \DS\Ted3\Controller\NewContentElementController::class . '::handleRequest',
    ],
    // deprecated
    'ted3_backend' => [
        'path' => '/ted3/backend',
        'target' => \DS\Ted3\Controller\BrowseLinksNewController::class . '::routeAction'
    ]
];
?>