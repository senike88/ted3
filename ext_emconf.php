<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'TED³',
    'description' => 'Frontendediting for TYPO3 (Basic version)',
    'category' => 'Frontend',
    'version' => '12.2.7',
    'state' => 'beta',
    'uploadfolder' => true,
    'createDirs' => '',
    'clearcacheonload' => true,
    'author' => 'Dominik Strolz',
    'author_email' => 'strolzdominik@gmx.net',
    'author_company' => 'Zimmermann & Streiter',
    'constraints' =>
    array(
        'depends' =>
        array(
            'typo3' => '12.0.0-12.9.99',
            'fluid_styled_content' => '12.0.0-12.9.99'
        ),
        'conflicts' =>
        array(
        ),
    ),
);

