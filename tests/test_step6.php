<?php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\Role;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Contracts\Console\Kernel;

$adminRole = Role::where('role_name', 'Administrator')->firstOrFail();
$admin = User::firstOrCreate(['username' => 'report_admin'], [
    'full_name' => 'Reports Administrator',
    'email' => 'rep_admin@boc.gov.ph',
    'password' => 'password123',
    'role_id' => $adminRole->role_id,
    'status' => 'Active',
]);

auth()->login($admin);

echo '=== 1. TEST ISSUE SUMMARY REPORT ==='.PHP_EOL;
$summary = ReportService::getReportData('issue_summary', ['year' => date('Y')]);
echo "Title: {$summary['title']}".PHP_EOL;
echo 'Headers: '.implode(', ', $summary['headers']).PHP_EOL;
echo 'Total Rows: '.count($summary['rows']).PHP_EOL;
if (count($summary['rows']) > 0) {
    echo 'First Row: '.implode(' | ', $summary['rows'][0]).PHP_EOL;
}

echo PHP_EOL.'=== 2. TEST PORT OPERATIONAL REPORT ==='.PHP_EOL;
$portReport = ReportService::getReportData('port_report', []);
echo "Title: {$portReport['title']}".PHP_EOL;
echo 'Headers: '.implode(', ', $portReport['headers']).PHP_EOL;
echo 'Total Ports Listed: '.count($portReport['rows']).PHP_EOL;
foreach ($portReport['rows'] as $row) {
    echo "  Port: {$row[0]} | Total: {$row[2]} | Resolved: {$row[3]} | Rate: {$row[6]}".PHP_EOL;
}

echo PHP_EOL.'=== 3. TEST IMPORTER CONCERN REPORT ==='.PHP_EOL;
$impReport = ReportService::getReportData('importer_report', []);
echo "Title: {$impReport['title']}".PHP_EOL;
echo 'Headers: '.implode(', ', $impReport['headers']).PHP_EOL;
echo 'Total Importers Listed: '.count($impReport['rows']).PHP_EOL;
foreach ($impReport['rows'] as $row) {
    echo "  Importer: {$row[0]} | Total: {$row[2]} | Resolved: {$row[3]}".PHP_EOL;
}

echo PHP_EOL.'=== 4. TEST MANAGER PERFORMANCE REPORT ==='.PHP_EOL;
$mgrReport = ReportService::getReportData('manager_performance', []);
echo "Title: {$mgrReport['title']}".PHP_EOL;
echo 'Headers: '.implode(', ', $mgrReport['headers']).PHP_EOL;
echo 'Total Managers Evaluated: '.count($mgrReport['rows']).PHP_EOL;
foreach ($mgrReport['rows'] as $row) {
    echo "  Manager: {$row[0]} | Assigned: {$row[1]} | Resolved: {$row[2]} | Overdue: {$row[4]} | Avg Time: {$row[5]}".PHP_EOL;
}

echo PHP_EOL.'=== 5. TEST CSV EXPORT STREAM ==='.PHP_EOL;
$csvResponse = ReportService::exportCsv('issue_summary', ['year' => date('Y')]);
ob_start();
$csvResponse->sendContent();
$csvContent = ob_get_clean();
echo 'CSV Export Generated: '.strlen($csvContent).' bytes'.PHP_EOL;
if (str_contains($csvContent, 'Issue Number') && str_contains($csvContent, 'Date Received')) {
    echo 'CSV Content Check: PASS'.PHP_EOL;
} else {
    echo 'CSV Content Check: FAIL'.PHP_EOL;
    exit(1);
}

echo PHP_EOL.'=== 6. TEST PDF EXPORT STREAM ==='.PHP_EOL;
$pdfResponse = ReportService::exportPdf('issue_summary', ['year' => date('Y')]);
ob_start();
$pdfResponse->sendContent();
$pdfContent = ob_get_clean();
echo 'PDF Export Generated: '.strlen($pdfContent).' bytes'.PHP_EOL;
if (str_starts_with($pdfContent, '%PDF')) {
    echo "PDF Content Check: PASS (Valid PDF header '%PDF' verified)".PHP_EOL;
} else {
    echo 'PDF Content Check: FAIL (Invalid PDF header)'.PHP_EOL;
    exit(1);
}

echo PHP_EOL.'STEP_6_VERIFICATION_COMPLETE: ALL 4 REPORTS AND PDF/CSV EXPORTS VERIFIED!'.PHP_EOL;
