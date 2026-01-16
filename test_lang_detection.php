<?php

use Illuminate\Http\Request;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$supported = ['es', 'en', 'fr', 'de', 'pt'];

$headers = [
    'es-ES,es;q=0.9,en-US;q=0.8,en;q=0.7', // Should pick 'es'
    'en-US,en;q=0.9,es;q=0.8',             // Should pick 'en'
    'pt-BR,pt;q=0.9',                      // Should pick 'pt'
    'de-DE,de;q=0.9',                      // Should pick 'de'
    'fr-CA,fr;q=0.9',                      // Should pick 'fr'
    'it-IT,it;q=0.9',                      // Should fallback to null (or handle default in middleware)
];

foreach ($headers as $h) {
    $request = Request::create('/', 'GET');
    $request->headers->set('Accept-Language', $h);
    
    $preferred = $request->getPreferredLanguage($supported);
    
    echo "Header: [$h] -> Matched: [" . ($preferred ?? 'NULL') . "]\n";
}
