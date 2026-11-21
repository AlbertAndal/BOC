<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::redirect('/admin/login', '/login');
Route::redirect('/admin/register', '/register');
Route::redirect('/manager/login', '/login');
Route::redirect('/broker/login', '/login');

Route::get('/setup-database', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();

        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        $seedOutput = \Illuminate\Support\Facades\Artisan::output();

        return response()->make("
            <html>
            <head><title>BOC Database Setup</title></head>
            <body style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif; padding: 40px; line-height: 1.6; max-width: 700px; margin: auto; background: #f8fafc; color: #1e293b;'>
                <div style='background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);'>
                    <h2 style='color: #16a34a; margin-top: 0;'>✅ Database Setup Successfully!</h2>
                    <p>All database tables, roles, and default accounts have been migrated and seeded into Wasmer MySQL.</p>
                    <div style='background: #0f172a; color: #38bdf8; padding: 15px; border-radius: 8px; font-family: monospace; white-space: pre-wrap; font-size: 13px; max-height: 300px; overflow-y: auto;'><strong>Migration Log:</strong>\n{$migrateOutput}\n<strong>Seeder Log:</strong>\n{$seedOutput}</div>
                    <div style='margin-top: 25px;'>
                        <a href='/login' style='background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: 600; display: inline-block;'>Go to Login</a>
                    </div>
                </div>
            </body>
            </html>
        ");
    } catch (\Throwable $e) {
        return response()->make("
            <html>
            <head><title>BOC Database Setup Error</title></head>
            <body style='font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif; padding: 40px; line-height: 1.6; max-width: 700px; margin: auto; background: #f8fafc; color: #1e293b;'>
                <div style='background: white; border: 1px solid #fecaca; border-radius: 12px; padding: 30px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);'>
                    <h2 style='color: #dc2626; margin-top: 0;'>❌ Database Setup Failed</h2>
                    <p style='color: #64748b;'>Failed to connect or migrate database with the provided settings:</p>
                    <pre style='background: #fef2f2; border: 1px solid #f87171; padding: 15px; border-radius: 8px; color: #991b1b; white-space: pre-wrap; font-family: monospace; font-size: 13px;'>" . htmlspecialchars($e->getMessage()) . "</pre>
                </div>
            </body>
            </html>
        ", 500);
    }
});
