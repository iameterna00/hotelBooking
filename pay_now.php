<?php 

require('admin/inc/db_config.php');
require('admin/inc/essentials.php');

date_default_timezone_set("Asia/Kolkata");

session_start();

if(!(isset($_SESSION['login']) && $_SESSION['login']==true)){
  redirect('index.php');
}

if(isset($_POST['pay_now']))
{
  header("Pragma: no-cache");
  header("Cache-Control: no-cache");
  header("Expires: 0");

  $ORDER_ID = 'ORD_'.$_SESSION['uId'].random_int(11111,9999999);    
  $CUST_ID = $_SESSION['uId'];
  $TXN_AMOUNT = $_SESSION['room']['payment'];

  // Insert booking order into database
  $frm_data = filteration($_POST);

  // Prepare the query for inserting booking order
  $query1 = "INSERT INTO `booking_order`(`user_id`, `room_id`, `check_in`, `check_out`, `booking_status`, `order_id`, `trans_amt`) VALUES (?, ?, ?, ?, 'Completed', ?, ?)";
  $values1 = [$CUST_ID, $_SESSION['room']['id'], $frm_data['checkin'], $frm_data['checkout'], $ORDER_ID, $TXN_AMOUNT];
  $types1 = 'iisssi';
  $result1 = insert($query1, $values1, $types1);
  
  if ($result1) {
    $booking_id = mysqli_insert_id($con);

    // Insert booking details into database
    $query2 = "INSERT INTO `booking_details`(`booking_id`, `room_name`, `price`, `total_pay`, `user_name`, `phonenum`, `address`) VALUES (?,?,?,?,?,?,?)";
    $values2 = [$booking_id, $_SESSION['room']['name'], $_SESSION['room']['price'], $TXN_AMOUNT, $frm_data['name'], $frm_data['phonenum'], $frm_data['address']];
    $types2 = 'issssss';
    $result2 = insert($query2, $values2, $types2);

    if ($result2) {
      // Redirect to booking status page with order_id
      redirect('pay_status.php?order=' . $ORDER_ID);
    } else {
      // Handle insertion error
      echo "Error inserting booking details.";
    }
  } else {
    // Handle insertion error
    echo "Error inserting booking order.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Processing</title>
</head>
<body>

  <h1>Please do not refresh this page...</h1>

  <form method="post" action="">
    <input type="hidden" name="pay_now" value="1">
  </form>

  <script type="text/javascript">
    document.forms[0].submit();
  </script>

</body>
</html>
