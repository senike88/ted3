<?php

$EM_CONF[$_EXTKEY] = array(
    'title' => 'TED³ (Base)',
    'description' => 'Frontendediting für TYPO3',
    'category' => 'Frontend',
    'version' => '11.2.0',
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
            'typo3' => '11.0.0-11.9.99',
            'fluid_styled_content' => '11.0.0-11.9.99',
            'typo3db_legacy' => '1.1.5-1.2.0',
        ),
        'conflicts' =>
        array(
        ),
    ),
);

