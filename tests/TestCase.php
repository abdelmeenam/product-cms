<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\File;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        File::ensureDirectoryExists(base_path('tests/.runtime/views'));
        File::ensureDirectoryExists(base_path('tests/.runtime/disks'));

        config()->set('logging.default', 'errorlog');
        config()->set('view.compiled', base_path('tests/.runtime/views'));
    }
}
