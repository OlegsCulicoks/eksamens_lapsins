<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guestbook</title>
    <link href="https://fonts.googleapis.com/css2?family=Kavoon&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="guest-form">
            <h2>Add Guest</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="name">Name:</label><br>
                <input type="text" id="name" name="name"><br>
                <label for="email">Email:</label><br>
                <input type="email" id="email" name="email"><br>
                <label for="message">Message:</label><br>
                <textarea id="message" name="message"></textarea><br><br>
                <input type="submit" value="Submit">
            </form>
        </div>
        <div class="search-sort">
            <h2>Search & Sort</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="search">Search:</label>
                <input type="text" id="search" name="search">
                <input type="submit" value="Search">
            </form>
            <form method="get" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="sort">Sort by:</label>
                <select id="sort" name="sort">
                    <option value="name">Name</option>
                    <option value="email">Email</option>
                    <option value="message">Message</option>
                </select>
                <input type="submit" value="Sort">
            </form>
        </div>
    </div>

    <?php
    include 'Database.php';
    include 'Guestbook.php';

    
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "guestbook";
    $db = new Database($servername, $username, $password, $dbname);
    $db->connect();
    $guestbook = new Guestbook($db);

    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"] ?? '';
        $email = $_POST["email"] ?? '';
        $message = $_POST["message"] ?? '';

       
        if (!empty($name) && !empty($email) && !empty($message)) {
            $existingGuest = $guestbook->findGuest($name, $email, $message);
            if (!$existingGuest) {
                $result = $guestbook->addGuest($name, $email, $message);
                if ($result !== true) {
                    echo "<p>Error: " . $result . "</p>";
                }
            } else {
                echo "<p>Guest already exists!</p>";
            }
        } else {
            echo "<p>Please fill out all fields!</p>";
        }
    }

    
    $guests = $guestbook->getGuests();

    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["search"])) {
        $search = $_POST["search"];
        $filteredGuests = array_filter($guests, function ($guest) use ($search) {
            return stripos($guest["name"], $search) !== false || 
                   stripos($guest["email"], $search) !== false ||
                   stripos($guest["message"], $search) !== false;
        });
        $guests = $filteredGuests;
    }

    
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["sort"])) {
        $sort = $_GET["sort"];
        if ($sort === "name") {
            usort($guests, function($a, $b) {
                return strtolower($a['name']) <=> strtolower($b['name']);
            });
        } elseif ($sort === "email") {
            usort($guests, function($a, $b) {
                return strtolower($a['email']) <=> strtolower($b['email']);
            });
        } elseif ($sort === "message") {
            usort($guests, function($a, $b) {
                return strtolower($a['message']) <=> strtolower($b['message']);
            });
        }
    }

    
    echo "<div class='guestbook-table'>";
    if (!empty($guests)) {
        echo "<h3>Guestbook Entries:</h3>";
        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>Name</th>";
        echo "<th>Email</th>";
        echo "<th>Message</th>";
        echo "</tr>";
        foreach ($guests as $guest) {
            echo "<tr>";
            echo "<td>" . $guest["name"] . "</td>";
            echo "<td>" . $guest["email"] . "</td>";
            echo "<td>" . $guest["message"] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No records</p>";
    }
    echo "</div>";

    
    $db->close();
    ?>
</body>
</html>
