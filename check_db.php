<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "DATABASE: " . DB::getDatabaseName() . "\n";
    $tables = DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    echo "TABLES:\n";
    foreach ($tables as $t) {
        echo "- " . $t->table_name . "\n";
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
