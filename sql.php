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

            for ($i = 0; $line = mysql_fetch_row($reply); ++$i)
            {
                $array[$i]['title'] = $line[0]; //czy tablica asocjacyjna nie jest zbyt wolna? czy w przypadku sieciowego programowania ma to jakies duze znaczenie?
                $array[$i]['date'] = $line[1];
                $array[$i]['note'] = $line[2];
            }

            return $array;
        }

        function Close()
        {
            mysql_close($this->sql_conn);
        }
    }
?>
