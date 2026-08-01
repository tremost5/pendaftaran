<?php

namespace Tests\Feature;

use App\Models\WhatsAppLog;
use Tests\TestCase;

class WhatsAppLogModelTest extends TestCase
{
    public function test_whatsapp_log_model_uses_the_existing_database_table(): void
    {
        $this->assertSame('whatsapp_logs', (new WhatsAppLog())->getTable());
    }
}
