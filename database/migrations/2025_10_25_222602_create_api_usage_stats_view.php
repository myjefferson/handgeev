<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW api_usage_stats AS
            SELECT 
                DATE(created_at) as date,
                user_id,
                workspace_id,
                COUNT(*) as total_requests,
                COUNT(CASE WHEN response_code >= 400 THEN 1 END) as failed_requests,
                AVG(response_time) as avg_response_time,
                MAX(response_time) as max_response_time
            FROM api_request_logs
            GROUP BY DATE(created_at), user_id, workspace_id
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS api_usage_stats');
    }
};