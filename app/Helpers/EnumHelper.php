<?php

namespace App\Helpers;

enum EnumHelper
{
    public const SHOW = 'show',
        HIDE = 'hide',
        CONTAINS = 'contains',
        EXACT = 'exact',
        STARTS_WITH = 'starts_with',
        ENDS_WITH = 'ends_with';
}
