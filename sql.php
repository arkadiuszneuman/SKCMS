<?php
    class Sql
    {
        private $sql_conn = null;

        public function Sql($server, $user, $pass, $database) //polaczenie z wybraniem bazy
        {
            $this->sql_conn = mysql_connect($server, $user, $pass);
            mysql_select_db($database);
        }

        public function SelectDatabase($database) //zmiana bazy danych
        {
            mysql_select_db($database);
        }
        
        public function AddNews($title, $note) //dodanie newsa
        {
            $query = "INSERT INTO `news` (`id`, `title`, `date`, `note`) VALUES
            (NULL, '".trim($title)."', ' ".date("Y-m-d H:i:s")."', '".trim($note)."');";

            return mysql_query($query);
        }

        public function NumberOfNewses()
        {
            $row = mysql_fetch_array(mysql_query("SELECT COUNT(*) as howmany FROM news"));
            return $row["howmany"];
        }

        public function ReadNews($isId, $from, $howMany) //odczytanie newsow, isId - czy zwracac tez id; from - od ktorej danej zwracac, to - ile notek
        {
            $query = "SELECT title, date, note";

            if ($isId)
                $query = $query.", id";

            $query = $query." FROM news ORDER BY date DESC LIMIT ".$from.", ".$howMany;
            
            $reply = mysql_query($query);
            for ($i = 0; $line = mysql_fetch_row($reply); ++$i)
            {
                $array[$i]['title'] = $line[0];
                $array[$i]['date'] = $line[1];
                $array[$i]['note'] = $line[2];
                if ($isId)
                    $array[$i]['id'] = $line[3];
            }

            return $array;
        }

        public function ReadSelectedNews($id) //odczytanie pojednynczego newsa
        {
            $query = "SELECT title, note FROM news WHERE id='".$id."'";

            $reply = mysql_query($query);
            $line = mysql_fetch_row($reply);
            $array['title'] = $line[0];
            $array['note'] = $line[1];

            return $array;
        }

        public function EditNews($id, $title, $note) //zmiana konkretnego newsa
        {
            $query = "UPDATE news SET title='".$title."', note='".$note."' WHERE id='".$id."'";

            return mysql_query($query);
        }

        public function RemoveNews($id) //usuwanie newsa
        {
            $query = "DELETE FROM news WHERE id='".$id."'";
            
            return mysql_query($query);
        }

        public function AddAdmin($login, $pass, $name, $mail) //dodawanie admina
        {
            $query = "INSERT INTO `admins` (`id`, `login`, `pass`, `name`, `mail`) VALUES
            (NULL, '".trim($login)."', ' ".trim($pass)."', '".trim($name)."', '".trim($mail)."');";

            return mysql_query($query);
        }

        public function CheckAdmin($login, $pass) //sprawdzanie loginu i hasla
        {
            $query = "SELECT login, pass FROM admins";
            $reply = mysql_query($query);

            for ($i = 0; $line = mysql_fetch_row($reply); ++$i)
            {
                if (trim($line[0]) === trim($login) && trim($line[1]) === trim($pass))
                    return true;
            }

            return false;
        }

        public function Close()
        {
            mysql_close($this->sql_conn);
        }
    }
?>
