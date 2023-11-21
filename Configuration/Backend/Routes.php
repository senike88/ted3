<?php

/**
 * Definitions of routes
 */
return [
 
    'ted3_new_content_element_wizard' => [
        'path' => '/ted3/content/wizard/new',
        'target' => \DS\Ted3\Controller\NewContentElementController::class . '::handleRequest',
    ]
];
?>