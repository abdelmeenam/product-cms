<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_the_root_path_redirects_to_the_overview_page(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/overview');
    }
}
