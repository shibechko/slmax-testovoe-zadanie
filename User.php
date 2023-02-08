<?php

class User {
    private $db = null;
    public $id = null;
    public $name;
    public $surname;
    public $birthday;
    public $gender;
    public $city;

    public function __construct(&$db, $data) {
        $this->db = $db;
        if(is_int($data)) {
            $this->getById($data);
        } else if (is_array($data)) {
            $this->updateFromArray($data);
            $this->create();
        }
    }

    private function getById($id) {
        $query = "SELECT * FROM users WHERE id = :id";
        $q = $this->db->prepare($query);
        $q->execute(['id' => $id]);
        if(!$row = $q->fetch(PDO::FETCH_ASSOC)) {
            throw new Exception('User not found');
        } else {
            $this->updateFromArray($row);
        }
    }

    private function updateFromArray($arr) {
        foreach(array_keys($arr) as $key) {
            $this->{$key} = $arr[$key];
        }
    }

    private function create() {
        $q = $this->db->prepare('INSERT users(`name`, `surname`, `birthday`, `gender`, `city`) VALUES (?, ?, ?, ?, ?)');
        if ($q->execute([$this->name, $this->surname, $this->birthday, $this->gender, $this->city])) {
            $this->id = (int) $this->db->lastInsertId();
        }
    }

    public function save() {
        $q = $this->db->prepare('UPDATE users SET `name`=?, `surname`=?, `birthday`=?, `gender`=?, `city`=? WHERE `id`=?');
        $q->execute([$this->name, $this->surname, $this->birthday, $this->gender, $this->city, $this->id]);
    }

    public function remove() {
        $q = $this->db->prepare('DELETE FROM users WHERE `id`=?');
        $q->execute([$this->id]);
    }

}
