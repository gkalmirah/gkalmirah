<?php if (basename($_SERVER['PHP_SELF']) == 'index.php'): ?>
    <footer class="footer-custom p-5 text-left">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                    <div class="footer-logo mb-4">
                        <img src="img/Logo White.png" alt="GK ALMIRAH Logo" class="footer-logo-img">
                    </div>
                    <p class="text-white-50 small pr-4">GK Almirah is a pioneer in premium steel furniture, delivering
                        durability and style since 2021.</p>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-lg-0">
                    <h5 class="heading">Quick Links</h5>
                    <ul class="list-unstyled footer-links">
                        <li><a href="index.php"><i class="fas fa-chevron-right mr-2"></i>Home</a></li>
                        <li><a href="product.php"><i class="fas fa-chevron-right mr-2"></i>Products</a></li>
                        <li><a href="track-order.php"><i class="fas fa-chevron-right mr-2"></i>Track Order</a></li>
                        <li><a href="delivery-check.php"><i class="fas fa-chevron-right mr-2"></i>Delivery areas</a></li>
                        <li><a href="about-us.php"><i class="fas fa-chevron-right mr-2"></i>About Us</a></li>
                        <li><a href="contact-us.php"><i class="fas fa-chevron-right mr-2"></i>Contact Us</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-lg-0 text-white">
                    <h5 class="heading">Contact Us</h5>
                    <div class="contact-info">
                        <p class="mb-2"><i class="fas fa-envelope mr-2 text-gold"></i> <a
                                href="mailto:contact@gkalmirah.com" class="text-white">contact@gkalmirah.com</a></p>
                        <p class="mb-2"><i class="fas fa-phone-alt mr-2 text-gold"></i> <a href="tel:7388418225"
                                class="text-white">7388418225</a></p>
                        <?php
                        $wa_footer_msg = urlencode("Hello G.K Almirah Team,\n\nI am visiting your website and would like to inquire about your products.\n\nThank you.");
                        ?>
                        <p class="mb-0"><i class="fab fa-whatsapp mr-2 text-gold"></i> <a
                                href="https://wa.me/9682021084?text=<?php echo $wa_footer_msg; ?>"
                                class="text-white">9682021084</a></p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <h5 class="heading">Our Presence</h5>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <div class="social-logos">
                            <img src="img/makeinindia.jpeg" alt="Make in India" class="social-logo-v3">
                            <img src="img/msme.png" alt="MSME" class="social-logo-v3">
                            <img src="img/flagindia.jpg" alt="India Flag" class="social-logo-v3">
                        </div>
                    </div>
                    <h6 class="text-white-50 small mb-2 uppercase">Also available on</h6>
                    <div class="d-flex align-items-center">
                        <img src="img/gemportal.png" alt="GEM" class="avail-logo mr-3">
                        <img src="img/indiamart.jpeg" alt="IndiaMart" class="avail-logo">
                    </div>
                    <div class="social-icons-v3 mt-4">
                        <a href="https://www.facebook.com/gkalmirah/"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/gkalmirah/"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.linkedin.com/company/gk-almirah/"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom mt-5 pt-4 text-center border-top border-secondary">
            <p class="mb-0 text-white-50 small">&copy; 2024 G.K. Almirah. Crafted for excellence.</p>
        </div>
    </footer>
<?php endif; ?>

<!-- AOS Animation Library JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({
        duration: 800,
        easing: 'ease-in-out',
        once: true,
        offset: 100
    });
</script>
</body>

</html>