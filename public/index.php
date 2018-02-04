<?php

include_once __DIR__ . '/../vendor/autoload.php';

BitCommunism\Marx\Instance::create(__DIR__ . '/../')->http()->serve();