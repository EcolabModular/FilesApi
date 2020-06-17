<?php

return [
    'disks' => [

        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app'),
        ],
        
        'files' => [
            'driver' => 'local',
            'root' => base_path().'/public/reportfiles',
            'visibility' => 'public',
        ],
        
    ],

];

?>