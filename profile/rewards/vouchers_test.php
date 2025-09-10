<?php
/*
Active vouchers → isUsed = 0 AND date range valid.
Upcoming vouchers → start_date > NOW().
Past vouchers → either isUsed = 1 OR end_date < NOW()
*/
session_start();
include(__DIR__ . '/../../connect_db.php');

// 1. Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// 2. Set PHP timezone
date_default_timezone_set('Asia/Kuala_Lumpur');

// 3. Force MySQL timezone
$conn->query("SET time_zone = '+08:00'");

// 4. Fetch MySQL current time safely with fallback
$now = null;
try {
    $nowResult = $conn->query("SELECT NOW() as `current_time`");
    if ($nowResult && $row = $nowResult->fetch_assoc()) {
        $now = $row['current_time'];
    }
} catch (Exception $e) {
    // ignore
}
if (!$now) {
    // fallback to PHP time if MySQL fails
    $now = date('Y-m-d H:i:s');
}

// 5. Fetch vouchers linked to the user
$sql = "
    SELECT v.*, uv.isUsed
    FROM vouchers v
    LEFT JOIN user_vouchers uv 
      ON v.voucher_id = uv.voucher_id AND uv.user_id = ?
    ORDER BY v.start_date DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$active = [];
$past = [];
$upcoming = [];

while ($voucher = $result->fetch_assoc()) {
    if (!empty($voucher['isUsed']) && $voucher['isUsed'] == 1) {
        $voucher['status'] = "USED";
        $past[] = $voucher;
    } elseif ($voucher['end_date'] < $now) {
        $voucher['status'] = "EXPIRED";
        $past[] = $voucher;
    } elseif ($voucher['start_date'] > $now) {
        $voucher['status'] = "UPCOMING";
        $upcoming[] = $voucher;
    } else {
        $voucher['status'] = "ACTIVE";
        $active[] = $voucher;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Vouchers</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f9f9f9; }
        h2 { margin-top: 30px; color: #333; }
        .voucher-list { display: flex; flex-wrap: wrap; gap: 20px; }
        .voucher {
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            width: 280px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .voucher img { max-width: 100%; border-radius: 8px; margin-bottom: 10px; }
        .voucher h3 { margin: 0; color: #222; }
        .voucher p { margin: 5px 0; font-size: 14px; color: #555; }
        .status { font-weight: bold; margin-top: 8px; }
        .status.ACTIVE { color: green; }
        .status.EXPIRED, .status.USED { color: red; }
        .status.UPCOMING { color: orange; }
    </style>
</head>
<body>
    <h1>My Vouchers</h1>

    <h2>Active Vouchers</h2>
    <div class="voucher-list">
        <?php if (empty($active)) echo "<p>No active vouchers.</p>"; ?>
        <?php foreach ($active as $v): ?>
            <div class="voucher">
                <img src="<?php echo htmlspecialchars($v['voucher_image_url']); ?>" alt="<?php echo htmlspecialchars($v['voucher_name']); ?>">
                <h3><?php echo htmlspecialchars($v['voucher_name']); ?></h3>
                <p><?php echo htmlspecialchars($v['description']); ?></p>
                <p><strong>Discount:</strong> 
                   <?php echo $v['discount_type'] == 'PERCENT' ? $v['discount_value'].'%' : 'RM'.$v['discount_value']; ?>
                </p>
                <p><strong>Min Spend:</strong> RM<?php echo $v['min_subtotal']; ?></p>
                <p><strong>Valid:</strong> <?php echo $v['start_date']; ?> → <?php echo $v['end_date']; ?></p>
                <p><?php echo htmlspecialchars($v['terms_conditions']); ?></p>
                <p class="status <?php echo $v['status']; ?>"><?php echo $v['status']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Upcoming Vouchers</h2>
    <div class="voucher-list">
        <?php if (empty($upcoming)) echo "<p>No upcoming vouchers.</p>"; ?>
        <?php foreach ($upcoming as $v): ?>
            <div class="voucher">
                <img src="<?php echo htmlspecialchars($v['voucher_image_url']); ?>" alt="<?php echo htmlspecialchars($v['voucher_name']); ?>">
                <h3><?php echo htmlspecialchars($v['voucher_name']); ?></h3>
                <p><?php echo htmlspecialchars($v['description']); ?></p>
                <p><strong>Discount:</strong> 
                   <?php echo $v['discount_type'] == 'PERCENT' ? $v['discount_value'].'%' : 'RM'.$v['discount_value']; ?>
                </p>
                <p><strong>Starts:</strong> <?php echo $v['start_date']; ?></p>
                <p><?php echo htmlspecialchars($v['terms_conditions']); ?></p>
                <p class="status <?php echo $v['status']; ?>"><?php echo $v['status']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Past Vouchers</h2>
    <div class="voucher-list">
        <?php if (empty($past)) echo "<p>No past vouchers.</p>"; ?>
        <?php foreach ($past as $v): ?>
            <div class="voucher">
                <img src="<?php echo htmlspecialchars($v['voucher_image_url']); ?>" alt="<?php echo htmlspecialchars($v['voucher_name']); ?>">
                <h3><?php echo htmlspecialchars($v['voucher_name']); ?></h3>
                <p><?php echo htmlspecialchars($v['description']); ?></p>
                <p><strong>Discount:</strong> 
                   <?php echo $v['discount_type'] == 'PERCENT' ? $v['discount_value'].'%' : 'RM'.$v['discount_value']; ?>
                </p>
                <p><strong>Valid:</strong> <?php echo $v['start_date']; ?> → <?php echo $v['end_date']; ?></p>
                <p><?php echo htmlspecialchars($v['terms_conditions']); ?></p>
                <p class="status <?php echo $v['status']; ?>"><?php echo $v['status']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
