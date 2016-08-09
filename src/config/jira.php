<?php

return [

    'default'     => env('JIRA_CONNECTION', 'example'),
    
    'connections' => [
        'example' => [
            'url'      => env('JIRA_URL',  'http://jira.mydomain.com'),
            'username' => env('JIRA_USER', 'johndoe'),
            'password' => env('JIRA_PASS', 'pass123'),
        ]
    ]
    
];
