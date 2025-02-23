<?php
// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jacky_salon"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<script>alert('Database connection failed!');</script>");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve common form fields
    $name  = $_POST['name']  ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';

    $form_type = ''; // Stores 'appointment' or 'contact'
    $sql = '';       // SQL query placeholder
    $email_subject = '';
    $email_body = '';

    if (!empty($_POST['service']) && !empty($_POST['date']) && !empty($_POST['time'])) {
        // Appointment Form Submission
        $service = $_POST['service'];
        $date    = $_POST['date'];
        $time    = $_POST['time'];
        $form_type = 'appointment';

        // Insert into database
        $sql = "INSERT INTO submissions (name, phone, email, service, preferred_date, preferred_time, form_type)
                VALUES ('$name', '$phone', '$email', '$service', '$date', '$time', '$form_type')";

        // Email to client
        $email_subject = "Appointment Confirmation - Jacky Salon";
        $email_body = "
            <div style='font-family: Arial, sans-serif; padding: 15px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); background-color: #f8f9fa;'>
                <h2 style='color:rgb(245, 72, 236);'>Appointment Confirmed</h2>
                <p>Hello <strong>$name</strong>,</p>
                <p>Your appointment has been successfully booked.</p>
                <p><strong>Service:</strong> $service</p>
                <p><strong>Date:</strong> $date</p>
                <p><strong>Time:</strong> $time</p>
                <p style='color: #333;'>We look forward to serving you!</p>
                <br>
                <p style='color: #777;'>Best Regards,<br><strong>Jacky Salon Team</strong></p>
            </div>
        ";

        // Email to salon
        $salon_subject = "New Appointment Booking - Jacky Salon";
        $salon_body = "
            <div style='font-family: Arial, sans-serif; padding: 15px; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); background-color: #f8f9fa;'>
                <h2 style='color:rgb(245, 43, 235);'>New Appointment Booking</h2>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Phone:</strong> $phone</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Service:</strong> $service</p>
                <p><strong>Date:</strong> $date</p>
                <p><strong>Time:</strong> $time</p>
            </div>
        ";
    }

    // Execute SQL query
    if (!empty($sql) && $conn->query($sql) === TRUE) {
        // Send email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host       = 'acutis.webhostultima.com'; // SMTP Host
            $mail->SMTPAuth   = true;
            $mail->Username   = 'info@jackysalon.co.ke'; // SMTP Username
            $mail->Password   = 'Jackysalon@0360'; // SMTP Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Fix SSL certificate verification (if needed)
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer'       => false,
                    'verify_peer_name'  => false,
                    'allow_self_signed' => true
                )
            );

            // Sender Info
            $mail->setFrom('info@jackysalon.co.ke', 'Jacky Salon');
            $mail->addReplyTo('info@jackysalon.co.ke', 'Jacky Salon');

            // Send email to the client
            $mail->addAddress($email); 
            $mail->isHTML(true);
            $mail->Subject = $email_subject;
            $mail->Body    = $email_body;
            $mail->send();

            // Send email to the salon
            $mail->clearAddresses(); 
            $mail->addAddress('info@jackysalon.co.ke'); 
            $mail->Subject = $salon_subject;
            $mail->Body    = $salon_body;
            $mail->send();

            // Success Message & Redirect to Home
            echo "<script>
                alert('Form submitted successfully! A confirmation email has been sent.');
                window.location.href = 'index.html'; // Redirects to homepage
            </script>";
        } catch (Exception $e) {
            echo "<script>alert('Message could not be sent. Error: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('Database error: " . $conn->error . "');</script>";
    }
}

// Close database connection
$conn->close();
?>
