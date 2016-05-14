<?php
class User {
    public $id;
    public $username;
    private $password;
    public $points;

    public function __construct($user) {
        $this->id = $user['id'];
        $this->username = $user['username'];
        $this->password = $user['password'];
        $this->points = $user['points'];
    }

    public function to_array() {
        return array(
            'id'        =>  $this->id,
            'username'  =>  $this->username,
            'password'  =>  $this->password,
            'points'    =>  $this->points,
        );
    }

    public function to_html() {
        $grav_url = "http://www.gravatar.com/avatar/";
        $grav_url .= md5(strtolower(trim($this->username)));
        $html = "<div class=\"item\">";
        $html .= "<img class=\"ui avatar image\" src=\"" . $grav_url . "\">";
        $html .= "<div class=\"content\">";
        $html .= "<a class=\"header\" href=\"/search/?username=" . $this->username . "\">" . $this->username . "</a>";
        $html .= "<div class=\"description\">Has <b>" . $this->points . "</b> points</div>";
        $html .= "</div>";
        $html .= "</div>";
        return $html;
    }
}
?>
