<?php

/**
 * Capstone "About" team roster (secret page: Ctrl+Alt+Shift+A).
 *
 * Photos live in a hidden private folder (NOT under public/):
 *   storage/app/private/about/team/
 * They are served only via /about/photo/{filename} for names listed here.
 * If a photo is missing, an initials avatar is shown instead.
 */
return [
    'title' => 'About This Capstone',
    'project' => 'PECIT Queuing System',
    'institution' => 'Philippine Electronics and Communication Institute of Technology Inc.',
    'subtitle' => 'Capstone Research Project',
    'description' => 'An anonymous kiosk-based queue management system for campus frontline services — ticket issuance with thermal printing, fair priority scheduling, multi-counter staff operations, public display with voice announcing, and administrative reporting.',
    'shortcut_hint' => 'Ctrl + Alt + Shift + A',
    'members' => [
        [
            'name' => 'Rhem Sumodlayon',
            'role' => 'Lead Developer / System Architect',
            'focus' => 'Backend, queue logic, staff tools, deployment',
            'photo' => 'rhem.jpg',
        ],
        [
            'name' => 'Group Member 2',
            'role' => 'Frontend / UI Designer',
            'focus' => 'Kiosk, display, and panel interface design',
            'photo' => 'member2.jpg',
        ],
        [
            'name' => 'Omar Bayabao',
            'role' => 'Database / QA Specialist',
            'focus' => 'Schema, testing, documentation, validation',
            'photo' => 'member3.jpg',
        ],
        [
            'name' => 'Group Member 4',
            'role' => 'Research / Documentation Lead',
            'focus' => 'Capstone manuscript, diagrams, presentation',
            'photo' => 'member4.jpg',
        ],
        [
            'name' => 'Group Member 5',
            'role' => 'Hardware / Deployment Specialist',
            'focus' => 'Kiosk PC setup, printers, display launchers',
            'photo' => 'member5.jpg',
        ],
    ],
];
