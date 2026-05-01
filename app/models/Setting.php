<?php
class Setting {
    private $db;
    public function __construct() {
        $this->db = new Database;
    }
}
