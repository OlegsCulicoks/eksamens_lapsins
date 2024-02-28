<?php
class Guestbook {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    

    public function getGuests() {
        $conn = $this->db->getConnection();
        $sql = "SELECT * FROM guests ORDER BY id DESC";
        $result = $conn->query($sql);

        $guests = array();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $guests[] = $row;
            }
        }

        return $guests;
    }

    public function addGuest($name, $email, $message) {
        $conn = $this->db->getConnection();
        $sql = "INSERT INTO guests (name, email, message) VALUES ('$name', '$email', '$message')";

        if ($conn->query($sql) === TRUE) {
            return true;
        } else {
            return "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    public function findGuest($name, $email, $message) {
        $conn = $this->db->getConnection();
        $sql = "SELECT * FROM guests WHERE name = '$name' AND email = '$email' AND message = '$message'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }
}
?>
