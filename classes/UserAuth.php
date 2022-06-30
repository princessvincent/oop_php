<?php
include_once 'Dbh.php';

class UserAuth extends Dbh {
    private static $db;

    public function __construct(){
        UserAuth::$db = new Dbh();
    }

    public function register($fullname, $email, $password, $confirmPassword, $country, $gender){
        $conn = UserAuth::$db->connect();
        if($this->checkEmailExist($email)) {
           echo "Oops! Email already Exist";
           header('location:forms/register.php');
        } else {
            if($this->confirmPasswordMatch($password, $confirmPassword)){
                $sql = "INSERT INTO students (`full_names`, `email`, `password`, `country`, `gender`) VALUES ('{$fullname}','{$email}', '{$password}', '{$country}', '{$gender}')";
                if($conn->query($sql)){
                    $_SESSION['email'] = $email;
                    echo "Registration successful";
                    header('location:dashboard.php');
                } else {
                    echo "Unable to Register User";
                    header('location:forms/register.php');
                }
            } else {
                echo "Passwords does not match"; 
                header('location:forms/register.php');
            }
        }
    }

    public function login($email, $password){
        $conn = UserAuth::$db->connect();
        $sql = "SELECT `password` FROM students WHERE email='{$email}'";
        $result = $conn->query($sql);
        if($result->num_rows > 0){
            $data = $result->fetch_array();
            if($data[0] == $password) {
                $_SESSION['email'] = $email;
                echo "Login successful";
                header('location:dashboard.php');
            } else {
                echo "Incorrect password";
                header('location:login.php');
            }
        } else {
            echo  "Email does not exist";
            header('location:login.php');
        }
    }

    public function checkEmailExist($email) {
        $conn = UserAuth::$db->connect();
        $sql = "SELECT id FROM students WHERE email = '{$email}'";
        $result = $conn->query($sql);
        if($result->num_rows > 0){
            return true;
        } else {
            return false;
        }
    }

    public function getAllusers(){
        $conn = UserAuth::$db->connect();
        $sql = "SELECT * FROM students";
        $result = $conn->query($sql);
        echo"<html>
        <head>
        <link rel='stylesheet' href='https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css' integrity='sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T' crossorigin='anonymous'>
        </head>
        <body>
        <center><h1><u> ZURI PHP students </u> </h1> 
        <table class='table table-bordered' border='0.5' style='width: 80%; background-color: smoke; border-style: none'; >
        <tr style='height: 40px'>
            <thead class='thead-dark'> <th>ID</th><th>Full Names</th> <th>Email</th> <th>Gender</th> <th>Country</th> <th>Action</th>
        </thead></tr>";
        if($result->num_rows > 0){ 
            while($data = mysqli_fetch_assoc($result)){
                //show data
                echo "<tr style='height: 20px'>".
                    "<td style='width: 50px; background: gray'>" . $data['id'] . "</td>
                    <td style='width: 150px'>" . $data['full_names'] .
                    "</td> <td style='width: 150px'>" . $data['email'] .
                    "</td> <td style='width: 150px'>" . $data['gender'] . 
                    "</td> <td style='width: 150px'>" . $data['country'] . 
                    "</td>
                    <td style='width: 150px'> 
                    <form action='action.php' method='post'>
                    <input type='hidden' name='id'" .
                     "value=" . $data['id'] . ">".
                    "<button class='btn btn-danger' type='submit', name='delete'> DELETE </button> </form> </td>".
                    "</tr>";
            }
            echo "</table></table></center></body></html>";
        }
    }

    public function deleteUser(int $id){
        $conn = UserAuth::$db->connect();
        $sql = "DELETE FROM students WHERE id = '{$id}'";
        if($conn->query($sql) === TRUE){
            echo "deleted successfully";
           header('location:./action.php');
        } else {
            echo "Unable to Delete Student";
            header('location:./action.php');
        }
    }

    public function updateUser($email, $password){
        $conn = UserAuth::$db->connect();
        if($this->checkEmailExist($email)) {
            $sql = "UPDATE students SET password = '{$password}' WHERE email = '{$email}'";
            if($conn->query($sql) === TRUE){
                echo "Password has been successfully reset";
                header('location:forms/login.php');
            } else {
                echo "Password reset failed";
                header('location:forms/resetpassword.php');
            }
        } else {
            echo "Email does not exist";
            header('location:forms/resetpassword.php');
        }
    }

    public function logout(){
        session_destroy();
        header('Location: index.php');
    }

    public function confirmPasswordMatch($password, $confirmPassword){
        if($password === $confirmPassword){
            return true;
        } else {
            return false;
        }
    }
}