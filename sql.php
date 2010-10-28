<?php
    class Sql
    {
        function Sql($server, $user, $pass, $database) //polaczenie z wybraniem bazy
        {
            $this->sql_conn = mysql_connect($server, $user, $pass);
            mysql_select_db($database);
        }

        function SelectDatabase($database) //zmiana bazy danych
        {
            mysql_select_db($database);
        }
        
        function AddNews($title, $note) //dodanie newsa
        {
            $query = "INSERT INTO `news` (`id`, `title`, `date`, `note`) VALUES
            (NULL, '".trim($title)."', ' ".date("Y-m-d H:i:s")."', '".trim($note)."');";

            if (mysql_query($query))
                echo "wysłane";
            else
                echo "nie wysłane";
        }

        function ReadNews() //odczytanie newsow
        { //TODO z podanego zakresu
            $query = "SELECT title, date, note FROM news ORDER BY date DESC";
            $reply = mysql_query($query);

            for ($i = 0; $line = mysql_fetch_row($reply); ++$i)
            {
                $array[$i]['title'] = $line[0];
                $array[$i]['date'] = $line[1];
                $array[$i]['note'] = $line[2];
            }

            return $array;
        }

        function AddAdmin($login, $pass, $name, $mail)
        {
            $query = "INSERT INTO `admins` (`id`, `login`, `pass`, `name`, `mail`) VALUES
            (NULL, '".trim($login)."', ' ".trim($pass)."', '".trim($name)."', '".trim($mail)."');";

            return mysql_query($query);
        }

        function CheckAdmin($login, $pass)
        {
            $query = "SELECT login, pass FROM admins";
            $reply = mysql_query($query);

            for ($i = 0; $line = mysql_fetch_row($reply); ++$i)
            {
                if (trim($line[0]) === $login && trim($line[1]) === trim($pass))
                    return true;
            }

            return false;
        }

        function Close()
        {
            mysql_close($this->sql_conn);
        }
    }
?>
