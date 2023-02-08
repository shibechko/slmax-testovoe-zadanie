<?php

if(!class_exists(User::class)) {
    throw new Exception("Class User does not exist");
} else {

    class UserRepository {
        private $db;
        private $ids = [];

        public function __construct($db, $filters=[]) {
            $this->db = $db;
            $q = $this->db->prepare("Select id from users");
            $q->execute();
            $this->ids = $q->fetchAll(PDO::FETCH_COLUMN, 0);
        }

        public function getUsers() {
            return array_map(function ($id) {
                    return new User($this->db, $id);
                }, $this->ids);
        }

        public function delUsers() {
            foreach($this->getUsers() as $user) 
                $user->remove();
        }
    }

}