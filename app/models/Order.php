<?php
class Order {
    private $db;
    public function __construct() {
        $this->db = new Database;
    }
}
