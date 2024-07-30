<?php
define("DBNAME", "api");
define("DBUSER", "root");
define("DBPASS", "");

try {
    $conn = new PDO("mysql:host=localhost;dbname=" . DBNAME, DBUSER, DBPASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    echo $e->getMessage();
}

echo 'I GOT HERE';

header('Content-type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $error = array();

   

    // Validation
    if (empty($_POST['name'])) {
        $error['name'] = "Enter First Name";
    }
    if (empty($_POST['email'])) {
        $error['email'] = "Enter Email";
    }
    if (empty($_POST['password'])) {
        $error['password'] = "Enter Password";
    }
    // Check if email already exists
    if (empty($error)) {
        $statement = $conn->prepare("SELECT * FROM user WHERE email = :email");
        $statement->bindParam(":email", $_POST['email']);
        $statement->execute();
        if ($statement->rowCount() > 0) {
            $error['email'] = "Email already exists";
        }
    }

    // If no errors, proceed with inserting data into the database
    if (empty($error)) {
        $encrypted_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO user (name, email, password, time_created, date_created) VALUES (:fname, :lname, :email, :phone, :password, :dob, :country, NOW(), NOW())");
        $stmt->execute([
            ':name' => $_POST['name'],
            ':email' => $_POST['email'],
            ':password' => $encrypted_password
        ]);

        return json_encode($stmt);
        exit();
    }
}
?>]