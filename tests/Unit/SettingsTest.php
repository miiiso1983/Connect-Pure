<?php

namespace Tests\Unit;

use App\Services\Settings;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SettingsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        Schema::dropIfExists('system_settings');
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function test_set_and_get_single_setting(): void
    {
        /** @var Settings $settings */
        $settings = app(Settings::class);

        $this->assertNull($settings->get('whatsapp.enabled'));

        $settings->set('whatsapp.enabled', 'true');

        $this->assertSame('true', $settings->get('whatsapp.enabled'));
    }

    public function test_set_many_and_all(): void
    {
        /** @var Settings $settings */
        $settings = app(Settings::class);

        $settings->setMany([
            'whatsapp.sender' => '15551234567',
            'whatsapp.enabled' => 'true',
        ]);

        $all = $settings->all();

        $this->assertArrayHasKey('whatsapp.sender', $all);
        $this->assertArrayHasKey('whatsapp.enabled', $all);
        $this->assertSame('15551234567', $all['whatsapp.sender']);
        $this->assertSame('true', $all['whatsapp.enabled']);
    }
}
