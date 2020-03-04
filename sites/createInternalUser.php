<?php
session_start();
require_once(__DIR__ . '/../modules/config.php');

// creates a new empty user Object
$user = new User();
/*
unsearialies the previous serialised user 
and saves it as the newly created user
*/
$user = unserialize($_SESSION['user']);

// Determins if the User in the session storage is set and an admin
if ($user) {
    /*
    Asks the user to login if the userSite.php got accesed via the searchbar
    and no User is in the session storage
    */
    if ($user->isAdministrator == 'n') {
        die('Bitte als administrator <a href="logout.php">einloggen</a>');
    } else {
        // Checks if the register form is submited
        if (isset($_GET['register'])) {
            // Creates a new internalUser object
            $newInternalUser = new InternalUser();
            // Sets up the needed variables 
            $error = false;
            $newInternalUser->email = $_POST['email'];
            $newInternalUser->password = $_POST['password'];
            $newInternalUser->userRole = $_POST['userRole'];
            $newInternalUser->firstname = $_POST['firstname'];
            $newInternalUser->lastname = $_POST['lastname'];
            $newInternalUser->postalCode = $_POST['postalCode'];
            $newInternalUser->city = $_POST['city'];
            $newInternalUser->street = $_POST['street'];
            $newInternalUser->password = $_POST['password'];
            $passwordConfirm = $_POST['passwordConfirm'];
            $userRole = $_POST['userRole'];

            // Checks if the given email is a valide email (formwise)
            if (!filter_var($newInternalUser->email, FILTER_VALIDATE_EMAIL)) {
                // Sets the errorMessage variable 
                $errorMessage = 'Bitte eine gültige E-Mail-Adresse eingeben<br>';
                $error = true;
            }

            // Checks if the given email has at least one char
            if (strlen($newInternalUser->password) == 0) {
                // Sets the errorMessage variable 
                $errorMessage = 'Bitte ein password angeben<br>';
                $error = true;
            }

            // Checks if the passwords matches
            if ($newInternalUser->password != $passwordConfirm) {
                // Sets the errorMessage variable 
                $errorMessage = 'Die Passwörter müssen übereinstimmen<br>';
                $error = true;
            }

            // Validates that the email is not registered yet 
            if (!$error) {
                $databaseUser = $database->getUser($newInternalUser->email);

                // Checks the response from the database
                if ($databaseUser->email) {


                    // Sets the errorMessage variable 
                    $errorMessage = 'Diese E-Mail-Adresse ist bereits vergeben<br>';
                    $error = true;
                }
            }
            // Regisers a new user 
            if (!$error) {
                $result = $database->createInternalUser($newInternalUser);
                if ($result) {
                    /* 
                    Displayes a html tag to confirn the creation of an new user
                    and provides a link to th login. 
                    */
                    $message = 'User wurde erfolgreich registriert.</a>';
                } else {
                    
                    // Sets the errorMessage variable 
                    $errorMessage = 'Beim Abspeichern ist leider ein Fehler aufgetreten<br>';
                }
            }
        }
    }
} else {
    // Dies if the user in the session storage is not set
    die('Bitte zuerst <a href="logout.php">einloggen</a>');
}
?>
<!DOCTYPE html>
<html lang="en">
<title>Register</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="/../src/css/styles.css">
<link rel="stylesheet" href="/../src/css/navbar.css">

<body>
    <!-- Navbar -->
    <div id="navbar">
        <ul style="display: flex;">
            <li> <a href="index.html" class="bar-item button padding-large white">Home</a></li>
            <div style="flex-grow: 1;"></div>
            <li><a href="adminSite.php" class="bar-item button padding-large white">Admin Area</a></li>
            <li><a href="/sites/logout.php" class="bar-item button padding-large white">Logout</a></li>
        </ul>
    </div>
    <!-- Content -->
    <?php
    // Displays an error message on the site if one is set 
    if (isset($errorMessage)) {
        echo $errorMessage;
    }

    // Displays a message on the site if one is set 
    if (isset($message)) {
        echo $message;
    }
    ?>
    <!-- Div that centers the displayed register form -->
    <div style=" display: block;margin-left: auto;margin-right: auto;width: max-content;">
        <form action="?register=1" method="post">
            E-Mail:<br>
            <input type="text" size="40" maxlength="250" name="email"><br><br>
            Dein Password:<br>
            <input type="password" size="40" maxlength="250" name="password"><br>
            Password wiederholen:<br>
            <input type="password" size="40" maxlength="250" name="passwordConfirm"><br><br>
            Vorname:<br>
            <input type="text" size="40" maxlength="250" name="firstname"><br><br>
            Nachname:<br>
            <input type="text" size="40" maxlength="250" name="lastname"><br><br>
            Postleitzahl:<br>
            <input type="text" size="40" maxlength="250" name="postalCode"><br><br>
            Stadt:<br>
            <input type="text" size="40" maxlength="250" name="city"><br><br>
            Strasse:<br>
            <input type="text" size="40" maxlength="250" name="street"><br><br>
            User Rolle<br>
            <select name="userRole">
                <option value="n">Trainer</option>
                <option value="y">Admin</option>
            </select><br><br>
            <input type="submit" value="Abschicken">
        </form>
    </div>
    <!-- Footer -->
    <footer>
        <div>
            <p>LAP (c) 2020</p>
        </div>
    </footer>
</body>

</html>