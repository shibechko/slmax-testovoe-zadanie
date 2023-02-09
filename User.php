<?php
function validateDate($date, $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}


class User {
    private $db = null;
    private $id = null;
    private $name;
    private $surname;
    private $birthday;
    private $gender;
    private $city;

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
            $this->id = $id;
            $this->updateFromArray($row);
        }
    }

    private function updateFromArray($arr) {
        $fields = array('name', 'surname', 'birthday', 'gender', 'city');
        if(count(array_intersect($fields, array_keys($arr))) != 5)
            throw new Exception("Not enough fields");

        if (!filter_var($arr['name'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[A-Za-zА-Яа-я]/"))))
            throw new Exception("Wrong name");
        if (!filter_var($arr['surname'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[A-Za-zА-Яа-я]/"))))
            throw new Exception("Wrong surname");
        if (!filter_var($arr['birthday'], FILTER_CALLBACK, array('options' => "validateDate")))
            throw new Exception("Wrong birthday");
        if ($arr['gender'] < 0 || $arr['gender'] > 1)
            throw new Exception("Wrong gender");
        if (!filter_var($arr['city'], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[A-Za-zА-Яа-я]/"))))
            throw new Exception("Wrong city");
                
        foreach($fields as $key) {
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

    public static function getAge($birthday) {
        $cur = time();
        $bday = strtotime($birthday);
        return (int) date("Y", $cur - $bday) - 1970;
    }

    public static function getGender($gender) {
        return $gender ? 'муж' : 'жен';
    }

    public function format(bool $age=false, bool $gender=false) {
        $user = new stdClass();
        $user->id = $this->id;
        $user->name = $this->name;
        $user->surname = $this->surname;
        $user->birthday = $age ? $this->getAge($this->birthday) : $this->birthday;
        $user->gender = $gender ? $this->getGender($this->gender) : $this->gender;
        $user->city = $this->city;
        return $user;
    }
}
