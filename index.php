<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Alkon hinnasto</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="css/styles.css">
    </head>
    <body>
        <?php

        // Update and convert new data if file does not exist or is older than 24h
        require("helpers/excelToCsv.php");
        $excel_uri ="https://www.alko.fi/INTERSHOP/static/WFS/Alko-OnlineShop-Site/-/Alko-OnlineShop/fi_FI/Alkon%20Hinnasto%20Tekstitiedostona/alkon-hinnasto-tekstitiedostona.xlsx";
        $csvFile = __DIR__ . "/data/alko.csv";
        if (!file_exists($csvFile) || (time() - filemtime($csvFile) > 24 * 3600)) {
            excelToCsv($excel_uri, $csvFile);
            header("Location: ".$_SERVER['REQUEST_URI']);
            exit;
        }

        require("model.php");
        require_once("controller.php");
        require_once("components/filterBar.php");
        require_once("components/pagination.php");

        $alkoData = initModel();
        $filters = handleRequest();
        $rowsFound = count($alkoData);
        $filterBar = filterBar($alkoData, $filters);
        $alkoProductTable = generateView($alkoData, $filters, 'products');

        echo
        "<div class='d-flex justify-content-center'>
        <div class='container-fluid px-3'>
            <div class='pricing-header px-3 py-3 pb-md-4 mx-auto text-center'>
                <h1 class='display-2'>Alkon hinnasto</h1>
                <p class='lead'>Hinnasto päivitetty: $priceListDate <br> Tuotteita yhteensä: $rowsFound</p>
            </div>";

        echo $filterBar;
        echo $alkoProductTable;
        echo "</div>";
        echo "</div>";
        ?>
    </body>
</html>
