<?php
include "config.php";

/* TOTAL USERS COUNT */
$countSql = "SELECT COUNT(*) AS total_users FROM users";
$countResult = mysqli_query($conn, $countSql);
$totalUsers = mysqli_fetch_assoc($countResult)['total_users'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Dashboard</h2>
        <a href="register.php" class="btn btn-success">+ New Register</a>
    </div>

    <!-- Stats Card -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <h6 class="text-muted">Total Users</h6>
                    <h1 class="text-primary"><?= $totalUsers ?></h1>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            Registered Users
        </div>

        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-secondary">
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Registered On</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>

                <tbody>
                <?php
                $sql = "SELECT * FROM users ORDER BY id DESC";
                $result = mysqli_query($conn, $sql);
                $i = 1;

                while ($row = mysqli_fetch_assoc($result)) {
                ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= htmlspecialchars($row['name']); ?></td>
                        <td><?= htmlspecialchars($row['email']); ?></td>
                        <td>
                            <?= isset($row['created_at']) 
                                ? date("d M Y", strtotime($row['created_at'])) 
                                : '—'; ?>
                        </td>
                        <td class="text-center">
                            <a href="edit.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete.php?id=<?= $row['id']; ?>"
                               onclick="return confirm('Are you sure?')"
                               class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>
