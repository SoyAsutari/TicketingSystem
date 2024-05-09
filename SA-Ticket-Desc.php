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

if (!isset($_GET['id'])) {
    header("Location: SA-Main.php");
    exit;
}

$ticket_id = $_GET['id'];

$sql = "SELECT tickets.*, accounts.username AS sender_username FROM tickets INNER JOIN accounts ON tickets.account_id = accounts.id WHERE tickets.id = '$ticket_id'";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $ticket = $result->fetch_assoc();
} else {
    header("Location: SA-Main.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply'])) {
    $reply = $_POST['reply'];
    $sender = $_SESSION['username'];
    $time = date("Y-m-d H:i:s"); 
    $sql = "INSERT INTO replies (ticket_id, sender, reply, time) VALUES ('$ticket_id', '$sender', '$reply', '$time')";
    if ($conn->query($sql) === TRUE) {
        header("Location: ".$_SERVER['PHP_SELF']."?id=$ticket_id");
        exit;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$previousReplies = "";
$sql = "SELECT * FROM replies WHERE ticket_id = '$ticket_id'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {

$serverTime = new DateTime($row['time'], new DateTimeZone('UTC'));
$serverTime->modify('+6 hours');
$localTime = $serverTime->format('Y-m-d H:i:s');

        $previousReplies .= "<p><strong>{$row['sender']} ({$localTime}):</strong> {$row['reply']}</p>";
    }
} else {
    $previousReplies = "<p>No replies yet.</p>";
}

$alertMessage = "";
if ($ticket['status'] == 'Closed') {
    $alertMessage = "<div class='alert alert-danger' role='alert'>This ticket is already closed.</div>";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SA Ticket Details</title>
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
        .description-box {
            border: 1px solid #ccc;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
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
                            <img src="imgs/StratoSolutionsLogo-1.png" alt="Strato Solutions Logo"
                                 style="max-width: 100px; max-height: 50px;">
                        </a>
                    </header>
                    <nav class="dashboard-nav-list">
                        <a href="SA-Main.php" class="dashboard-nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <div class='dashboard-nav-dropdown'>
                        <div class='dashboard-nav-dropdown'>
                        <a href="#!" class="dashboard-nav-item dashboard-nav-dropdown-toggle active">
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
                        </div>
                        <div class='dashboard-nav-dropdown'>
                            <a href="#!" class="dashboard-nav-item dashboard-nav-dropdown-toggle"><i class="fas accounts"></i> Accounts </a>
                            <div class='dashboard-nav-dropdown-menu'>
                                <a href="SA-Accounts-All.php" class="dashboard-nav-dropdown-item">All</a>
                                <a href="#" class="dashboard-nav-dropdown-item">Strato Account</a>
                                <a href="#" class="dashboard-nav-dropdown-item">Client Account</a>
                                <a href="#" class="dashboard-nav-dropdown-item">Client's User</a>
                                <a href="#" class="dashboard-nav-dropdown-item">Create New Account</a>
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
                    <header class='dashboard-toolbar'><a href="#!" class="menu-toggle"><i
                                class="fas fa-bars"></i></a></header>
                    <div class='dashboard-content'>
                        <div class='container' style="margin-left: -150px;">
                            <h2>Ticket Details</h2>
                            <div class="card">
                                <div class="card-body">
                                    <p><strong>ID:</strong> <?php echo $ticket['id']; ?></p>
                                    <p><strong>Subject:</strong> <?php echo $ticket['subject']; ?></p>
                                    <p><strong>Status:</strong> 
                                    <?php 
                                    $status = $ticket['status']; 
                                    if ($status == 'Closed') { 
                                     echo "<span class='badge badge-danger'>Closed</span>"; 
                                    } else { 
                                     echo "<span class='badge badge-success'>Open</span>"; 
                                    } 
                                    ?> 
                                    </p>
                                    <p><strong>Time:</strong> <?php echo $ticket['time']; ?></p>
                                    <p><strong>Description:</strong></p>
                                    <div class="description-box">
                                        <?php echo $ticket['description']; ?>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h3>Previous Replies</h3>
                            <?php echo $previousReplies; ?>

                           
                            <?php echo $alertMessage; ?>

                        
                            <form id="closeTicketForm" method="post" action="close_ticket.php?id=<?php echo $ticket_id; ?>">
                                <button type="submit" class="btn btn-danger">Close Ticket</button>
                            </form>
                            

                            <form id="replyForm" method="post" action="<?php echo $_SERVER['PHP_SELF']."?id=$ticket_id"; ?>">
                                <div class="form-group">
                                    <label for="reply">Reply:</label>
                                    <textarea class="form-control" id="reply" name="reply" rows="7"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                            <br>
                            <a href="SA-Ticket-All.php" class="btn btn-primary">Back to All Tickets</a>
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
<script>
    document.getElementById("replyForm").addEventListener("submit", function(event) {
        event.preventDefault(); 
        var reply = document.getElementById("reply").value;
        console.log("Submitting reply:", reply); 
        this.submit();
    });
</script>
</body>
</html>
