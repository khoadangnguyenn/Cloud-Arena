<?php
  class Product {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

    // Get All Products
    public function getProducts(){
      $this->db->query('SELECT * FROM products ORDER BY created_at DESC');
      $results = $this->db->resultSet();
      return $results;
    }

    // Get Product By ID
    public function getProductById($id){
      $this->db->query('SELECT * FROM products WHERE id = :id');
      $this->db->bind(':id', $id);
      $row = $this->db->single();
      return $row;
    }

    // Add Product
    public function addProduct($data){
      $this->db->query('INSERT INTO products (name, description, price, image, stock) VALUES(:name, :description, :price, :image, :stock)');
      // Bind values
      $this->db->bind(':name', $data['name']);
      $this->db->bind(':description', $data['description']);
      $this->db->bind(':price', $data['price']);
      $this->db->bind(':image', $data['image']);
      $this->db->bind(':stock', $data['stock']);

      // Execute
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    // Update Product
    public function updateProduct($data){
      $this->db->query('UPDATE products SET name = :name, description = :description, price = :price, image = :image, stock = :stock WHERE id = :id');
      // Bind values
      $this->db->bind(':id', $data['id']);
      $this->db->bind(':name', $data['name']);
      $this->db->bind(':description', $data['description']);
      $this->db->bind(':price', $data['price']);
      $this->db->bind(':image', $data['image']);
      $this->db->bind(':stock', $data['stock']);

      // Execute
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    // Delete Product
    public function deleteProduct($id){
      $this->db->query('DELETE FROM products WHERE id = :id');
      $this->db->bind(':id', $id);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }
  }
