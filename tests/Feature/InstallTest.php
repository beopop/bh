<?php

namespace Tests\Feature;

use Tests\TestCase;

class InstallTest extends TestCase
{
    public function test_installation_page_is_accessible_when_not_installed(): void
    {
        $response = $this->get('/install');
        $response->assertStatus(200);
    }
}
