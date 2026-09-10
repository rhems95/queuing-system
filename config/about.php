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
    'description' => 'An anonymous campus queue system for PECIT frontline offices. Students confirm with a Student ID; 80mm thermal tickets and the public display show numbers only — never names. People without a record use a kiosk walk-in PIN instead of a Guard login. Staff share a fair 2 Priority → 1 Regular call order across counters, with Hold and independent service timers. Admins manage users, the kiosk PIN, students, and reports on completed tickets only.',
    'shortcut_hint' => 'Ctrl + Alt + Shift + A',
    'members' => [
        [
            'name' => 'Rhem Sumodlayon',
            'role' => 'Lead Developer / System Architect',
            'focus' => 'Backend, queue logic, staff tools, deployment',
            'photo' => 'rhem.png',
        ],
        [
            'name' => 'Mike Bedrona',
            'role' => 'Frontend / UI Designer',
            'focus' => 'Kiosk, display, and panel interface design',
            'photo' => 'mike.png',
        ],
        [
            'name' => 'Omar Bayabao',
            'role' => 'Database / QA Specialist',
            'focus' => 'Schema, testing, documentation, validation',
            'photo' => 'member3.jpg',
        ],
        [
            'name' => 'Niño Angelo Malicay',
            'role' => 'Research / Documentation Lead',
            'focus' => 'Capstone manuscript, diagrams, presentation',
            'photo' => 'member4.jpg',
        ],
        [
            'name' => 'Ron Matheo Arquisola',
            'role' => 'Hardware / Deployment Specialist',
            'focus' => 'Kiosk PC setup, printers, display launchers',
            'photo' => 'member5.jpg',
        ],
    ],
];
