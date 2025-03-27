<?php
function generateSlides($dir) { //banner na index.php
    $files = glob($dir . "/*.jpg");
    $json = file_get_contents("data/datas.json");
    $data = json_decode($json, true);
    $text = $data["text_banner"];
    foreach ($files as $file) {
        echo '<div class="slide fade">';
        echo '<img src="' . $file . '">';
        echo '<div class="slide-text">';
        echo ($text[basename($file)]);
        echo '</div>';
        echo '</div>';
    }
}

function preparePortfolio(int $numberOfRows = 2, int $numberOfCols = 4): array{
    $portfolio = [];
    $colIndex = 1;
    for ($i = 1; $i <= $numberOfRows; $i++) {
        for($j = 1; $j <= $numberOfCols; $j++) {
            $portfolio[$i][$j] = $colIndex;
            $colIndex++;
        }
    }
    return $portfolio;
}
function finishPortfolio(){
    $json = file_get_contents("data/datas.json");
    $data = json_decode($json, true);
    $portfolio = preparePortfolio();
    foreach ($portfolio as $row => $col) {
        echo '<div class="row">';
        foreach ($col as $index) {
            $portfolioKey = 'portfolio' . $index;
            $portfolioName = $data['portfolio_obrazky'][$portfolioKey]['Názov'];
            $portfolioURL = $data['portfolio_obrazky'][$portfolioKey]['url'];
            echo '<a href="' . $portfolioURL . '" target="_blank">
                    <div class="col-25 portfolio text-white text-center" id="portfolio-' . $index . '">
                        ' . $portfolioName . '
                    </div>
                  </a>';
        }
        echo '</div>';
    }
}

function getMenuData(string $type): array{
    $menu = [];
if($type === "header") {
    $menu = [
        'home' => [
            'name' => 'Domov',
            'path' => 'index.php',
        ],
        'portfolio' => [
            'name' => 'Portfólio',
            'path' => 'portfolio.php',
        ],
        'qna' => [
            'name' => 'Q&A',
            'path' => 'qna.php',
        ],
        'kontakt' => [
            'name' => 'Kontakt',
            'path' => 'kontakt.php',
        ]
    ];
}
return $menu;
}
function printMenu(array $menu){
foreach ($menu as $menuName => $menuData) {
    echo '<li><a href="'.$menuData['path'].'">'.$menuData['name'].'</a></li>';
}
}
$theme = $_GET["theme"]

?>


