<?php

return [

    'debug'       => env('APP_DEBUG_PDF', false),
    'binpath'     => 'lib/',
    'binfile'     => env('WKHTML2PDF_BIN_FILE', 'wkhtmltopdf-amd64'),
    'output_mode' => 'I',
];
