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
$sql = "SELECT tickets.id, accounts.username AS sender, tickets.subject, tickets.status, tickets.time 
        FROM tickets 
        INNER JOIN accounts ON tickets.account_id = accounts.id
        WHERE tickets.status = 'Closed'";
$result = $conn->query($sql);

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

        table tbody tr:hover {
            background-color: #f5f5f5;
            cursor: pointer;
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
                     <a href="SA-Ticket-Close.php" class="dashboard-nav-dropdown-item  active">Closed</a>
                            </div>
                        </div>
                        <div class='dashboard-nav-dropdown'>
                            <a href="#!" class="dashboard-nav-item dashboard-nav-dropdown-toggle"><i
                                    class="fas accounts"></i> Accounts </a>
                            <div class='dashboard-nav-dropdown-menu'>
                                <a href="Sa-Accounts-All.php" class="dashboard-nav-dropdown-item">All</a>
                                <a href="#" class="dashboard-nav-dropdown-item">Strato Account</a>
                                <a href="#" class="dashboard-nav-dropdown-item">Client Account</a>
                                <a href="#" class="dashboard-nav-dropdown-item">Client's User</a>
                                <a href="SA-Create-Account.php" class="dashboard-nav-dropdown-item">Create New
                                    Account</a>
                            </div>
                        </div>
                        <a href="SA-Logs.php" class="dashboard-nav-item"><i class="fa-solid fa-warehouse"></i> Logs</a>
                        </div>
                        <a href="#" class="dashboard-nav-item"><i class="fa-solid fa-gears"></i> Settings </a>
                        <a href="#" class="dashboard-nav-item"><i class="fa-solid fa-user-tie"></i> Profile </a>
                        <div class="nav-item-divider"></div>

                        <a href="logout.php" class="dashboard-nav-item"><i class="fas fa-sign-out-alt"></i> Logout
                        </a>

                    </nav>
                </div>
                <div class='dashboard-app'>
                    <header class='dashboard-toolbar'><a href="#!" class="menu-toggle"><i
                                class="fas fa-bars"></i></a></header>
                    <div class='dashboard-content'>
                        <div class='container'>


                            <table class="table" style="margin-left: -200px;">
                                <thead class="thead-dark">
                                <tr>
                                    <th>Id</th>
                                    <th>Sender</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr class='ticket-row' data-id='" . $row['id'] . "'>";
                                        echo "<td>" . $row['id'] . "</td>";
                                        echo "<td>" . $row['sender'] . "</td>";
                                        echo "<td>" . $row['subject'] . "</td>";
                                        $statusBadge = ($row['status'] == 'Closed') ? "<span class='badge badge-danger'>Closed</span>" : "<span class='badge badge-success'>Open</span>";
                                        echo "<td>" . $statusBadge . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6'>No closed tickets found.</td></tr>";
                                }

                                $conn->close();
                                ?>

                                </tbody>
                            </table>

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

        $('.ticket-row').click(function () {
            const ticketId = $(this).data('id');
            window.location.href = 'SA-Ticket-Desc.php?id=' + ticketId;
        });
    });
</script>
</body>
</html>
