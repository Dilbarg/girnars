<?php
// admin.php - simple admin view (basic password protection)
$admin_pass = 'admin123'; // change immediately
if(isset($_GET['p']) && $_GET['p']===$admin_pass){
  // show orders
  $conn = new mysqli('localhost','root','','myshop_db');
  if($conn->connect_error) die("DB Err");
  $res = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
  echo "<!doctype html><html><head><meta charset='utf-8'><title>Orders</title><style>body{font-family:Arial}table{border-collapse:collapse;width:100%}td,th{border:1px solid #ddd;padding:8px}th{background:#222;color:#fff}</style></head><body>";
  echo "<h2>Orders</h2><p><a href='admin.php'>Refresh</a></p>";
  echo "<table><tr><th>ID</th><th>When</th><th>Product</th><th>Customer</th><th>Qty</th><th>Payment</th><th>Status</th><th>Address</th></tr>";
  while($row = $res->fetch_assoc()){
    echo "<tr>";
    echo "<td>{$row['id']}</td>";
    echo "<td>{$row['created_at']}</td>"; 
    echo "<td>{$row['company']} - {$row['product_name']} (₹{$row['price']})</td>";
    echo "<td>{$row['customer_name']}<br/>{$row['customer_email']}</td>";
    echo "<td>{$row['quantity']}</td>";
    echo "<td>{$row['payment_method']}</td>";
    echo "<td>{$row['status']}</td>";
    echo "<td style='max-width:300px'>{$row['address']}</td>";
    echo "</tr>";
  } 
  echo "</table></body></html>";
  $conn->close();
  exit;
} else {
  // ask for password
  echo "<!doctype html><html><body style='font-family:Arial;padding:20px'><h2>Admin Login</h2>";
  echo "<form method='GET'><input type='password' name='p' placeholder='Password'/> <button>Enter</button></form>";
  echo "<p>Use password set in admin.php file.</p></body></html>";
  exit;
}
?>
