<?php

// duration & links limits
// 0 = unlimited
$limits['duration'] = 168;        // maximum duration hours
$limits['links'] = 0;   //maximum sharing links per user

// extra
$limits['nolimit'] = 0; // allow unlimited duration (=~ 100 years) with duration = 0 (the maximum duration limit is kept) [1 = yes | 0 = no]

// path on domain where a symlink to share.php can be found
// example: http://mydomain.com/share.php
// $downloadpath = '//'.$_SERVER['HTTP_HOST'].'/rutorrent/plugins/filemanager-share/share.php';
$downloadpath = 'http://mydomain.com/share.php';


return ['limits' => $limits,
        'endpoint' => $downloadpath,
        "key" => "mycu570m3ncryp710nk3y"

];
