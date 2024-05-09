<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Helpdesk</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    .modal-backdrop {
      background-color: rgba(0, 0, 0, 0.5) !important; 
    }
    .logo {
      max-width: 200px;
      position: absolute;
      top: 10px;
      left: 10px;
    }
  </style>
</head>
<body>

<div class="container mt-5">
    <img src="Imgs/StratoSolutionsLogo-1.png" alt="Strato Solutions Logo" class="logo">
    <br>
    <h1>Welcome to Our Company Helpdesk</h1>
    <p>This is a brief introduction about our company and how we strive to provide excellent support to our clients.</p>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#loginModal">
      Helpdesk
    </button>
</div>

<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="loginModalLabel">Login Required</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" class="form-control" id="username" name="username" placeholder="Enter username">
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
          </form>
        </div>
      </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'config.php';

    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM accounts WHERE username = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $username);

        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();
                $stored_password = $row['password'];
                $status = $row['status'];

                if ($password === $stored_password) {
                    $_SESSION['loggedin'] = true;
                    $_SESSION['username'] = $username;
                    $_SESSION['status'] = $status;

                    if ($status == 'Strato Admin') {
                        header('Location: SA-Main.php');
                    } else {
                        header('Location: default-page.php');
                    }
                    exit;
                } else {
                    echo "Invalid username or password.";
                }
            } else {
                echo "Invalid username or password.";
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>
<script src="https://kit.fontawesome.com/d566b3549f.js" crossorigin="anonymous"></script>

