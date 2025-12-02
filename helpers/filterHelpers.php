<?php

function describeFilters($filters) {
    $desc = [];
    if (!empty($filters['country'])) $desc[] = "Valmistusmaa: {$filters['country']}";
    if (!empty($filters['type'])) $desc[] = "Juoman tyyppi: {$filters['type']}";
    if (!empty($filters['bottleSize'])) {
        $map = ['mini' => 'Alle 0,2l', 'small'=>'0,2l - 0,5l', 'medium'=>'0,5l - 1l', 'large'=>'Yli 1l'];
        $desc[] = "Pakkauskoko: ".$map[$filters['bottleSize']];
    }
    if (!empty($filters['priceLow'])) $desc[] = "Hinta alkaen: {$filters['priceLow']}";
    if (!empty($filters['priceHigh'])) $desc[] = "Hinta asti: {$filters['priceHigh']}";
    if (!empty($filters['energyLow'])) $desc[] = "Energia alkaen: {$filters['energyLow']}";
    if (!empty($filters['energyHigh'])) $desc[] = "Energia asti: {$filters['energyHigh']}";
    return implode(" <br>", $desc);
}

function parseSize($str) {
    $num = str_replace(",", ".", str_ireplace(" l", "", trim($str)));
    return floatval($num);
}

function detectSizeGroupValue($rawSize) {
    $liters = parseSize($rawSize);

    if ($liters <= 0.2) {
        return "mini";
    } elseif ($liters <= 0.5) {
        return "small";
    } elseif ($liters <= 1.0) {
        return "medium";
    } else {
        return "large";
    }
}

?>