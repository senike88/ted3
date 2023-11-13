<?php
use TYPO3\CMS\Backend\Controller;

/**
 * Definitions for routes provided by EXT:backend
 * Contains all AJAX-based routes for entry points
 *
 * Currently the "access" property is only used so no token creation + validation is made
 * but will be extended further.
 */
return [

    // Expand or toggle in legacy database tree
//    'sc_alt_db_navframe_expandtoggle' => [
//        'path' => '/record/tree/expand',
//        'target' => Controller\PageTreeNavigationController::class . '::ajaxExpandCollapse'
//    ],
//      'ted3_new_content_element' => [
//        'path' => '/ted3/contentwizard',
//        'access' => 'public',
//        'target' => \DS\Ted3\Controller\BackendController::class . '::newcontentAction'
//    ],


];
