<?php

function buildQuery($overrides = []) {
    // Aloita nykyisistä GET-parametreista
    $params = $_GET;
    // Korvaa mahdolliset arvot
    foreach ($overrides as $k => $v) {
        $params[$k] = $v;
    }
    // Poista tyhjät arvot
    $params = array_filter($params, fn($v) => $v !== "");
    return http_build_query($params);
}

function renderPagination($currpage, $totalPages)
{
    $html = "<ul class='pagination justify-content-center'>";

    // Previous
    $prevDisabled = ($currpage <= 0) ? " disabled" : "";
    $prevLink = "./index.php?" . buildQuery(['page' => $currpage - 1]);
    $html .= "<li class='page-item$prevDisabled'>
            <a class='page-link' href='$prevLink'>&laquo;</a>
        </li>";

    // First two pages
    for ($p = 0; $p <= 1 && $p < $totalPages; $p++) {
        $html .= renderPageNumber($p, $currpage);
    }

    if ($currpage > 3) {
        $html .= "<li class='page-item disabled'><span class='page-link'>…</span></li>";
    }

   // Current page zone
    for ($p = $currpage - 1; $p <= $currpage + 1; $p++) {
        if ($p > 1 && $p < $totalPages - 2) {
            $html .= renderPageNumber($p, $currpage);
        }
    }

    if ($currpage < $totalPages - 4) {
        $html .= "<li class='page-item disabled'><span class='page-link'>…</span></li>";
    }

    // Last two pages
    for ($p = $totalPages - 2; $p < $totalPages; $p++) {
        if ($p >= 2) { 
            $html .= renderPageNumber($p, $currpage); 
        } 
    }

    // Next
    $nextDisabled = ($currpage >= $totalPages - 1) ? " disabled" : "";
    $nextLink = "./index.php?" . buildQuery(['page' => $currpage + 1]);
    $html .= "<li class='page-item$nextDisabled'>
            <a class='page-link' href='$nextLink'>&raquo;</a>
        </li>";

    $html .= "</ul>";
    return $html;
}

function renderPageNumber($p, $currpage)
{
    if ($p < 0) return;
    $active = ($p == $currpage) ? " active" : "";
    $pageLink = "./index.php?" . buildQuery(['page' => $p]);
    $html = "<li class='page-item$active'>
            <a class='page-link' href='$pageLink'>" . ($p + 1) . "</a>
        </li>";
    return $html;
}

?>