<?php
class Database
{
    private $db;

    public function __construct()
    {
        $this->db = null;
        try {
            $conn = 'mysql:host=' . $_ENV['DB_PORT_3306_TCP_ADDR'] .
                    ';dbname=' . $_ENV['DB_ENV_MYSQL_DATABASE'] .
                    ';charset=utf8';
            $this->db = new PDO($conn,
                $_ENV['DB_ENV_MYSQL_USER'],
                $_ENV['DB_ENV_MYSQL_PASSWORD']
           );
        } catch (Exception $err) {
            error_log("ERROR: " . $err->getMessage());
            return;
        }
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $this->db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES UTF8');
        $this->create_users();
    }

    public function table_exists($table_name)
    {
        $exists = false;
        $statement = $this->db->prepare("SELECT table_name FROM USERS WHERE table_name=':table_name'");
        $statement->bindValue(':table_name', $table_name, PDO::FETCH_ASSOC);

        try {
            $statement->execute();

            if ($row = $statement->fetchAll(PDO::FETCH_ASSOC))
            {
                $exists = true;
            }
        } catch (Exception $err) {
            error_log("ERROR: " . $err->getMessage());
        }
        return $exists;
    }

    private function create_users()
    {
        if (!$this->table_exists('USERS'))
        {
            $this->db->exec("CREATE TABLE IF NOT EXISTS USERS (username VARCHAR(100), password VARCHAR(100), table_name VARCHAR(100) PRIMARY KEY)");
        }
    }

    public function check_user($user)
    {
        $username = false;
        $table_name = false;
        if (isset($user['username']))
        {
            $statement = $this->db->prepare('SELECT * FROM USERS WHERE username=:username');
            $statement->bindValue(':username', $user['username'], PDO::FETCH_ASSOC);
            $statement->execute();
            if ($result = $statement->fetchAll(PDO::FETCH_ASSOC))
            {
                    $username = count((array)$result);
            }
        }
        if (isset($user['table_name']))
        {
            $table_name = $this->check_table($user['table_name']);
        }
        if ($table_name || $username)
        {
            return true;
        }
        return false;
    }

    public function user($user)
    {
        if (!isset($user['username']) || !isset($user['password']))
        {
            return false;
        }
        $result = false;
        $statement = $this->db->prepare('SELECT * FROM USERS WHERE username=:username AND password=:password');
        $statement->bindValue(':username', $user['username'], PDO::FETCH_ASSOC);
        $statement->bindValue(':password', $user['password'], PDO::FETCH_ASSOC);

        $statement->execute();
        if ($result = $statement->fetchAll(PDO::FETCH_ASSOC))
        {
            $array = (array)$result;
            if (0 == count($array))
            {
                $result = false;
            }
        }
        return $result;
    }

    public function table($table)
    {
        $statement = $this->db->prepare('SELECT * FROM `' . $table . '`');
        $statement->execute();
        $headers = false;
        echo "<h2 id='table_name_name' >" . $table . "</h2>";
        $this->table_options();
        echo "<table id='table' >";
        $all = array();
        while ($row = $statement->fetchAll(PDO::FETCH_ASSOC))
        {
            array_push($all, $row);
            if (!$headers)
            {
                echo "<thead><tr>";
                foreach ($row as $key => $value)
                {
                    echo "<th>" . $key . "</th>";
                }
                echo "</tr></thead><tbody>";
                $headers = true;
            }
            echo "<tr>";
            foreach ($row as $key => $value)
            {
                echo "<td>" . $value . "</td>";
            }
            echo "</tr>";
        }
        echo "</tbody></table>";
        echo "<script>var table_object = " . json_encode($all) . "</script>";
    }

    public function table_options()
    {
        echo "<button onclick=\"download($('#table_name_name').html() + '.csv', $('#table').table2CSV({delivery:'value'}));\" >Download</button>";
        echo "<button id=\"clear\" >Delete Data</button>";
        // echo "<select id='column' ><option value='0' > Column</option></select>";
        // echo "<select id='filter' ><option value='0' > Filter on</option></select>";
        // echo "<input id='sort' placeholder='Sort on' ></input>";
    }

    public function last_ticket($table)
    {
        $statement = $this->db->prepare('SELECT max(ticket) FROM "' . $table . '"');
        $statement->execute();
        if ($row = $statement->fetchAll(PDO::FETCH_ASSOC))
        {
            $ticket = $row['max(ticket)'];
        }
        $statement = $this->db->prepare('SELECT max(guest_ticket) FROM "' . $table . '"');
        $statement->execute();
        if ($row = $statement->fetchAll(PDO::FETCH_ASSOC))
        {
            $guest_ticket = $row['max(guest_ticket)'];
        }
        return ($ticket > $guest_ticket ? $ticket : $guest_ticket);
    }

    public function clear($table)
    {
        $statement = $this->db->prepare('DELETE FROM "' . $table . '"');
        $statement->execute();
        return $result;
    }

}

$database = new Database;
?>
