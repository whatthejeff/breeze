<?php

    require_once 'Breeze.php';

    get('/', function(){
        display('index');
    });

    run();