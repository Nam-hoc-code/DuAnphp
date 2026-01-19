<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Cloudinary\Configuration\Configuration;

Configuration::instance([
    'cloud' => [
        'cloud_name' => 'devetmefh',
        'api_key'    => '592532872642166',
        'api_secret' => 'YhA1u30lheJ6wyp4ba1HMhyS4wE',
    ],
    'url' => [
        'secure' => true
    ]
]);
    