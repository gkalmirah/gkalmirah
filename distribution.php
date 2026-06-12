<?php
session_start();
include('include/header.php');

// Handle Form Submission
if (isset($_POST['submit_application'])) {
    $full_name = mysqli_real_escape_string($con, trim($_POST['full_name']));
    $company_name = mysqli_real_escape_string($con, trim($_POST['company_name']));
    $phone = mysqli_real_escape_string($con, trim($_POST['phone']));
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $state = mysqli_real_escape_string($con, trim($_POST['state']));
    $city = mysqli_real_escape_string($con, trim($_POST['city']));
    $business_type = mysqli_real_escape_string($con, trim($_POST['business_type']));
    $experience = mysqli_real_escape_string($con, trim($_POST['experience']));
    $investment_capacity = mysqli_real_escape_string($con, trim($_POST['investment_capacity']));
    $message = mysqli_real_escape_string($con, trim($_POST['message']));

    if (empty($full_name) || empty($company_name) || empty($phone) || empty($email) || empty($state) || empty($city)) {
        header('Location: distribution.php?error=empty');
        exit();
    }

    $query = "INSERT INTO distributor_inquiries (full_name, company_name, phone, email, state, city, business_type, experience, investment_capacity, message, status) 
              VALUES ('$full_name', '$company_name', '$phone', '$email', '$state', '$city', '$business_type', '$experience', '$investment_capacity', '$message', 'New')";
    
    if (mysqli_query($con, $query)) {
        header('Location: distribution.php?success=1');
        exit();
    } else {
        header('Location: distribution.php?error=db');
        exit();
    }
}
?>

<!-- Inject SEO Title and Meta Description -->
<script>
    document.title = "Become a GK Almirah Distributor | Business Opportunity";
    
    var metaDesc = document.createElement('meta');
    metaDesc.name = "description";
    metaDesc.content = "Partner with GK Almirah, India's trusted steel furniture and locker manufacturer. Apply for dealership/distributorship and grow your business with high profit margins.";
    document.getElementsByTagName('head')[0].appendChild(metaDesc);
</script>

<style>
    /* Styling System variables */
    :root {
        --brand-gold: #D4AF37;
        --brand-gold-dark: #b89626;
        --brand-dark: #0f172a;
        --brand-charcoal: #1e293b;
        --brand-light: #f8fafc;
        --brand-border: #e2e8f0;
        --brand-text: #334155;
    }

    /* Common utility classes */
    .section-padding {
        padding: 80px 0;
    }
    .text-gold {
        color: var(--brand-gold) !important;
    }
    .bg-dark-brand {
        background: linear-gradient(135deg, var(--brand-dark) 0%, var(--brand-charcoal) 100%);
        color: #fff;
    }
    .bg-light-brand {
        background-color: var(--brand-light);
    }
    .btn-gold {
        background-color: var(--brand-gold);
        color: var(--brand-dark);
        font-weight: 600;
        border: 2px solid var(--brand-gold);
        transition: all 0.3s ease;
    }
    .btn-gold:hover {
        background-color: var(--brand-gold-dark);
        border-color: var(--brand-gold-dark);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.4);
    }
    .btn-outline-gold {
        background-color: transparent;
        color: var(--brand-gold);
        font-weight: 600;
        border: 2px solid var(--brand-gold);
        transition: all 0.3s ease;
    }
    .btn-outline-gold:hover {
        background-color: var(--brand-gold);
        color: var(--brand-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2);
    }
    
    /* Hero section */
    .dist-hero {
        padding: 120px 0 100px 0;
        position: relative;
        overflow: hidden;
    }
    .dist-hero::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: radial-gradient(circle at 80% 20%, rgba(212, 175, 55, 0.15) 0%, transparent 50%);
        z-index: 1;
    }
    .dist-hero .container {
        position: relative;
        z-index: 2;
    }
    .dist-hero h1 {
        font-family: 'Playfair Display', serif;
        font-size: 3.5rem;
        font-weight: 700;
        line-height: 1.2;
    }
    .dist-hero p {
        font-size: 1.25rem;
        color: #cbd5e1;
        max-width: 650px;
        font-weight: 300;
    }

    /* Card styling */
    .benefit-card {
        background: #fff;
        border: 1px solid var(--brand-border);
        border-radius: 12px;
        padding: 30px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    .benefit-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px -5px rgba(0,0,0,0.1);
        border-color: var(--brand-gold);
    }
    .benefit-icon {
        font-size: 2.5rem;
        color: var(--brand-gold);
        margin-bottom: 20px;
    }
    .benefit-card h5 {
        font-weight: 700;
        color: var(--brand-dark);
        margin-bottom: 12px;
    }
    .benefit-card p {
        font-size: 0.95rem;
        color: var(--brand-text);
        line-height: 1.6;
        margin-bottom: 0;
    }

    /* Stats Section */
    .stat-card {
        text-align: center;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 25px;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: var(--brand-gold);
    }
    .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--brand-gold);
        margin-bottom: 5px;
    }
    .stat-label {
        font-size: 0.95rem;
        color: #cbd5e1;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0;
    }

    /* Requirements Check list */
    .checklist-item {
        display: flex;
        align-items: flex-start;
        margin-bottom: 20px;
    }
    .checklist-icon {
        background-color: rgba(212, 175, 55, 0.1);
        color: var(--brand-gold);
        border-radius: 50%;
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        flex-shrink: 0;
    }
    .checklist-content h6 {
        font-weight: 700;
        color: var(--brand-dark);
        margin-bottom: 4px;
    }
    .checklist-content p {
        font-size: 0.9rem;
        color: var(--brand-text);
        margin-bottom: 0;
    }

    /* Form Container */
    .inquiry-form-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid var(--brand-border);
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        padding: 40px;
    }
    .inquiry-form-card h4 {
        font-weight: 700;
        color: var(--brand-dark);
    }
    .form-control {
        border-radius: 8px;
        padding: 12px 15px;
        font-size: 0.95rem;
        border: 1px solid var(--brand-border);
        transition: all 0.3s ease;
    }
    .form-control:focus {
        border-color: var(--brand-gold);
        box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
    }

    /* FAQ accordion styling */
    .faq-accordion .card {
        border: 1px solid var(--brand-border);
        border-radius: 8px !important;
        margin-bottom: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .faq-header {
        background-color: #fff;
        border-bottom: none;
        padding: 0;
    }
    .faq-btn {
        width: 100%;
        text-align: left;
        padding: 20px;
        font-weight: 600;
        color: var(--brand-dark);
        background: none;
        border: none;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
    }
    .faq-btn:focus, .faq-btn:hover {
        color: var(--brand-gold-dark);
        text-decoration: none;
        background-color: var(--brand-light);
    }
    .faq-btn::after {
        content: '\f078';
        font-family: 'Font Awesome 5 Free';
        font-weight: 900;
        font-size: 0.9rem;
        color: var(--brand-gold);
        transition: transform 0.3s ease;
    }
    .faq-btn:not(.collapsed)::after {
        transform: rotate(180deg);
    }
    .faq-body {
        padding: 20px;
        font-size: 0.95rem;
        color: var(--brand-text);
        line-height: 1.6;
        background-color: #fff;
        border-top: 1px solid var(--brand-border);
    }

    /* Contact details */
    .contact-card {
        background: #fff;
        border: 1px solid var(--brand-border);
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.03);
    }
    .contact-info-row {
        display: flex;
        align-items: flex-start;
        margin-bottom: 22px;
    }
    .contact-info-row:last-child {
        margin-bottom: 0;
    }
    .contact-info-icon {
        color: var(--brand-gold);
        font-size: 1.25rem;
        margin-right: 15px;
        margin-top: 4px;
    }
    .contact-info-text strong {
        display: block;
        color: var(--brand-dark);
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 3px;
    }
    .contact-info-text p, .contact-info-text a {
        margin-bottom: 0;
        color: var(--brand-text);
        font-size: 0.95rem;
    }
    .contact-info-text a:hover {
        color: var(--brand-gold);
        text-decoration: none;
    }

    @media (max-width: 768px) {
        .dist-hero h1 {
            font-size: 2.25rem;
        }
        .inquiry-form-card {
            padding: 25px;
        }
    }
</style>

<!-- Hero Section -->
<section class="dist-hero bg-dark-brand">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7 col-md-12">
                <span class="badge badge-warning px-3 py-2 text-dark font-weight-bold mb-3" style="background-color: var(--brand-gold);"><i class="fas fa-handshake mr-1"></i> PARTNERSHIP OPPORTUNITY</span>
                <h1>Become an Authorized GK Almirah Distributor</h1>
                <p class="mt-3">Partner with one of India's trusted steel furniture and locker manufacturers. Expand your business with premium products, competitive margins, and dedicated support.</p>
                <div class="mt-4">
                    <a href="#inquiry-form-section" class="btn btn-gold btn-lg px-4 py-3 mr-3"><i class="fas fa-file-signature mr-2"></i> Apply for Distributorship</a>
                    <?php 
                    $whatsapp_number = '9682021084';
                    $wa_dist_msg = urlencode("Hello G.K Almirah Team,\n\nI am interested in becoming a distributor for G.K Almirah products. Could you please share more details regarding the process and requirements?\n\nThank you.");
                    ?>
                    <a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=<?php echo $wa_dist_msg; ?>" target="_blank" class="btn btn-outline-gold btn-lg px-4 py-3"><i class="fab fa-whatsapp mr-2"></i> WhatsApp Inquiry</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats counters Section -->
<section class="py-5" style="background-color: #111827;">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-card">
                    <div class="stat-number">500+</div>
                    <div class="stat-label">Dealers Network</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-4 mb-md-0">
                <div class="stat-card">
                    <div class="stat-number">1000+</div>
                    <div class="stat-label">Products Delivered</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-number">PAN India</div>
                    <div class="stat-label">Distribution</div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card">
                    <div class="stat-number">10+ Years</div>
                    <div class="stat-label">Industry Experience</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Become a Distributor -->
<section class="section-padding bg-light-brand">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="font-weight-bold text-dark">Why Partner with GK Almirah?</h2>
            <p class="text-muted" style="max-width: 600px; margin: 0 auto;">Join hands with a industry leader and leverage our robust brand value and quality manufacturing capabilities to scale your business.</p>
        </div>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="benefit-card">
                    <div class="benefit-icon"><i class="fad fa-percentage"></i></div>
                    <h5>High Profit Margins</h5>
                    <p>Earn attractive and industry-leading returns on every sale with our highly optimized distributor pricing model.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="benefit-card">
                    <div class="benefit-icon"><i class="fad fa-medal"></i></div>
                    <h5>Trusted Brand</h5>
                    <p>Leverage our decade-long reputation for premium quality, unmatched durability, and absolute security.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="benefit-card">
                    <div class="benefit-icon"><i class="fad fa-bullhorn"></i></div>
                    <h5>Marketing Support</h5>
                    <p>Access high-quality marketing collaterals, store banners, brochures, and local digital advertising assistance.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="benefit-card">
                    <div class="benefit-icon"><i class="fad fa-chalkboard-teacher"></i></div>
                    <h5>Product Training</h5>
                    <p>Get comprehensive training for your team on product technical specifications, key features, and selling points.</p>
                </div>
            </div>
            <div class="col-md-4 mb-4 mb-md-0">
                <div class="benefit-card">
                    <div class="benefit-icon"><i class="fad fa-shipping-fast"></i></div>
                    <h5>Fast Delivery Network</h5>
                    <p>Rely on our optimized distribution and logistics network for quick, seamless order execution and stock replenishment.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="benefit-card">
                    <div class="benefit-icon"><i class="fad fa-user-headset"></i></div>
                    <h5>Dedicated Support</h5>
                    <p>Resolve queries quickly through your assigned relationship manager, dedicated exclusively to business operations.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Requirements Section -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <h2 class="font-weight-bold text-dark mb-4">Distributor Eligibility & Requirements</h2>
                <p class="text-muted mb-4">We seek partners who share our commitment to quality, customer trust, and long-term brand growth. Review our requirements to qualify for distributorship.</p>
                
                <div class="checklist-item">
                    <div class="checklist-icon"><i class="fas fa-check"></i></div>
                    <div class="checklist-content">
                        <h6>Business Experience Preferred</h6>
                        <p>Prior experience in wholesale, distribution, retail networks, or hardware/furniture segments is highly valued.</p>
                    </div>
                </div>
                <div class="checklist-item">
                    <div class="checklist-icon"><i class="fas fa-check"></i></div>
                    <div class="checklist-content">
                        <h6>Retail or Wholesale Network</h6>
                        <p>Established relationships with local sub-dealers, commercial contractors, or retail shops in your region.</p>
                    </div>
                </div>
                <div class="checklist-item">
                    <div class="checklist-icon"><i class="fas fa-check"></i></div>
                    <div class="checklist-content">
                        <h6>Storage Space Available</h6>
                        <p>Adequate warehousing capability (minimum 500–1000 sq. ft.) to stock and securely handle steel almirahs and furniture items.</p>
                    </div>
                </div>
                <div class="checklist-item">
                    <div class="checklist-icon"><i class="fas fa-check"></i></div>
                    <div class="checklist-content">
                        <h6>Investment Capacity</h6>
                        <p>Financial liquidity to maintain optimal stock levels and comfortably absorb operational credit cycles.</p>
                    </div>
                </div>
                <div class="checklist-item">
                    <div class="checklist-icon"><i class="fas fa-check"></i></div>
                    <div class="checklist-content">
                        <h6>Commitment to Brand Growth</h6>
                        <p>Active interest in building brand visibility, running local promotions, and expanding local coverage.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="img/distributor_banner.jpg" class="img-fluid rounded shadow-sm" style="max-height: 480px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&q=80&w=800'">
            </div>
        </div>
    </div>
</section>

<!-- Inquiry Form Section -->
<section id="inquiry-form-section" class="section-padding bg-light-brand">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="inquiry-form-card">
                    <div class="text-center mb-5">
                        <h4>Apply for GK Almirah Distributorship</h4>
                        <p class="text-muted">Fill out the form below with your company profile and information. Our business development team will review it and get back to you within 48 business hours.</p>
                    </div>
                    
                    <form method="post" action="distribution.php">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" placeholder="Enter your full name" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" class="form-control" placeholder="Enter company / firm name" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Mobile Number <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile number" pattern="[0-9]{10}" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="e.g. contact@yourcompany.com" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">State <span class="text-danger">*</span></label>
                                <input type="text" name="state" class="form-control" placeholder="State name" required>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">City / District <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control" placeholder="City or District name" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Business Type</label>
                                <select name="business_type" class="form-control">
                                    <option value="Retailer">Retailer / Dealer</option>
                                    <option value="Wholesaler">Wholesaler / Distributor</option>
                                    <option value="Trader">Trader / Sourcing Agent</option>
                                    <option value="New Entrepreneur">New Entrepreneur</option>
                                    <option value="Other">Other Business</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group">
                                <label class="font-weight-bold">Years of Business Experience</label>
                                <select name="experience" class="form-control">
                                    <option value="No Experience">No Experience</option>
                                    <option value="Less than 1 Year">Less than 1 Year</option>
                                    <option value="1-3 Years">1 to 3 Years</option>
                                    <option value="3-5 Years">3 to 5 Years</option>
                                    <option value="More than 5 Years">More than 5 Years</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Proposed Investment Capacity <span class="text-danger">*</span></label>
                            <select name="investment_capacity" class="form-control" required>
                                <option value="Under 5 Lakhs">Under ₹5 Lakhs</option>
                                <option value="5-10 Lakhs">₹5 Lakhs - ₹10 Lakhs</option>
                                <option value="10-20 Lakhs">₹10 Lakhs - ₹20 Lakhs</option>
                                <option value="Above 20 Lakhs">Above ₹20 Lakhs</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Brief Message / Business Proposal</label>
                            <textarea name="message" class="form-control" rows="4" placeholder="Tell us more about your target territory, current operations, and distribution plans..."></textarea>
                        </div>
                        
                        <div class="mt-4 text-center">
                            <button type="submit" name="submit_application" class="btn btn-gold btn-lg px-5 py-3 shadow mr-3"><i class="fas fa-paper-plane mr-2"></i> Submit Application</button>
                            <a href="https://wa.me/<?php echo $whatsapp_number; ?>?text=<?php echo $wa_dist_msg; ?>" target="_blank" class="btn btn-success btn-lg px-5 py-3 shadow"><i class="fab fa-whatsapp mr-2"></i> Chat with Us</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="section-padding bg-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="font-weight-bold text-dark">Frequently Asked Questions</h2>
            <p class="text-muted" style="max-width: 600px; margin: 0 auto;">Got questions? We have compiled the answers to the most common questions regarding partnership opportunities.</p>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="faq-accordion accordion" id="faqAccordion">
                    
                    <div class="card">
                        <div class="faq-header" id="headingOne">
                            <h2 class="mb-0">
                                <button class="faq-btn" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    How do I become an authorized distributor?
                                </button>
                            </h2>
                        </div>
                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#faqAccordion">
                            <div class="faq-body">
                                The process starts by submitting the application inquiry form on this page. Our team reviews your location, investment capacity, and business profile. If there is a fit, we will arrange a call or local visit to finalize agreement terms, territory coverage, and initial inventory orders.
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="faq-header" id="headingTwo">
                            <h2 class="mb-0">
                                <button class="faq-btn collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    What is the required investment amount?
                                </button>
                            </h2>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#faqAccordion">
                            <div class="faq-body">
                                The initial investment depends on your territory size, store size, and required starter stock. Generally, investment ranges from ₹5 Lakhs to ₹20 Lakhs, which covers the costs of stock inventory, security deposits, showroom display setups, and local business development cycles.
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="faq-header" id="headingThree">
                            <h2 class="mb-0">
                                <button class="faq-btn collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Which geographical locations are available?
                                </button>
                            </h2>
                        </div>
                        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#faqAccordion">
                            <div class="faq-body">
                                We are actively expanding our footprint PAN India, with particular focus on underserved tier-2 and tier-3 cities. We guarantee exclusive territory rights within a designated radius so that our distributors do not experience direct competition.
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="faq-header" id="headingFour">
                            <h2 class="mb-0">
                                <button class="faq-btn collapsed" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    How long does the approval process take?
                                </button>
                            </h2>
                        </div>
                        <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#faqAccordion">
                            <div class="faq-body">
                                After submitting your inquiry form, our relationship manager will contact you within 48 business hours. The complete process, including profile review, territory checks, documentation, deposit, and initial order dispatch, generally takes 7 to 15 business days.
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="faq-header" id="headingFive">
                            <h2 class="mb-0">
                                <button class="faq-btn collapsed" type="button" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                    What support will GK Almirah provide?
                                </button>
                            </h2>
                        </div>
                        <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#faqAccordion">
                            <div class="faq-body">
                                We provide extensive support including: marketing materials, glow-sign board support, catalog and product spec brochures, sales team training, leads sharing from local digital queries, relationship managers to optimize orders, and full warranty support for replacement components.
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Office details at bottom -->
<section class="section-padding bg-light-brand" style="border-top: 1px solid var(--brand-border);">
    <div class="container">
        <div class="row align-items-stretch">
            <div class="col-lg-5 mb-5 mb-lg-0">
                <h3 class="font-weight-bold text-dark mb-4">Contact Our Corporate Office</h3>
                <p class="text-muted mb-4">Have specific business inquiries or wish to visit our manufacturing facility? Get in touch using our office details below.</p>
                
                <div class="contact-card">
                    <div class="contact-info-row">
                        <div class="contact-info-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="contact-info-text">
                            <strong>Registered Address</strong>
                            <p>Gandhi Nagar, Gyanodya Nagar, Saurikh Kannauj, Uttar Pradesh, India - 209728</p>
                        </div>
                    </div>
                    
                    <div class="contact-info-row">
                        <div class="contact-info-icon"><i class="fas fa-phone-alt"></i></div>
                        <div class="contact-info-text">
                            <strong>Telephone / Mobile</strong>
                            <p><a href="tel:+919682021084">+91 9682021084</a></p>
                        </div>
                    </div>
                    
                    <div class="contact-info-row">
                        <div class="contact-info-icon"><i class="fas fa-envelope"></i></div>
                        <div class="contact-info-text">
                            <strong>Official Email</strong>
                            <p><a href="mailto:contact@gkalmirah.com">contact@gkalmirah.com</a></p>
                        </div>
                    </div>
                    
                    <div class="contact-info-row">
                        <div class="contact-info-icon"><i class="fab fa-whatsapp"></i></div>
                        <div class="contact-info-text">
                            <strong>WhatsApp Chat</strong>
                            <p><a href="https://wa.me/9682021084" target="_blank">+91 9682021084</a></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="h-100 rounded overflow-hidden shadow-sm" style="min-height: 350px;">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3553.831532218271!2d79.48899787522888!3d27.03548887657425!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x399e1b6971ae513f%3A0xdc160e91b4992703!2sgk%20almirah!5e0!3m2!1sen!2sin!4v1721157108155!5m2!1sen!2sin"
                        width="100%" height="100%" frameborder="0" style="border:0; min-height: 380px;" allowfullscreen="" aria-hidden="false"
                        tabindex="0"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SweetAlert handler for submission response messages -->
<?php if (isset($_GET['success'])): ?>
<script>
    $(document).ready(function() {
        swal({
            title: "Application Submitted!",
            text: "Your distributorship application has been saved. Our sales team will get in touch with you within 48 business hours.",
            icon: "success",
            button: "Close"
        });
    });
</script>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<script>
    $(document).ready(function() {
        var errorMsg = "An error occurred while saving your inquiry. Please try again.";
        <?php if ($_GET['error'] == 'empty'): ?>
        errorMsg = "Please fill out all the required fields (*).";
        <?php endif; ?>
        swal({
            title: "Submission Failed",
            text: errorMsg,
            icon: "error",
            button: "Try Again"
        });
    });
</script>
<?php endif; ?>

<?php include('include/footer.php'); ?>