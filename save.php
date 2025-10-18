<?php
// save.php
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain; charset=UTF-8");

$raw = file_get_contents("php://input");
if(!$raw){
  echo "No data received";
  exit;
}

$data = json_decode($raw, true);
if(!$data){
  echo "Invalid JSON";
  exit;
}

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "myshop_db";
$conn = new mysqli($host, $user, $pass, $dbname);
if($conn->connect_error){
  echo "DB connect error: " . $conn->connect_error;
  exit;
}

/* Contact message */
if(isset($data['contact']) && $data['contact'] === true){
  $name = $conn->real_escape_string($data['name']);
  $email = $conn->real_escape_string($data['email']);
  $message = $conn->real_escape_string($data['message']);

  $sql = "INSERT INTO contacts (name, email, message) VALUES ('$name','$email','$message')";
  if($conn->query($sql)){
    // send email to admin
    $to = "dilbargurjar1601@gmail.com, shivshaktienterprises1601@gmail.com";
    $subject = "Website Contact: $name";
    $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
    @mail($to, $subject, $body);
    echo "Thank you! Your message has been received.";
  } else {
    echo "DB error: " . $conn->error;
  }
  $conn->close();
  exit;
}

/* Order submission */
$company = $conn->real_escape_string($data['company'] ?? '');
$product_name = $conn->real_escape_string($data['product_name'] ?? '');
$price = (float)($data['price'] ?? 0);
$size = $conn->real_escape_string($data['size'] ?? '');
$finish = $conn->real_escape_string($data['finish'] ?? '');
$quantity = (int)($data['quantity'] ?? 1);
$customer_name = $conn->real_escape_string($data['customer_name'] ?? '');
$customer_email = $conn->real_escape_string($data['customer_email'] ?? '');
$address = $conn->real_escape_string($data['address'] ?? '');
$payment = $conn->real_escape_string($data['payment_method'] ?? '');

$sql = "INSERT INTO orders (company, product_name, price, size, finish, quantity, customer_name, customer_email, address, payment_method)
VALUES ('$company','$product_name','$price','$size','$finish','$quantity','$customer_name','$customer_email','$address','$payment')";

if($conn->query($sql)){
  // ----------------------------------------
  // 📧 Email Notification to admin
  // ----------------------------------------
  $to = "dilbargurjar1601@gmail.com, shivshaktienterprises1601@gmail.com";
  $subject = "🛒 New Order: $product_name";
  $body = "New order received:\n\n"
        . "Product: $company - $product_name\n"
        . "Size: $size\nFinish: $finish\nQty: $quantity\nPrice: ₹$price\n\n"
        . "Customer: $customer_name\nEmail: $customer_email\n"
        . "Address:\n$address\n\nPayment: $payment\n\n-- WIN Edge Bands Website";
  @mail($to, $subject, $body);

  // ----------------------------------------
  // 📱 SMS Notification to admin using Fast2SMS
  // ----------------------------------------
  $apiKey = "YOUR_FAST2SMS_API_KEY"; // ← यहाँ अपनी API Key डाल
  $mobile = "9928500892";
  $sms = "New order from $customer_name for $product_name (Qty: $quantity)";
  $sms_url = "https://www.fast2sms.com/dev/bulkV2?authorization=$apiKey&sender_id=TXTIND&message=" . urlencode($sms) . "&language=english&route=v3&numbers=$mobile";
  @file_get_contents($sms_url);

  echo "Order received. We will contact you shortly.";
} else {
  echo "DB error: " . $conn->error;
}

$conn->close();
?>
