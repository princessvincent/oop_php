
<?php
session_start();
class Dbh {
    private string $_hostname = "localhost";
    private string $_username = "root";
    private string $_password = "";
    private string $_database = "users";

    protected function connect() {
        $cons = new mysqli($this->_hostname, $this->_username, $this->_password, $this->_database);
        if($cons->connect_errno) {
            Dbh::showError("index.php", "Unable to connect database");
        }
        return $cons;
    }
}

