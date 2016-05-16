<?php
class Bounty {
    private $db;

    public $id;
    public $title;
    public $description;
    public $creator;
    public $awarded;
    public $points;

    public function __construct($bounty) {
        $this->db = new Database;

        $this->id = $bounty['id'];
        $this->title = $bounty['title'];
        $this->description = $bounty['description'];
        $this->points = $bounty['points'];
        $this->creator = $bounty['creator'];
        $this->awarded = $bounty['awarded'];
    }

    public function to_array() {
        return array(
            'id'            =>  $this->id,
            'title'         =>  $this->title,
            'description'   =>  $this->description,
            'creator'       =>  $this->creator,
            'awarded'       =>  $this->awarded,
            'points'        =>  $this->points,
        );
    }

    public function to_html() {
        $creator_name = $this->db->username($this->creator);
        $awarded_name = $this->db->username($this->awarded);
        $creator_img = "https://www.gravatar.com/avatar/";
        $creator_img .= md5(strtolower(trim($creator_name)));
        $awarded_img = "https://www.gravatar.com/avatar/";
        $awarded_img .= md5(strtolower(trim($awarded_name)));
        $html = "<div class=\"ui message\">";
        $html .= "<div class=\"item\">";
        $html .= "<div class=\"content\">";
        $html .= "<a class=\"header\" href=\"/bounty/?id=" . $this->id . "\">" . xssafe($this->title) . "</a>";
        $html .= "<p><img class=\"ui avatar image\" src=\"" . $creator_img . "\"/>Created by " . xssafe($creator_name) . "</p>";
        $html .= "<div class=\"description\">";
        $html .= "<p>Worth <b>" . $this->points . "</b> points</p>";
        $html .= "<p>" . xssafe($this->description) . "</p>";
        if ($this->awarded != 0) {
            $html .= "<br>";
            $html .= "<p><img class=\"ui avatar image\" src=\"" . $awared_img . "\"/>Awarded to " . xssafe($awared_name) . "</p>";
        }
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        $html .= "</div>";
        return $html;
    }
}
?>
