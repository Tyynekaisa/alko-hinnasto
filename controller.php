<?php

function handleRequest() {

    $filters['TYPE'] = $_GET['type'] ?? null;
    $filters['LIMIT'] = $_GET['limit'] ?? 25;
    $filters['PAGE'] = $_GET['page'] ?? 0;
    $filters['COUNTRY'] = $_GET['country'] ?? null;
    $filters['PRICELOW'] = $_GET['priceLow'] ?? null;
    $filters['PRICEHIGH'] = $_GET['priceHigh'] ?? null;
    $filters['BOTTLESIZE'] = $_GET['bottleSize'] ?? null;
    $filters['ENERGYLOW'] = $_GET['energyLow'] ?? null;
    $filters['ENERGYHIGH'] = $_GET['energyHigh'] ?? null;

    return $filters;    
}
