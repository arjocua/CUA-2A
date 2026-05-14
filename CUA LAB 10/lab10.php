<?php
    // =========================================================
    // TODO 1: SECURE DATABASE CONNECTION (XAMPP / MySQL)
    // =========================================================
    // Using 127.0.0.1:3307 because of your specific port setup
    $conn = mysqli_connect("127.0.0.1:3307", "root", "", "pizza_db"); 

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // =========================================================
    // TODO 2: HANDLE POST REQUESTS (ALL CRUD OPERATIONS)
    // =========================================================
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        
        // ---  PIZZA ADMIN ---
        if (isset($_POST['add_pizza'])) {
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $price = $_POST['price'];
            $sql = "INSERT INTO pizzas (name, price) VALUES ('$name', '$price')";
            mysqli_query($conn, $sql);
            header("Location: " . $_SERVER['PHP_SELF']); exit;
        }
        if (isset($_POST['update_pizza'])) {
            $id = $_POST['item_id'];
            $new_price = $_POST['new_price'];
            $sql = "UPDATE pizzas SET price = '$new_price' WHERE id = $id";
            mysqli_query($conn, $sql);
            header("Location: " . $_SERVER['PHP_SELF']); exit;
        }
        if (isset($_POST['delete_pizza'])) {
            $id = $_POST['item_id'];
            mysqli_query($conn, "DELETE FROM pizzas WHERE id = $id");
            header("Location: " . $_SERVER['PHP_SELF']); exit;
        }

        // ---  TOPPINGS ADMIN ---
        if (isset($_POST['add_topping'])) {
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $price = $_POST['price'];
            $sql = "INSERT INTO toppings (name, price) VALUES ('$name', '$price')";
            mysqli_query($conn, $sql);
            header("Location: " . $_SERVER['PHP_SELF']); exit;
        }
        if (isset($_POST['update_topping'])) {
            $id = $_POST['item_id'];
            $new_price = $_POST['new_price'];
            $sql = "UPDATE toppings SET price = '$new_price' WHERE id = $id";
            mysqli_query($conn, $sql);
            header("Location: " . $_SERVER['PHP_SELF']); exit;
        }
        if (isset($_POST['delete_topping'])) {
            $id = $_POST['item_id'];
            mysqli_query($conn, "DELETE FROM toppings WHERE id = $id");
            header("Location: " . $_SERVER['PHP_SELF']); exit;
        }

        // --- 🛒 ORDERING SYSTEM ---
        if (isset($_POST['create_order'])) {
            $customer = mysqli_real_escape_string($conn, $_POST['customer']);
            $pizza_id = $_POST['pizza_id'];
            $qty = $_POST['qty'];
            
            // 1. Fetch Pizza Info
            $p_res = mysqli_query($conn, "SELECT name, price FROM pizzas WHERE id = $pizza_id");
            $p_row = mysqli_fetch_assoc($p_res);
            $pizza_name = $p_row['name'];
            $pizza_price = $p_row['price'];

            // 2. Calculate Toppings
            $topping_total = 0;
            $topping_names = [];
            if (!empty($_POST['topping_ids'])) {
                foreach ($_POST['topping_ids'] as $t_id) {
                    $t_res = mysqli_query($conn, "SELECT name, price FROM toppings WHERE id = $t_id");
                    $t_row = mysqli_fetch_assoc($t_res);
                    $topping_total += $t_row['price'];
                    $topping_names[] = $t_row['name'];
                }
            }
            $toppings_list = implode(", ", $topping_names);

            // 3. Calculate Grand Total
            $grand_total = ($pizza_price + $topping_total) * $qty;

            // 4. INSERT order[cite: 2]
            $sql = "INSERT INTO orders (customer, pizza, toppings, qty, total) 
                    VALUES ('$customer', '$pizza_name', '$toppings_list', $qty, $grand_total)";
            mysqli_query($conn, $sql);
            header("Location: " . $_SERVER['PHP_SELF']); exit;
        }

        // --- 📋 MANAGE ORDERS ---
        if (isset($_POST['update_status'])) {
            $id = $_POST['order_id'];
            mysqli_query($conn, "UPDATE orders SET status = 'Completed' WHERE id = $id");
            header("Location: " . $_SERVER['PHP_SELF']); exit;
        }
        if (isset($_POST['delete_order'])) {
            $id = $_POST['order_id'];
            mysqli_query($conn, "DELETE FROM orders WHERE id = $id");
            header("Location: " . $_SERVER['PHP_SELF']); exit;
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🍕 Pizza Master Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #FF6B6B 0%, #FFA500 100%); min-height: 100vh; padding: 40px 20px; color: #333;}
        .container { max-width: 1200px; margin: 0 auto; }
        header { text-align: center; color: white; margin-bottom: 40px; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
        h1 { font-size: 3em; margin-bottom: 10px; }
        
        .grid-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px;}
        .full-width { grid-column: 1 / -1; }
        @media(max-width: 800px) { .grid-layout { grid-template-columns: 1fr; } }
        
        .card { background: white; border-radius: 15px; padding: 30px; box-shadow: 0 10px 20px rgba(0,0,0,0.2); }
        .card h2 { color: #FF6B6B; border-bottom: 3px solid #FFA500; padding-bottom: 10px; margin-bottom: 20px; }
        
        .form-group { display: flex; gap: 10px; margin-bottom: 20px; align-items: flex-end; }
        .form-stack { display: flex; flex-direction: column; gap: 8px; margin-bottom: 15px; }
        input[type="text"], input[type="number"] { padding: 10px; border: 2px solid #FF6B6B; border-radius: 8px; width: 100%; }
        
        .radio-group, .checkbox-group { display: flex; flex-direction: column; gap: 10px; }
        .selection-item { display: flex; align-items: center; padding: 10px; border-radius: 8px; cursor: pointer; background: #fff5f5;}
        .selection-item:hover { background-color: #ffe8e8; }
        .selection-item input { margin-right: 10px; width: 18px; height: 18px; accent-color: #FF6B6B; }
        .price { color: #FFA500; font-weight: bold; }
        
        button { padding: 10px 15px; background: #FF6B6B; color: white; border: none; border-radius: 8px; font-weight: bold; cursor: pointer; }
        button:hover { background: #FFA500; }
        .btn-large { width: 100%; padding: 15px; font-size: 1.1em; }
        .btn-update { background: #4CAF50; padding: 6px 12px; font-size: 0.9em; }
        .btn-delete { background: #f44336; padding: 6px 12px; font-size: 0.9em; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ecf0f1; }
        th { background-color: #FFF5E6; color: #FF6B6B; }
        .price-input { width: 90px !important; padding: 6px !important; margin-right: 5px; border: 1px solid #ccc !important;}
        
        .badge { padding: 5px 10px; border-radius: 20px; font-size: 0.8em; font-weight: bold; color: white; }
        .bg-pending { background-color: #FFA500; }
        .bg-completed { background-color: #4CAF50; }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>🍕 Pizza Master Dashboard</h1>
            <p>Admin Menu Management & Live Ordering System</p>
        </header>

        <div class="grid-layout">
            
            <div class="card">
                <h2>⚙️ Manage Pizzas</h2>
                <form method="post" class="form-group">
                    <div style="flex: 2;"><input type="text" name="name" placeholder="New Pizza Name" required></div>
                    <div style="flex: 1;"><input type="number" name="price" step="0.01" min="0" placeholder="Price" required></div>
                    <button type="submit" name="add_pizza">Add</button>
                </form>
                <table>
                    <tbody>
                        <?php
                            // TODO 3: Read from 'pizzas' table[cite: 1, 2]
                            $res = mysqli_query($conn, "SELECT * FROM pizzas");
                            while($row = mysqli_fetch_assoc($res)) {
                                echo "<tr>
                                    <td><strong>".htmlspecialchars($row['name'])."</strong></td>
                                    <td>
                                        <form method='post' style='display:flex;'>
                                            <input type='hidden' name='item_id' value='".$row['id']."'>
                                            <input type='number' name='new_price' value='".$row['price']."' step='0.01' class='price-input' required>
                                            <button type='submit' name='update_pizza' class='btn-update'>Save</button>
                                        </form>
                                    </td>
                                    <td>
                                        <form method='post'>
                                            <input type='hidden' name='item_id' value='".$row['id']."'>
                                            <button type='submit' name='delete_pizza' class='btn-delete'>✖</button>
                                        </form>
                                    </td>
                                </tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="card">
                <h2>⚙️ Manage Toppings</h2>
                <form method="post" class="form-group">
                    <div style="flex: 2;"><input type="text" name="name" placeholder="New Topping Name" required></div>
                    <div style="flex: 1;"><input type="number" name="price" step="0.01" min="0" placeholder="Price" required></div>
                    <button type="submit" name="add_topping">Add</button>
                </form>
                <table>
                    <tbody>
                        <?php
                            // TODO 4: Read from 'toppings' table[cite: 1, 2]
                            $res = mysqli_query($conn, "SELECT * FROM toppings");
                            while($row = mysqli_fetch_assoc($res)) {
                                echo "<tr>
                                    <td><strong>".htmlspecialchars($row['name'])."</strong></td>
                                    <td>
                                        <form method='post' style='display:flex;'>
                                            <input type='hidden' name='item_id' value='".$row['id']."'>
                                            <input type='number' name='new_price' value='".$row['price']."' step='0.01' class='price-input' required>
                                            <button type='submit' name='update_topping' class='btn-update'>Save</button>
                                        </form>
                                    </td>
                                    <td>
                                        <form method='post'>
                                            <input type='hidden' name='item_id' value='".$row['id']."'>
                                            <button type='submit' name='delete_topping' class='btn-delete'>✖</button>
                                        </form>
                                    </td>
                                </tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card" style="max-width: 800px; margin: 0 auto 30px auto;">
            <h2>🛒 Place New Order</h2>
            <form method="post">
                <div class="form-stack">
                    <label><strong>Customer Name</strong></label>
                    <input type="text" name="customer" required>
                </div>

                <div class="grid-layout" style="gap: 20px; margin-bottom: 0;">
                    
                    <div class="form-stack">
                        <label><strong>Select Pizza</strong></label>
                        <div class="radio-group">
                            <?php 
                                // TODO 5: Fetch Pizzas from DB[cite: 1, 2]
                                $res = mysqli_query($conn, "SELECT * FROM pizzas");
                                while($row = mysqli_fetch_assoc($res)) {
                                    echo "<label class='selection-item'>
                                            <input type='radio' name='pizza_id' value='".$row['id']."' required>
                                            <span>".htmlspecialchars($row['name'])." <span class='price'>($".$row['price'].")</span></span>
                                          </label>";
                                }
                            ?>
                        </div>
                    </div>

                    <div class="form-stack">
                        <label><strong>Select Toppings</strong></label>
                        <div class="checkbox-group">
                            <?php 
                                // TODO 6: Fetch Toppings from DB[cite: 1, 2]
                                $res = mysqli_query($conn, "SELECT * FROM toppings");
                                while($row = mysqli_fetch_assoc($res)) {
                                    echo "<label class='selection-item'>
                                            <input type='checkbox' name='topping_ids[]' value='".$row['id']."'>
                                            <span>".htmlspecialchars($row['name'])." <span class='price'>(+$".$row['price'].")</span></span>
                                          </label>";
                                }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="form-stack" style="margin-top: 15px;">
                    <label><strong>Quantity</strong></label>
                    <input type="number" name="qty" min="1" value="1" required>
                </div>

                <button type="submit" name="create_order" class="btn-large">🚀 Submit Order</button>
            </form>
        </div>

        <div class="card full-width">
            <h2>📋 Live Kitchen Orders</h2>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th><th>Customer</th><th>Order Details</th><th>Total</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            // TODO 7: Read from 'orders' table[cite: 1, 2]
                            $res = mysqli_query($conn, "SELECT * FROM orders ORDER BY id DESC");
                            while($row = mysqli_fetch_assoc($res)) {
                                $status_class = ($row['status'] === 'Pending') ? 'bg-pending' : 'bg-completed';
                                echo "<tr>
                                    <td>#".$row['id']."</td>
                                    <td>".htmlspecialchars($row['customer'])."</td>
                                    <td><strong>".$row['pizza']."</strong><br><small>".$row['toppings']."</small> (x".$row['qty'].")</td>
                                    <td class='price'>$".number_format($row['total'], 2)."</td>
                                    <td><span class='badge $status_class'>".$row['status']."</span></td>
                                    <td>
                                        <div style='display:flex; gap:5px;'>";
                                            if($row['status'] === 'Pending') {
                                                echo "<form method='post'>
                                                        <input type='hidden' name='order_id' value='".$row['id']."'>
                                                        <button type='submit' name='update_status' class='btn-update'>✔</button>
                                                      </form>";
                                            }
                                            echo "<form method='post'>
                                                    <input type='hidden' name='order_id' value='".$row['id']."'>
                                                    <button type='submit' name='delete_order' class='btn-delete'>✖</button>
                                                  </form>
                                        </div>
                                    </td>
                                </tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
</body>
</html>