<?php

require "./vendor/autoload.php";
use Shuchkin\SimpleXLSX;

//Excel to csv converter
function excelToCsv($uri, $outputCsv) {
    $excelContent = file_get_contents($uri);
    $tempFileName = __DIR__ . '/../data/alko.xlsx';
    file_put_contents($tempFileName, $excelContent);
    if ( $xlsx = SimpleXLSX::parse( $tempFileName )) {
        $f = fopen($outputCsv, 'wb');
        foreach ( $xlsx->readRows() as $r ) {
            fputcsv($f, $r, ';', '"', "\\", "\r\n");
        }
        fclose($f);
    } else {
        echo SimpleXLSX::parseError();
    }
}

?>