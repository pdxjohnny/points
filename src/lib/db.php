<?php
class Database
{
    public $db;

    public function __construct()
    {
        $this->db = new PDO("mysql:host=" . $_ENV['DB_PORT_3306_TCP_ADDR'] . ";dbname=" . $_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
        $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        $this->db->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES UTF8');
        $this->create_users();
    }

    public function table_exists( $table_name )
    {
        $exists = false;
        $statement = $this->db->prepare("SELECT name FROM SQLITE_MASTER WHERE name=':table_name'");
        $statement->bindValue( ':table_name', $table_name, SQLITE3_TEXT );

        $result = $statement->execute();

        if ( $row = $result->fetchArray(SQLITE3_ASSOC) )
        {
            $exists = true;
        }
        return $exists;
    }

    private function create_users()
    {
        if ( !$this->table_exists('USERS') )
        {
            $this->db->exec("CREATE TABLE IF NOT EXISTS USERS ( username TEXT, password TEXT, school TEXT PRIMARY KEY )");
        }
    }

    public function check_user( $user )
    {
            $username = false;
            $school = false;
            if ( isset($user['username']) )
            {
                    $statement = $this->db->prepare('SELECT * FROM USERS WHERE username=:username');
                    $statement->bindValue( ':username', $user['username'], SQLITE3_TEXT );
                    $result = $statement->execute();
                    if ( $result = $result->fetchArray(SQLITE3_ASSOC) )
                    {
                            $username = count( (array)$result );
                    }
            }
            if ( isset($user['school']) )
            {
                    $statement = $this->db->prepare('SELECT * FROM SQLITE_MASTER WHERE name=:school');
                    $statement->bindValue( ':school', $user['school'], SQLITE3_TEXT );
                    $result = $statement->execute();
                    if ( $result = $result->fetchArray(SQLITE3_ASSOC) )
                    {
                            $school = count( (array)$result );
                    }
            }
            if ( $school || $username )
            {
                    return true;
            }
            return false;
    }

    public function user( $user )
    {
        if ( !isset($user['username']) || !isset($user['password']) )
        {
            return false;
        }
        $result = false;
        $statement = $this->db->prepare('SELECT * FROM USERS WHERE username=:username AND password=:password');
        $statement->bindValue( ':username', $user['username'], SQLITE3_TEXT );
        $statement->bindValue( ':password', $user['password'], SQLITE3_TEXT );

        $result = $statement->execute();
        if ( $result = $result->fetchArray(SQLITE3_ASSOC) )
        {
            $array = (array)$result;
            if ( 0 == count( $array ) )
            {
                $result = false;
            }
        }
        return $result;
    }

    public function table( $table )
    {
        $statement = $this->db->prepare('SELECT * FROM `' . $table . '`');
        $result = $statement->execute();
        $headers = false;
        echo "<h2 id='school_name' >" . $table . "</h2>";
        $this->table_options();
        echo "<table id='table' >";
        $all = array();
        while ( $row = $result->fetchArray(SQLITE3_ASSOC) )
        {
            array_push($all, $row);
            if ( !$headers )
            {
                echo "<thead><tr>";
                foreach ( $row as $key => $value )
                {
                    echo "<th>" . $key . "</th>";
                }
                echo "</tr></thead><tbody>";
                $headers = true;
            }
            echo "<tr>";
            foreach ( $row as $key => $value )
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
        echo "<button onclick=\"download( $('#school_name').html() + '.csv', $('#table').table2CSV({delivery:'value'}));\" >Download</button>";
        echo "<button id=\"clear\" >Delete Data</button>";
        // echo "<select id='column' ><option value='0' > Column</option></select>";
        // echo "<select id='filter' ><option value='0' > Filter on</option></select>";
        // echo "<input id='sort' placeholder='Sort on' ></input>";
    }

    public function last_ticket( $table )
    {
        $statement = $this->db->prepare('SELECT max( ticket ) FROM "' . $table . '"' );
        $result = $statement->execute();
        if ( $row = $result->fetchArray(SQLITE3_ASSOC) )
        {
            $ticket = $row['max( ticket )'];
        }
        $statement = $this->db->prepare('SELECT max( guest_ticket ) FROM "' . $table . '"' );
        $result = $statement->execute();
        if ( $row = $result->fetchArray(SQLITE3_ASSOC) )
        {
            $guest_ticket = $row['max( guest_ticket )'];
        }
        return ($ticket > $guest_ticket ? $ticket : $guest_ticket);
    }

    public function clear( $table )
        {
                $statement = $this->db->prepare('DELETE FROM "' . $table . '"' );
                $result = $statement->execute();
                return $result;
        }

}

$database = new Database;
?>
