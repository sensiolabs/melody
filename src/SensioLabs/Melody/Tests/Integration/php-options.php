<?php
<<<CONFIG
packages:
    - "twig/twig:1.16.0"
php-options:
    - "-d memory_limit=42M"
CONFIG;

echo ini_get('memory_limit');
