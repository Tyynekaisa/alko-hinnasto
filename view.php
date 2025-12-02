<?php

function createColumnHeaders($columns2Include) {
    $t = "<thead>"; 
    $t .= "<tr>";
    for($i = 0; $i < count($columns2Include); $i++ ) {
        $val = $columns2Include[$i];
        $t .= '<th scope="col">'.$val."</th>";
    }
    $t .= "</tr></thead>";    
    return $t;
}

function createTableRow($product,$columns2Include,$columnNamesMap) {
    $t = "<tr>";
    for($i = 0; $i < count($columns2Include); $i++ ) {
        $columnName = $columns2Include[$i];
        $item = $product[ $columnNamesMap[$columnName]];
        if($i == 0) {
            $t .= '<th scope="row">'.$item."</td>";
        } else {
            $t .= "<td>".$item."</td>";
        }
    }
    $t .= "</tr>";    
    return $t;    
}

/**
 * Creates a html-table from alko products
 * @param type $products array of products
 * @param type $columns2Include names of columns to include
 * @param type $columnNamesMap column names to index mas
 * @param $filters['LIMIT'] max nbr of items to include
 *        $filters['PAGE'] page to start from
 *        $filters['TYPE'] product type
 *        $filters['COUNTRY'] product country
 *        $filters['PRICELOW'] price low limit
 *        $filters['PRICEHIGH'] price high limit
 *        $filters['BOTTLESIZE'] bottle size
 *        $filters['ENERGYLOW'] energy low limit
 *        $filters['ENERGYHIGH'] energy high limit
 * @return string html table 
 */

function createAlkoProductsTable($products, $columns2Include, $columnNamesMap, $filters, $tblId) {
    $limitCounter = 0;
    $limitCounterLow = $filters['LIMIT']*$filters['PAGE'];
    $limitCounterHigh = $limitCounterLow + $filters['LIMIT'];

    
    if($tblId != null) {
        $t = "<table id='$tblId' class='table mb-5'>";    
    } else {
        $t = "<table class='table my-5'>";    
    }
    $t .= createColumnHeaders($columns2Include); 
    $t .= '<tbody>';
    for($i = 0; $i < count($products); $i++) {
        $product = $products[$i];
        
        if($filters['TYPE'] != null){
            if($product[$columnNamesMap['Tyyppi']] !== $filters['TYPE']) {
                continue;
            }
        }
        if($filters['COUNTRY'] != null){
            if($product[$columnNamesMap['Valmistusmaa']] !== $filters['COUNTRY']) {
                continue;
            }
        }
        
        if($filters['PRICELOW'] != null){
            if($product[$columnNamesMap['Hinta']] < $filters['PRICELOW']) {
                continue;
            }
        }
        if($filters['PRICEHIGH'] != null){
            if($product[$columnNamesMap['Hinta']] > $filters['PRICEHIGH']) {
                continue;
            }
        }

        if ($filters['BOTTLESIZE'] != null) {
            $group = detectSizeGroupValue($product[$columnNamesMap['Pullokoko']]);

            if ($group !== $filters['BOTTLESIZE']) {
                continue;
            }
        }

        if($filters['ENERGYLOW'] != null){
            if($product[$columnNamesMap['Energia kcal/100 ml']] < $filters['ENERGYLOW']) {
                continue;
            }
        }
        if($filters['ENERGYHIGH'] != null){
            if($product[$columnNamesMap['Energia kcal/100 ml']] > $filters['ENERGYHIGH']) {
                continue;
            }
        }

        $limitCounter++;
        if($limitCounter > $limitCounterLow) {
            $t .= createTableRow($product,$columns2Include,$columnNamesMap);
            if($limitCounter >= $limitCounterHigh) {
                break;
            }
        }
    }
    $t .= '</tbody>';
    $t .= "</table>";
    return $t;
}

function generateView($alkoData, $filters, $tblId=null) {
    global $columns2Include, $columnNamesMap;

    $alkoProductTable = createAlkoProductsTable($alkoData, $columns2Include, $columnNamesMap, $filters, $tblId);
    return $alkoProductTable;
}