<?php
  class NewsModel {
    private $db;

    public function __construct(){
      $this->db = new Database;
    }

    // Get All News
    public function getNews(){
      $this->db->query('SELECT news.*, users.username as author_name FROM news JOIN users ON news.author_id = users.id ORDER BY news.created_at DESC');
      return $this->db->resultSet();
    }

    // Get News By ID
    public function getNewsById($id){
      $this->db->query('SELECT news.*, users.username as author_name FROM news JOIN users ON news.author_id = users.id WHERE news.id = :id');
      $this->db->bind(':id', $id);
      return $this->db->single();
    }

    // Add News
    public function addNews($data){
      $this->db->query('INSERT INTO news (title, content, image, author_id, seo_keyword, seo_description) VALUES(:title, :content, :image, :author_id, :seo_keyword, :seo_description)');
      // Bind values
      $this->db->bind(':title', $data['title']);
      $this->db->bind(':content', $data['content']);
      $this->db->bind(':image', $data['image']);
      $this->db->bind(':author_id', $data['author_id']);
      $this->db->bind(':seo_keyword', $data['seo_keyword']);
      $this->db->bind(':seo_description', $data['seo_description']);

      // Execute
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    // Update News
    public function updateNews($data){
      $this->db->query('UPDATE news SET title = :title, content = :content, image = :image, seo_keyword = :seo_keyword, seo_description = :seo_description WHERE id = :id');
      // Bind values
      $this->db->bind(':id', $data['id']);
      $this->db->bind(':title', $data['title']);
      $this->db->bind(':content', $data['content']);
      $this->db->bind(':image', $data['image']);
      $this->db->bind(':seo_keyword', $data['seo_keyword']);
      $this->db->bind(':seo_description', $data['seo_description']);

      // Execute
      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }

    // Delete News
    public function deleteNews($id){
      $this->db->query('DELETE FROM news WHERE id = :id');
      $this->db->bind(':id', $id);

      if($this->db->execute()){
        return true;
      } else {
        return false;
      }
    }
  }
