<?php 
// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jacky_salon"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Common fields
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';

    $form_type = ''; // Determine form type
    $sql = '';       // SQL query placeholder
    $message = '';   // Initialize message for email

    if (!empty($_POST['service']) && !empty($_POST['date']) && !empty($_POST['time'])) {
        // Appointment form
        $service = $_POST['service'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $form_type = 'appointment';

        $sql = "INSERT INTO submissions (name, phone, email, service, preferred_date, preferred_time, form_type)
                VALUES ('$name', '$phone', '$email', '$service', '$date', '$time', '$form_type')";
    } elseif (!empty($_POST['message'])) {
        // Contact form
        $message = $_POST['message'];
        $form_type = 'contact';

        $sql = "INSERT INTO submissions (name, phone, email, message, form_type)
                VALUES ('$name', '$phone', '$email', '$message', '$form_type')";
    }

    // Execute SQL query
    if (!empty($sql) && $conn->query($sql) === TRUE) {
        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'your_email@gmail.com'; // Your email
            $mail->Password = 'your_email_password'; // Your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipient settings
            $mail->setFrom('your_email@gmail.com', 'Jacky Salon');
            $mail->addAddress($email); // Client email

            // Content
            $mail->isHTML(true);
            if ($form_type == 'appointment') {
                $mail->Subject = 'Appointment Confirmation';
                $mail->Body = "<p>Hello $name,</p>
                               <p>Your appointment at Jacky Salon has been booked successfully.</p>
                               <p>Service: $service</p>
                               <p>Date: $date</p>
                               <p>Time: $time</p>
                               <p>We look forward to serving you!</p>";
            } elseif ($form_type == 'contact') {
                $mail->Subject = 'Contact Form Submission';
                $mail->Body = "<p>You have a new contact form submission:</p>
                               <p>Name: $name</p>
                               <p>Email: $email</p>
                               <p>Phone: $phone</p>
                               <p>Message: $message</p>";
            }

            $mail->send();
            echo 'Form submitted successfully and email sent!';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
