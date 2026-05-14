<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP String & Math Lab</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f4f9; display: flex; justify-content: center; padding: 50px; }
        .receipt { background: white; width: 350px; padding: 20px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border-top: 8px solid #4CAF50; }
        .header { text-align: center; border-bottom: 2px dashed #eee; padding-bottom: 15px; }
        .section { margin: 20px 0; font-size: 14px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 8px; }
        .total-row { border-top: 2px solid #333; padding-top: 10px; margin-top: 10px; font-weight: bold; font-size: 18px; color: #2e7d32; }
        .label { color: #666; font-weight: 600; }
        .badge { background: #e8f5e9; color: #2e7d32; padding: 2px 8px; border-radius: 10px; font-size: 11px; }
    </style>
</head>
<body>

<?php
// --- data input ---
$rawCustomer = "   mArK sPeNcEr   ";
$cardNum = "1234567890123456";
$priceA = 1250.00;
$priceB = 450.75;
$qtyA = 2;
$discountPercent = 15; // 15% discount

// --- code your string manipulation answers here ---
$trimmedName = trim($rawCustomer);
$cleanName = ucwords(strtolower($trimmedName));
$maskedCard = "**** " . substr($cardNum, -4);

// --- code your math operations here ---
$subtotal = ($priceA * $qtyA) + $priceB;
$discountAmount = $subtotal * ($discountPercent / 100);
$finalTotal = $subtotal - $discountAmount;

?>

<div class="receipt">
    <div class="header">
        <h3>OFFICIAL RECEIPT</h3>
        <p><span class="badge">Order ID: MAR-7416</span></p>
    </div>

    <div class="section">
        <div class="row"><span class="label">Customer:</span> <span><?php echo $cleanName; ?></span></div>
        <div class="row"><span class="label">Payment:</span> <span>Visa <?php echo $maskedCard; ?></span></div>
    </div>

    <div class="section">
        <div class="row"><span>Item A (x<?php echo $qtyA; ?>)</span> <span>$<?php echo number_format($priceA * $qtyA, 2); ?></span></div>
        <div class="row"><span>Item B (x1)</span> <span>$<?php echo number_format($priceB, 2); ?></span></div>
    </div>

    <div class="section">
        <div class="row"><span class="label">Subtotal:</span> <span>$<?php echo number_format($subtotal, 2); ?></span></div>
        <div class="row"><span class="label">Discount (<?php echo $discountPercent; ?>%):</span> <span style="color:red;">-$<?php echo number_format($discountAmount, 2); ?></span></div>
        
        <div class="row total-row">
            <span>Total Paid:</span>
            <span>$<?php echo number_format($finalTotal, 2); ?></span>
        </div>
    </div>

    <div style="text-align:center; font-size: 10px; color: #aaa; margin-top: 20px;">
        Thank you for shopping with us!
    </div>
</div>

</body>
</html>