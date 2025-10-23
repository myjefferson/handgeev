<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $procedure = "
            CREATE OR REPLACE FUNCTION cleanup_old_api_logs(retention_days INTEGER)
            RETURNS void AS $$
            BEGIN
                DELETE FROM api_request_logs 
                WHERE created_at < (CURRENT_TIMESTAMP - (retention_days || ' days')::INTERVAL);
            END;
            $$ LANGUAGE plpgsql;
        ";

        DB::unprepared($procedure);
    }

    public function down(): void
    {
        DB::unprepared('DROP FUNCTION IF EXISTS cleanup_old_api_logs(INTEGER);');
    }
};