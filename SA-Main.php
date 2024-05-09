<?php
session_start();

include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit;
}

$sqlOpenTicketsCount = "SELECT COUNT(*) AS open_tickets_count FROM tickets WHERE status = 'Open'";
$resultOpenTicketsCount = $conn->query($sqlOpenTicketsCount);
if ($resultOpenTicketsCount && $resultOpenTicketsCount->num_rows > 0) {
    $rowOpenTicketsCount = $resultOpenTicketsCount->fetch_assoc();
    $openTicketsCount = $rowOpenTicketsCount['open_tickets_count'];
} else {
    $openTicketsCount = 0;
}

$username = $_SESSION['username']; 
$sql = "SELECT * FROM accounts WHERE username = '$username'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $name = $user['username'];
    $status = $user['status']; 
} else {
    $name = 'Unknown';
    $status = 'Unknown';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SA Main</title>
  <link rel="stylesheet" href="menu.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <style>
    .menu {
      padding-top: 50px;
    }
    .menu-item {
      padding: 10px;
      border-bottom: 1px solid #dee2e6;
    }
  </style>
</head>
<body>

<div class="container-fluid">
  <div class="row">
    <div class="col-md-3">
    </div>
    <div class="col-md-9">
      <div class='dashboard'>
        <div class="dashboard-nav">
        <header>
  <a href="#!" class="menu-toggle"><i class="fas fa-bars"></i></a>
  <a href="SA-Main.php" class="brand-logo">
    <img src="imgs/StratoSolutionsLogo-1.png" alt="Strato Solutions Logo" style="max-width: 100px; max-height: 50px;">
  </a>
</header>

          <nav class="dashboard-nav-list">
            
          <a href="SA-Main.php" class="dashboard-nav-item active"><i class="fas fa-tachometer-alt active"></i> Dashboard</a>
                        <div class='dashboard-nav-dropdown'>
                        <div class='dashboard-nav-dropdown'>
                        <a href="#!" class="dashboard-nav-item dashboard-nav-dropdown-toggle">
                         <i class="fa-solid fa-ticket"></i> Tickets
                      <?php if ($openTicketsCount > 0): ?>
                         <span class="badge badge-danger"><?php echo $openTicketsCount; ?></span>
                      <?php endif; ?>
                        </a>
                    <div class='dashboard-nav-dropdown-menu'>
                     <a href="SA-Ticket-All.php" class="dashboard-nav-dropdown-item">All
                     <?php if ($openTicketsCount > 0): ?>
                         <span class="badge badge-danger"><?php echo $openTicketsCount; ?></span>
                      <?php endif; ?>
                     </a>
                     <a href="SA-Ticket-Open.php" class="dashboard-nav-dropdown-item">Open
                     <?php if ($openTicketsCount > 0): ?>
                         <span class="badge badge-danger"><?php echo $openTicketsCount; ?></span>
                      <?php endif; ?>
                     </a>
                     <a href="SA-Ticket-Close.php" class="dashboard-nav-dropdown-item">Closed</a>
              </div>    
            </div>
            <div class='dashboard-nav-dropdown'>
              <a href="#!" class="dashboard-nav-item dashboard-nav-dropdown-toggle"><i class="fa-solid fa-users"></i> Accounts </a>
              <div class='dashboard-nav-dropdown-menu'>
                <a href="SA-Accounts-All.php" class="dashboard-nav-dropdown-item">All</a>
                <a href="#" class="dashboard-nav-dropdown-item">Strato Account</a>
                <a href="#" class="dashboard-nav-dropdown-item">Client Account</a>
                <a href="#" class="dashboard-nav-dropdown-item">Client's User</a>
                <a href="SA-Create-Account.php" class="dashboard-nav-dropdown-item">Create New Account</a>
              </div>
            </div>
            <a href="SA-Logs.php" class="dashboard-nav-item"><i class="fa-solid fa-warehouse"></i> Logs</a>
              
        
            </div>
            <a href="#" class="dashboard-nav-item"><i class="fa-solid fa-gears"></i> Settings </a>
            <a href="#" class="dashboard-nav-item"><i class="fa-solid fa-user-tie"></i> Profile </a>
            <div class="nav-item-divider"></div>
            <a href="logout.php" class="dashboard-nav-item"><i class="fas fa-sign-out-alt"></i> Logout </a>

          </nav>
        </div>
        <div class='dashboard-app'>
          <header class='dashboard-toolbar'><a href="#!" class="menu-toggle"><i class="fas fa-bars"></i></a></header>
          <div class='dashboard-content'>
            <div class='container'>

              <div class="card" style="margin-left: -200px;">
                <div class="card-header">
                  <h1>Welcome back, <?php echo $name; ?>!</h1>
                </div>
              </div>

              <div class="card" style="margin-left: -200px;">
                <div class="card-header">
                  <h5 class="card-title">Open Tickets Chart</h5>
                </div>
                <div class="card-body">
                  <canvas id="openTicketsChart"></canvas>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://kit.fontawesome.com/d566b3549f.js" crossorigin="anonymous"></script>
<script>
 const mobileScreen = window.matchMedia("(max-width: 990px )");
$(document).ready(function () {
    $(".dashboard-nav-dropdown-toggle").click(function () {
        $(this).closest(".dashboard-nav-dropdown")
            .toggleClass("show")
            .find(".dashboard-nav-dropdown")
            .removeClass("show");
        $(this).parent()
            .siblings()
            .removeClass("show");
    });
    $(".menu-toggle").click(function () {
        if (mobileScreen.matches) {
            $(".dashboard-nav").toggleClass("mobile-show");
        } else {
            $(".dashboard").toggleClass("dashboard-compact");
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  var ctx = document.getElementById('openTicketsChart').getContext('2d');
  var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July',],
      datasets: [{
        label: 'Open Tickets',
        data: [12, 19, 3, 5, 2, 3, 10],
        backgroundColor: 'rgba(54, 162, 235, 0.2)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>
</body>
</html>
