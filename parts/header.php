<?php
include("parts/functions.php");
$menu = getMenuData("header");
$theme = $_GET["theme"];
?>
<header style="background-color: <?php echo $theme === "dark" ? "grey" : "white"; ?>" class="container main-header">

    <div>
        <a href="index.html">
            <img src="img/logo.png" height="40">
        </a>
    </div>
    <nav class="main-nav">
        <ul class="main-menu" id="main-menu">
            <a href=<?php echo $theme === "dark" ? "?theme=light" : "?theme=dark"; ?> >Zmena témy</a>
            <li><a href="index.php">Domov</a></li>
            <li><a href="portfolio.php">Portfólio</a></li>
            <li><a href="qna.php">Q&A</a></li>
            <li><a href="kontakt.php">Kontakt</a></li>
        </ul>
        <a class="hamburger" id="hamburger">
            <i class="fa fa-bars"></i>
        </a>
    </nav>
</header>