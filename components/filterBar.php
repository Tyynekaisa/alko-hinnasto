<?php

require_once('helpers/filterHelpers.php');
require_once('components/pagination.php');

function filterBar($alkoData, $filters) {
    $itemsPerPage = 25;
    $currpage = isset($_GET['page']) ? (int)$_GET['page'] : 0;
    if ($currpage < 0) $currpage = 0;

    // Get current filter values
    $country = $_GET['country'] ?? "";
    $priceLow = $_GET['priceLow'] ?? "";
    $priceHigh = $_GET['priceHigh'] ?? "";
    $type = $_GET['type'] ?? "";
    $bottleSize = $_GET['bottleSize'] ?? "";
    $energyLow = $_GET['energyLow'] ?? "";
    $energyHigh = $_GET['energyHigh'] ?? "";

    // Dropdown values
    $countries = array_unique(array_column($alkoData, 12));
    $countries = array_filter($countries, function($c) {
        return trim($c) !== "" && trim($c) !== "Alkuperämaa vaihtelee";
    });
    sort($countries);

    $originalTypes = array_unique(array_column($alkoData, 8));
    $types = [];
    foreach ($originalTypes as $t) {
        $types[$t] = strtolower($t);
    }
    $types = array_filter($types, function($t) {
        return trim($t) !== "";
    });
    asort($types);

    $bottleSizes = ['mini' => 'Mini (<= 0,2 l)', 'small'=>'Pieni (0,2l - 0,5l)', 'medium'=>'Medium (0,5 l -1 l)', 'large'=>'Suuri (yli 1 l)'];

    $html = "<hr>
        <form method='get' action='./index.php'>
            <input type='hidden' name='page' value='0'>
            <h3>Suodata hakutuloksia</h3>
            <div class='form-row mb-3'>
                <div class='col-md-4'>
                    <label for='countryFilter'>Valmistusmaa</label>
                    <select class='form-control' id='countryFilter' name='country'>
                        <option value=''>Kaikki maat</option>";
                        foreach ($countries as $c) {
                            $sel = ($c === ($_GET['country'] ?? "")) ? " selected" : "";
                            $html .= "<option value='".htmlspecialchars($c, ENT_QUOTES)."'$sel>"
                            .htmlspecialchars($c)
                            ."</option>";
                        }
                    $html .= "</select>
                </div>

                <div class='col-md-4'>
                    <label for='type'>Juoman tyyppi</label>
                    <select class='form-control' id='type' name='type'>
                        <option value=''>Kaikki juomat</option>";
                        foreach ($types as $original => $lowercase) {
                            $sel = ($original === $type) ? " selected" : "";
                            $html .= "<option value='".htmlspecialchars($original, ENT_QUOTES)."'$sel>"
                                .htmlspecialchars($lowercase)
                                ."</option>";
                        }
                    $html .= "</select>
                </div>

                <div class='col-md-4'>
                    <label for='bottleSize'>Pakkauskoko</label>
                    <select class='form-control' id='bottleSize' name='bottleSize'>
                        <option value=''>Kaikki koot</option>";
                        foreach ($bottleSizes as $key=>$label) {
                            $sel = ($key === $bottleSize) ? " selected" : "";
                            $html .= "<option value='$key'$sel>$label</option>";
                        }
                    $html .= "</select>
                </div>
            </div>

            <div class='form-row form-group'>
                <div class='col-md-3'>
                    <label for='priceLow'>Hinta</label>
                    <input type='text' class='form-control' id='priceLow' name='priceLow' placeholder='Min. hinta' value='".htmlspecialchars($priceLow)."'>
                </div>
                <div class='col-md-3'>
                    <label class='invisible' for='priceHigh'>.</label>
                    <input type='text' class='form-control' id='priceHigh' name='priceHigh' placeholder='Max. hinta' value='".htmlspecialchars($priceHigh)."'>
                </div>

                <div class='col-md-3'>
                    <label for='energyLow'>Energia</label>
                    <input type='text' class='form-control' id='energyLow' name='energyLow' placeholder='Min. energia'value='".htmlspecialchars($energyLow)."'>
                </div>
                <div class='col-md-3'>
                    <label class='invisible' for='energyHigh'>.</label>
                    <input type='text' class='form-control' id='energyHigh' name='energyHigh' placeholder='Max. energia' value='".htmlspecialchars($energyHigh)."'>
                </div>
            </div>

            <button type='submit'>Suodata</button>

            
        </form>
        <hr>";

    $desc = describeFilters([
        'country'=>$country,
        'type'=>$type,
        'bottleSize'=>$bottleSize,
        'priceLow'=>$priceLow,
        'priceHigh'=>$priceHigh,
        'energyLow'=>$energyLow,
        'energyHigh'=>$energyHigh
    ]);
    if ($desc!=="") {
        $html .=
            "<h3 class='font-weight-normal'>Suodattimet</h3>
            $desc";

        $countFiltered = 0;
        foreach ($alkoData as $product) {
            if ($country && $product[12]!==$country) continue;
            if ($type && $product[8]!==$type) continue;
            if ($bottleSize && detectSizeGroupValue($product[3])!==$bottleSize) continue;
            if ($priceLow && $product[4]<$priceLow) continue;
            if ($priceHigh && $product[4]>$priceHigh) continue;
            if ($energyLow && $product[22]<$energyLow) continue;
            if ($energyHigh && $product[22]>$energyHigh) continue;
            $countFiltered++;
        }
        
        $html .= "<p><br>Tulokset: $countFiltered</p>";
        $html .= "<button type='button' onClick=\"location.href='./index.php'\">Tyhjennä suodattimet</button>";
    }

    // Pagination

    $filteredRows = [];

    foreach ($alkoData as $product) {
        if ($country && $product[12] !== $country) continue;
        if ($type && $product[8] !== $type) continue;
        if ($bottleSize && detectSizeGroupValue($product[3]) !== $bottleSize) continue;
        if ($priceLow && $product[4] < $priceLow) continue;
        if ($priceHigh && $product[4] > $priceHigh) continue;
        if ($energyLow && $product[22] < $energyLow) continue;
        if ($energyHigh && $product[22] > $energyHigh) continue;

        $filteredRows[] = $product;
    }

    $totalFiltered = count($filteredRows);
    $totalPages = ceil($totalFiltered / $itemsPerPage);

    $offset = $currpage * $itemsPerPage;
    $currentPageRows = array_slice($filteredRows, $offset, $itemsPerPage);


    $html .= renderPagination($currpage, $totalPages);

    return $html;

}