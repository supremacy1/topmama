<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center">Filter Transactions</h1>
        <form method="GET" action="reports.php" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" name="start_date" id="start_date" value="<?= htmlspecialchars($start_date) ?>">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" name="end_date" id="end_date" value="<?= htmlspecialchars($end_date) ?>">
                </div>
                <div class="col-md-4">
                    <label for="category" class="form-label">Category</label>
                    <input type="text" class="form-control" name="category" id="category" value="<?= htmlspecialchars($category) ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Filter</button>
        </form>

        <h2 class="text-center">Transaction List</h2>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?= htmlspecialchars($transaction['id']) ?></td>
                        <td><?= htmlspecialchars($transaction['category']) ?></td>
                        <td><?= htmlspecialchars($transaction['amount']) ?></td>
                        <td><?= htmlspecialchars($transaction['transaction_date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
