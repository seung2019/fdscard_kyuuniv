<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $category = $_POST['category'];
    $amount = $_POST['amount'];
    $card_number = $_POST['card_number'];

    $data = array(
        'age' => $age,
        'gender' => $gender,
        'category' => $category,
        'amount' => $amount,
        'card_number' => $card_number,
    );

    $payload = json_encode($data);

    $ch = curl_init('https://fdscard.duckdns.org/payment.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    $result = curl_exec($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($result === false) {
        // Handle cURL error
        echo "cURL error: " . $curl_error;
        exit;
    }

    // Find and decode the last JSON object in the result
    $json_start_pos = strrpos($result, '{');
        if ($json_start_pos !== false) {
            $json_str = substr($result, $json_start_pos);
            $result_data = json_decode($json_str, true);
        } else {
            echo "Invalid JSON response.";
            exit;
        }
        
        // Ensure headers are set before any output
        if (isset($result_data['result']) && $result_data['result'] === false) {
            header('Location: result.html?prediction=1');
        } else {
            header('Location: result.html?prediction=0');
        }
        exit;
}
?>