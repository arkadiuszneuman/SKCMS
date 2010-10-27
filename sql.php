<?php
    class sql
    {
        function Sql($server, $user, $pass, $database) //polaczenie z wybraniem bazy
        {
            $this->sql_conn = mysql_connect($server, $user, $pass);
            mysql_select_db($database);
        }

        function SelectDatabase($database) //zmiana bazy w tym miejscu
        {
            mysql_select_db($database);
        }
        
        function AddNews($title, $note)
        {
            $query = "INSERT INTO `news` (`id`, `title`, `date`, `note`) VALUES
            (NULL, '".$title."', ' ".date("Y-m-d H:i:s")."', '".$note."');";

            if (mysql_query($query))
                echo "wysłane";
            else
                echo "nie wysłane";
        }

        function ReadNews()
        {
            $query = "SELECT title, date, note FROM news ORDER BY date DESC";
            $reply = mysql_query($query);

            while ($line = mysql_fetch_row($reply)) //do czegos by bylo trzeba to ciulstwo zapisac i zwrocic w funkcji te dane, tylko chuj wie do czego. tworzyc nowa klase news?
            {
                echo "<h3>$line[0]</h3><h6><br />"; //TODO tylko zapis do czegos, bez wyswietlenia
                echo "$line[1]</h6><br />";
                echo "<hr />";
                echo "<p>".nl2br($line[2])."</p><br>";
            }
        }

        function Close()
        {
            mysql_close($this->sql_conn);
        }
    }
?>
