<?php

declare(strict_types=1);

namespace App\Tests\Helper;

final class PatternHelper
{
    public const string DATE_PATTERN = '/^\d{4}-\d{2}-\d{2}T\d{2}\:\d{2}\:\d{2}\+\d{2}\:\d{2}$/';
    public const string UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i';
}
