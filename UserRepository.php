<?php

if(!class_exists(User::class)) {
    throw new Exception("Class User does not exist");
} else {

    class UserRepository {
        private $db;
        private $ids = [];

        public function __construct($db, $filters=[]) {
            $this->db = $db;
            $query = "Select id from users ";
            $where = $this->getFiltersQuery($filters);
            $vals = $this->getValuesQuery($filters);
            if ($where)
                $query .= " where " . $where;
            $q = $this->db->prepare($query);
            $q->execute($vals);
            $this->ids = $q->fetchAll(PDO::FETCH_COLUMN, 0);
        }

        private static function getFiltersQuery($filters) {
            $where = array_map(function ($i) {
                return "({$i[0]} {$i[1]} ?)"; 
            }, $filters);
            $where = implode(' and ', $where);
            return $where;
        }

        private static function getValuesQuery($filters) {
            $vals = array_map(function ($i) {
                return $i[2];
            }, $filters);
            return $vals;
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