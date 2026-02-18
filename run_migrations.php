<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "Checking migrations status...\n";
    $exitCode = Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    $output = Illuminate\Support\Facades\Artisan::output();
    file_put_contents('migration_out.log', "Exit Code: $exitCode\nOutput:\n$output");
    echo "Done. Check migration_out.log\n";
} catch (\Exception $e) {
    $err = "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString();
    file_put_contents('migration_out.log', $err);
    echo "Error caught. Check migration_out.log\n";
}
