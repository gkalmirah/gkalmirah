<?php
session_start();
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
include('include/header.php');

// Database connection parameters
// Database connection is handled in header.php
if (!isset($con)) {
    include('include/dbcon.php');
}

// Check if form is submitted
if (isset($_POST['submit'])) {
    // Retrieve form data
    $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $message = mysqli_real_escape_string($con, $_POST['message']);

    // Insert data into the database
    $query = "INSERT INTO contact_us (fullname, email, message) VALUES ('$fullname', '$email', '$message')";

    if (mysqli_query($con, $query)) {
        $msg = "<div class='alert alert-success'>Your message has been sent successfully!</div>";
    } else {
        $msg = "<div class='alert alert-danger'>There was an error sending your message. Please try again later.</div>";
    }
}
?>

<div class="jumbotron text-center bg-primary text-white">
    <h1 class="display-4 my-5">Contact Us</h1>
    <p>We're here to help and answer any question you might have. We look forward to hearing from you.</p>
</div>

<div class="container mt-5">
    <?php
    if (isset($msg)) {
        echo "<div class='text-center mb-4'>$msg</div>";
    }
    ?>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <h3 class="text-primary">Our Office</h3>
                <hr class="my-4">
                <p><strong>Address:</strong> Gandhi Nagar, Gyanodya Nagar, Saurikh Kannauj, Uttar Pradesh</p>
                <p><strong>Email:</strong> <a href="mailto:contact@gkalmirah.com"
                        class="text-primary">contact@gkalmirah.com</a></p>
                <p><strong>Call:</strong> <a href="tel:+ 7388418225" class="text-primary">7388418225</a></p>
                <?php
                $wa_contact_msg = urlencode("Hello G.K Almirah Team,\n\nI have a query regarding your products and services. Could you please assist me?\n\nThank you.");
                ?>
                <p><strong>Whatsapp:</strong> <a href="https://wa.me/9682021084?text=<?php echo $wa_contact_msg; ?>"
                        class="text-primary" target="_blank">9682021084</a></p>
            </div>
            <div class="col-md-6">
                <form action="contact-us.php" method="post" class="p-4 shadow rounded bg-light animated fadeInUp">
                    <div class="form-group">
                        <input type="text" name="fullname" placeholder="Full Name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="Email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" rows="5" name="message" placeholder="Message"
                            required></textarea>
                    </div>
                    <div class="form-group text-center mt-4">
                        <input type="submit" name="submit" class="btn btn-primary btn-block" value="Send Message">
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="mt-5">
        <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3553.831532218271!2d79.48899787522888!3d27.03548887657425!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x399e1b6971ae513f%3A0xdc160e91b4992703!2sgk%20almirah!5e0!3m2!1sen!2sin!4v1721157108155!5m2!1sen!2sin"
            width="100%" height="450" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false"
            tabindex="0"></iframe>
    </div>
</div>

<?php include('include/footer.php'); ?>

<style>
    .jumbotron {
        padding: 4rem 2rem;
    }

    .jumbotron h1 {
        font-size: 3rem;
        font-weight: 700;
    }

    .jumbotron p {
        font-size: 1.25rem;
        font-weight: 300;
    }

    .container {
        margin-top: 2rem;
    }

    .text-primary {
        color: #bc987e !important;
    }

    .bg-light {
        background-color: #bc987e !important;
    }

    .bg-primary {
        background-color: #bc987e !important;
    }

    .shadow {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .rounded {
        border-radius: .25rem;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    .animated {
        animation-duration: 1s;
        animation-fill-mode: both;
    }

    .fadeInUp {
        animation-name: fadeInUp;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translate3d(0, 100%, 0);
        }

        to {
            opacity: 1;
            transform: translate3d(0, 0, 0);
        }
    }
</style>