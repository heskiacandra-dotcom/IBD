<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * DCL (Data Control Language) Setup & Database Security.
     */
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            $dbName = config('database.connections.mysql.database', 'ruangwarga');

            try {
                // 1. Create dedicated least-privilege web application user
                DB::unprepared("CREATE USER IF NOT EXISTS 'app_ruangwarga'@'localhost' IDENTIFIED BY 'SecurePassWarga2026!';");

                // 2. Grant DML and Stored Procedure Execution on application database
                DB::unprepared("GRANT SELECT, INSERT, UPDATE, DELETE, EXECUTE ON `{$dbName}`.* TO 'app_ruangwarga'@'localhost';");

                // 3. Flush privileges
                DB::unprepared("FLUSH PRIVILEGES;");
            } catch (\Throwable $e) {
                // Log or ignore if MySQL root does not have global user creation privilege in test env
                logger()->info('DCL Setup Notice: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            try {
                DB::unprepared("DROP USER IF EXISTS 'app_ruangwarga'@'localhost';");
                DB::unprepared("FLUSH PRIVILEGES;");
            } catch (\Throwable $e) {
                logger()->info('DCL Rollback Notice: ' . $e->getMessage());
            }
        }
    }
};
