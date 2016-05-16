<?php
class Database
{
    private $token;
    private $db;

    public function __construct()
    {
        $this->auth = new Auth;
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
        $this->create_tables();
    }

    public function table_exists($table_name)
    {
        $exists = false;
        $statement = $this->db->prepare("SHOW TABLES LIKE '" . $table_name . "'");
        $statement->bindValue(':table_name', $table_name, PDO::PARAM_STR);

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

    private function create_tables() {
        if (!$this->table_exists('USERS')) {
            $this->db->exec("CREATE TABLE IF NOT EXISTS USERS (id INT NOT NULL AUTO_INCREMENT, username VARCHAR(100) NOT NULL, password VARCHAR(100) NOT NULL, points INT DEFAULT 100, PRIMARY KEY (id))");
        }
        if (!$this->table_exists('BOUNTY')) {
            $this->db->exec("CREATE TABLE IF NOT EXISTS BOUNTY (id INT NOT NULL AUTO_INCREMENT, creator INT NOT NULL, awarded INT DEFAULT 0, title VARCHAR(100) NOT NULL, description VARCHAR(500), points INT DEFAULT 0, PRIMARY KEY (id))");
        }
    }

    public function create_bounty($bounty) {
        if ($bounty == NULL) {
            return new Bounty;
        }
        // Subtract from the creators points
        $statement = $this->db->prepare("UPDATE USERS SET points=points - :points WHERE id=:creator AND points >= :points");
        $statement->bindValue(':points', $bounty->points, PDO::PARAM_INT);
        $statement->bindValue(':creator', $bounty->creator, PDO::PARAM_INT);
        $statement->execute();
        // Creator did not have enough points
        if ($statement->rowCount() < 1) {
            return $bounty;
        }
        // Create the bounty
        $statement = $this->db->prepare("INSERT INTO BOUNTY(creator,title,description,points) VALUES(:creator,:title,:description,:points)");
        $statement->bindValue(':creator', $bounty->creator, PDO::PARAM_INT);
        $statement->bindValue(':title', $bounty->title, PDO::PARAM_STR);
        $statement->bindValue(':description', $bounty->description, PDO::PARAM_STR);
        $statement->bindValue(':points', $bounty->points, PDO::PARAM_INT);
        $statement->execute();
        $bounty->id = intval($this->db->lastInsertId());
        return $bounty;
    }

    public function award_bounty($bounty) {
        if ($bounty == NULL) {
            return new Bounty;
        }
        $statement = $this->db->prepare("UPDATE BOUNTY SET awarded=:awarded WHERE id=:id AND creator=:creator");
        $statement->bindValue(':awarded', $bounty->awarded, PDO::PARAM_INT);
        $statement->bindValue(':id', $bounty->id, PDO::PARAM_INT);
        $statement->bindValue(':creator', $bounty->creator, PDO::PARAM_INT);
        $statement->execute();
        // Add to the awrareds points
        $statement = $this->db->prepare("UPDATE USERS SET points=points + :points WHERE id=:awarded");
        $statement->bindValue(':points', $bounty->points, PDO::PARAM_INT);
        $statement->bindValue(':awarded', $bounty->awarded, PDO::PARAM_INT);
        $statement->execute();
        return $bounty;
    }

    public function top_100_bountys($onbounty) {
        $statement = $this->db->prepare('SELECT * FROM BOUNTY WHERE awarded IS NULL ORDER BY points DESC LIMIT 100');
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $onbounty(new Bounty($row));
        }
        return true;
    }

    public function top_100_users($onuser) {
        $statement = $this->db->prepare('SELECT id,username,points FROM USERS ORDER BY points DESC LIMIT 100');
        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $onuser(new User($row));
        }
        return true;
    }

    public function username($id) {
        $id = array(
            'id'    =>  $id,
        );
        return $this->check_user($id)->username;
    }

    public function check_bounty($bounty)
    {
        if (isset($bounty['id'])) {
            $statement = $this->db->prepare('SELECT * FROM BOUNTY WHERE id=:id');
            $statement->bindValue(':id', $bounty['id'], PDO::PARAM_INT);
        } else {
            return false;
        }
        $statement->execute();
        if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            return new Bounty($row);
        }
        return false;
    }

    public function check_user($user)
    {
        $username = false;
        // Check the user
        if (isset($user['id']))
        {
            $statement = $this->db->prepare('SELECT id,username,points FROM USERS WHERE id=:id');
            $statement->bindValue(':id', $user['id'], PDO::PARAM_INT);
        } else if (isset($user['username'])) {
            $statement = $this->db->prepare('SELECT id,username,points FROM USERS WHERE username=:username');
            $statement->bindValue(':username', $user['username'], PDO::PARAM_STR);
        } else {
            return false;
        }
        $statement->execute();
        if ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            return new User($row);
        }
        return false;
    }

    public function login_user($user)
    {
        if (!(isset($user['id']) || isset($user['username'])) || !isset($user['password']))
        {
            return false;
        }
        $statement = false;
        // Lookup by id is faster
        if (isset($user['id'])) {
            $statement = $this->db->prepare('SELECT * FROM USERS WHERE id=:id');
            $statement->bindValue(':id', $user['id'], PDO::PARAM_INT);
        } else {
            $statement = $this->db->prepare('SELECT * FROM USERS WHERE username=:username');
            $statement->bindValue(':username', $user['username'], PDO::PARAM_STR);
        }

        $statement->execute();
        while ($row = $statement->fetch(PDO::FETCH_ASSOC))
        {
            if (password_verify($user['password'], $row['password'])) {
                unset($row['password']);
                $row['id'] = intval($row['id']);
                // Create a login token for the user
                $row['token'] = $this->auth->create_token($row);
                return $row;
            }
        }
        return false;
    }

    public function create_user($user) {
        // Check if this username is alreay taken
        if ($this->check_user($user) != false) {
            return false;
        }
        if ($user == NULL || !isset($user['username']) || !isset($user['password']))
        {
            return false;
        }
        // Hash the password
        $hash_options = array('cost' => 11);
        $user['password'] = password_hash($user['password'], PASSWORD_BCRYPT, $hash_options);
        $statement = $this->db->prepare("INSERT INTO USERS(username,password) VALUES(:username,:password)");
        $statement->bindValue(':username', $user['username'], PDO::PARAM_STR);
        $statement->bindValue(':password', $user['password'], PDO::PARAM_STR);
        $statement->execute();
        $user['id'] = intval($this->db->lastInsertId());
        // Dont return the hashed password
        unset($user['password']);
        // Create a login token for the user
        $user['token'] = $this->auth->create_token($user);
        return $user;
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
?>
