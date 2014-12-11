<?php
<<<CONFIG
packages:
    - "twig/twig:1.16.0"
php-options:
    - "-d memory_limit=42M"
CONFIG;

echo sprintf('memory_limit=%s', ini_get('memory_limit'));
