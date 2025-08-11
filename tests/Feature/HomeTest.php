<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomeTest extends TestCase
{
    public function test_redirects_to_installer_when_not_installed(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/install');
    }
}
