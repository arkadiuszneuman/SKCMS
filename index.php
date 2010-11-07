<script type="text/javascript" src="./javascript/ajax.js"></script>

<?php
    include('structure/up.html');

    session_start();
    if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] == false)
        echo '<a href="./user.php?task=login">Panel administracyjny</a>';
    else
    {
        echo '<a href="./panel.php">Panel administracyjny</a> &nbsp; &nbsp;';
        echo '<a href="./user.php?task=logoff">Wyloguj</a>';
    }

    echo "<div id=\"newses\">";
    echo "tutaj beda newsy<br /><br />";
    echo "</div>";
    echo "<a href=\"#\" onclick=\"sendGet('ajaxnewses.php?page=', 'news')\">Następna strona</a>";
    
    include('structure/down.html');
?>