<?php
/* 
Active vouchers → isUsed = 0 AND date range valid. 
Upcoming vouchers → start_date > NOW(). 
Past vouchers → either isUsed = 1 OR end_date < NOW() 
*/
session_start();
include(__DIR__ . '/../../connect_db.php');
date_default_timezone_set('Asia/Kuala_Lumpur');

if (!isset($_SESSION['user_id'])) {
    echo "<p>You must be logged in to see vouchers.</p>";
    exit;
}

$user_id = $_SESSION['user_id'];
$type = $_GET['type'] ?? 'active';

// Get current time
$now = date('Y-m-d H:i:s');

// Fetch vouchers joined with user_vouchers
$sql = "
    SELECT v.voucher_id, v.voucher_name, v.description, v.terms_conditions,
           v.voucher_image_url, v.discount_type, v.discount_value,
           v.min_subtotal, v.start_date, v.end_date,
           uv.isUsed
    FROM vouchers v
    LEFT JOIN user_vouchers uv 
        ON v.voucher_id = uv.voucher_id AND uv.user_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$vouchers = [];
while ($row = $result->fetch_assoc()) {
    $status = "UPCOMING";
    if ($row['isUsed'] == 1) {
        $status = "USED";
    } elseif ($row['end_date'] < $now) {
        $status = "EXPIRED";
    } elseif ($row['start_date'] <= $now && $row['end_date'] >= $now && $row['isUsed'] == 0) {
        $status = "ACTIVE";
    }
    $row['status'] = $status;
    $vouchers[] = $row;
}

// Filter by tab type
$filtered = array_filter($vouchers, function ($v) use ($type) {
    if ($type === "active") return $v['status'] === "ACTIVE";
    if ($type === "upcoming") return $v['status'] === "UPCOMING";
    if ($type === "past") return in_array($v['status'], ["USED", "EXPIRED"]);
    return false;
});
?>

<div class="voucher-list active">
    <?php if (empty($filtered)) : ?>
        <p>No <?php echo ucfirst($type); ?> vouchers.</p>
    <?php else : ?>
        <?php foreach ($filtered as $v): ?>
            <div class="voucher">
                <img src="<?php echo htmlspecialchars($v['voucher_image_url']); ?>" alt="Voucher Image">

                <h3><?php echo htmlspecialchars($v['voucher_name']); ?></h3>
                <p><?php echo htmlspecialchars($v['description']); ?></p>

                <div class="badges">
                    <span class="badge <?php echo $v['status']; ?>"><?php echo $v['status']; ?></span>
                    <span class="badge <?php echo $v['discount_type']; ?>">
                        <?php echo $v['discount_type'] == 'PERCENT' ? 'PERCENTAGE' : 'FIXED'; ?>
                    </span>
                </div>

                <p><strong>Discount:</strong>
                    <?php echo $v['discount_type'] == 'PERCENT'
                        ? $v['discount_value'] . '%'
                        : 'RM' . $v['discount_value']; ?>
                </p>
                <p><strong>Min Spend:</strong> RM<?php echo $v['min_subtotal']; ?></p>
                <p><strong>Duration:</strong> <?php echo $v['start_date']; ?> → <?php echo $v['end_date']; ?></p>
                <p><strong>Terms:</strong> <?php echo htmlspecialchars($v['terms_conditions']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
