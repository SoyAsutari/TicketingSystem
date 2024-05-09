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

$accountId = $_GET['id'];

$sql = "SELECT * FROM accounts WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $accountId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: SA-Accounts-All.php");
    exit;
}

$account = $result->fetch_assoc();

$conn->close();
$needsRehash = password_needs_rehash($account['password'], PASSWORD_DEFAULT);
echo "Needs Rehash: " . ($needsRehash ? "true" : "false");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SA Main</title>
<link rel="stylesheet" href="menu.css">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class='dashboard'>
                <div class="dashboard-nav">
                    <header>
                        <a href="#!" class="menu-toggle"><i class="fas fa-bars"></i></a>
                        <a href="SA-Main.php" class="brand-logo">
                            <img src="imgs/StratoSolutionsLogo-1.png" alt="Strato Solutions Logo" style="max-width: 100px; max-height: 50px;">
                        </a>
                    </header>
                    <nav class="dashboard-nav-list">
                        <a href="SA-Main.php" class="dashboard-nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
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
                            <a href="#!" class="dashboard-nav-item dashboard-nav-dropdown-toggle active"><i class="fas fa-users"></i> Accounts </a>
                            <div class='dashboard-nav-dropdown-menu'>
                                <a href="SA-Accounts-All.php" class="dashboard-nav-dropdown-item active">All</a>
                                <a href="#" class="dashboard-nav-dropdown-item ">Strato Account</a>
                                <a href="#" class="dashboard-nav-dropdown-item">Client Account</a>
                                <a href="#" class="dashboard-nav-dropdown-item">Client's User</a>
                                <a href="SA-Create-Account.php" class="dashboard-nav-dropdown-item">Create New Account</a>
                            </div>
                        </div>
                        <a href="SA-Logs.php" class="dashboard-nav-item"><i class="fa-solid fa-warehouse"></i> Logs</a>
                        <a href="#" class="dashboard-nav-item"><i class="fa-solid fa-gears"></i> Settings </a>
                        <a href="#" class="dashboard-nav-item"><i class="fa-solid fa-user-tie"></i> Profile </a>
                        <div class="nav-item-divider"></div>
                        <a href="logout.php" class="dashboard-nav-item"><i class="fas fa-sign-out-alt"></i> Logout </a>
                    </nav>
                </div>
                <div class='dashboard-app' style="margin-left: 550px;">
                    <header class='dashboard-toolbar'><a href="#!" class="menu-toggle"><i class="fas fa-bars"></i></a></header>
                    <div class='dashboard-content'>
                        <div class='container'>
                            <h2>Account Details</h2>
                            <div class="card">
                                <div class="card-body">
                                    <ul class="list-group">
                                        <li class="list-group-item">
                                            <strong>Username:</strong>
                                            <span id="usernameView"><?php echo $account['username']; ?></span>
                                            <span id="usernameEdit" style="display: none;">
                                                <input type="text" id="editUsername" value="<?php echo $account['username']; ?>">
                                            </span>
                                            <i class="fa-solid fa-pen edit-icon" onclick="toggleEditMode('username')"></i>
                                        </li>
                                        <li class="list-group-item">
                                            <strong>Password:</strong> 
                                            <?php 
                                                if (password_needs_rehash($account['password'], PASSWORD_DEFAULT)) {
                                                    echo '<button onclick="revealPassword()">Reveal</button>'; 
                                                } else {
                                                    echo $account['password']; 
                                                }
                                            ?>
                                        </li>
                                        <li class="list-group-item"><strong>Email:</strong> <?php echo $account['email']; ?></li>
                                        <li class="list-group-item"><strong>Phone Number:</strong> <?php echo $account['notel']; ?></li>
                                        <?php if (isset($account['account_id'])): ?>
                                            <li class="list-group-item"><strong>Account ID:</strong> <?php echo $account['account_id']; ?></li>
                                        <?php endif; ?>
                                        <li class="list-group-item"><strong>Status:</strong> <?php echo $account['status']; ?></li>
                                    </ul>
                                    <a href="SA-Accounts-All.php" class="btn btn-primary mt-3 mr-2">Back</a>
                                    <button class="btn btn-danger mt-3" onclick="deleteAccount()">Delete Account</button>
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

<script>
    function toggleEditMode(field) {
        var viewElement = document.getElementById(field + 'View');
        var editElement = document.getElementById(field + 'Edit');
        var submitButton = document.getElementById('submitChanges');

        if (viewElement.style.display === 'none') {
            viewElement.style.display = 'inline';
            editElement.style.display = 'none';
            submitButton.style.display = 'none';
        } else {
            viewElement.style.display = 'none';
            editElement.style.display = 'inline';
            submitButton.style.display = 'inline';
        }
    }

    function deleteAccount() {
        if (confirm("Are you sure you want to delete this account?")) {
            window.location.href = "delete_account.php?id=<?php echo $accountId; ?>";
        }
    }

    function revealPassword() {
        alert("Password: <?php echo $account['password']; ?>");
    }
</script>

</body>
</html>
