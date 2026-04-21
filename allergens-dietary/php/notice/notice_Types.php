<?php

if (!defined('ABSPATH')) {
    exit;
}

enum Allergens_Dietary_Notice_Types: string
{
    case ERROR = 'notice-error';
    case WARNING = 'notice-warning';
    case SUCCESS = 'notice-success';
    case INFO = 'notice-info';
}
