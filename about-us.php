<?php 
session_start();
include('include/header.php'); ?>

<div class="container-fluid about-page-wrapper">
  <div class="container about-page">
    <div class="row align-items-center">
      <!-- Refined Image Composition -->
      <div class="col-lg-5 mb-5 mb-lg-0">
        <div class="about-image-composition position-relative">
          <div class="main-image-frame animated fadeInLeft">
            <img src="img/about1.png" class="img-fluid main-img" alt="GK Almirah Manufacturing Facility">
          </div>
          <div class="sub-image-frame animated fadeInUp delay-1s">
            <img src="img/about2.png" class="img-fluid sub-img shadow-2xl" alt="Premium Steel Wardrobe Design">
          </div>
          <!-- Subtle Accent Element -->
          <div class="accent-line d-none d-lg-block animated fadeIn"></div>
        </div>
      </div>

      <!-- Corporate Story Section -->
      <div class="col-lg-7">
        <div class="about-content-inner pl-lg-5">
          <header class="section-header mb-5 animated fadeInDown">
            <span class="sub-title text-uppercase font-weight-bold">Established Excellence</span>
            <h1 class="corporate-title mt-2">About Us — <span class="brand-emphasis">GK Almirah</span></h1>
            <div class="title-underline"></div>
          </header>
          
          <div class="corporate-narrative">
            <p class="intro-text animated fadeInUp">
              At GK Almirah, we are committed to delivering durable, stylish, and affordable steel storage solutions designed specifically for the modern Indian household.
            </p>
            
            <p class="description-text animated fadeInUp delay-1s">
              GK Almirah has rapidly evolved into a leading manufacturer of high-quality, powder-coated steel wardrobes in North India. Based in the industrial hub of <strong>Kannauj</strong>, our state-of-the-art manufacturing unit blends traditional craftsmanship with advanced technology.
            </p>

            <p class="description-text animated fadeInUp delay-1s">
              Our presence is strategically expanding across <strong>Uttar Pradesh, Madhya Pradesh, and Uttarakhand</strong>. Through a robust network of distributors and retailers, we ensure that premium quality steel furniture remains accessible and affordable for every middle-class family.
            </p>

            <!-- Commitment Feature Box -->
            <div class="commitment-box my-5 animated fadeInUp delay-2s">
              <h5 class="box-title text-uppercase mb-4">Our Core Focus</h5>
              <div class="row no-gutters">
                <div class="col-md-6">
                  <ul class="feature-list">
                    <li>Superior Quality Standards</li>
                    <li>Affordable Market Pricing</li>
                    <li>Modern Aesthetic Designs</li>
                  </ul>
                </div>
                <div class="col-md-6">
                  <ul class="feature-list">
                    <li>Customer-Centric Service</li>
                    <li>Reliable Storage Systems</li>
                    <li>Continuous Product Innovation</li>
                  </ul>
                </div>
              </div>
            </div>

            <!-- Vision & Mission Segment -->
            <div class="brand-purpose-section mt-5 pt-4 animated fadeInUp delay-3s">
              <div class="purpose-divider mb-5">
                <span class="divider-text">Brand Purpose</span>
              </div>
              <div class="row">
                <div class="col-md-6 mb-4">
                  <div class="purpose-item">
                    <h3 class="purpose-title">Our Vision</h3>
                    <p class="purpose-body">To become India’s most trusted and recognized brand in steel storage solutions by delivering excellence and value in every product.</p>
                  </div>
                </div>
                <div class="col-md-6 mb-4">
                  <div class="purpose-item border-left-highlight">
                    <h3 class="purpose-title">Our Mission</h3>
                    <p class="purpose-body">To provide durable and value-for-money steel wardrobes that meet the evolving needs of Indian households while maintaining manufacturing excellence.</p>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('include/footer.php'); ?>

<style>
  :root {
    --navy-blue: #002147;
    --sand-beige: #d1bea8;
    --sand-dark: #bba68e;
    --off-white: #fdfdfd;
    --text-dark: #2c3e50;
    --text-light: #5a6c7d;
  }

  .about-page-wrapper {
    background-color: var(--sand-beige);
    padding: 80px 0;
  }

  .about-page {
    max-width: 1240px;
    background-color: var(--sand-beige);
    padding: 60px 40px;
    border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
    margin: 0 auto;
  }

  /* Improved Image Composition */
  .about-image-composition {
    max-width: 480px;
    margin: 0 auto;
  }

  .main-image-frame {
    width: 90%;
    z-index: 1;
    position: relative;
  }

  .main-img {
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  }

  .sub-image-frame {
    position: absolute;
    bottom: -40px;
    right: 0;
    width: 65%;
    z-index: 2;
  }

  .sub-img {
    border-radius: 12px;
    border: 8px solid var(--sand-beige);
  }

  .accent-line {
    position: absolute;
    top: 50%;
    left: -20px;
    width: 4px;
    height: 100px;
    background: var(--navy-blue);
    opacity: 0.3;
    transform: translateY(-50%);
  }

  /* Typography Refinement */
  .section-header .sub-title {
    font-size: 0.85rem;
    letter-spacing: 3px;
    color: var(--navy-blue);
    opacity: 0.7;
    display: block;
  }

  .corporate-title {
    font-size: 2.75rem;
    font-weight: 800;
    color: var(--navy-blue);
    letter-spacing: -1px;
    line-height: 1.1;
  }

  .brand-emphasis {
    font-style: italic;
    font-family: 'Georgia', serif;
    font-weight: 700;
  }

  .title-underline {
    width: 80px;
    height: 5px;
    background: var(--navy-blue);
    margin-top: 15px;
  }

  .intro-text {
    font-size: 1.3rem;
    font-weight: 600;
    line-height: 1.6;
    color: var(--navy-blue);
    margin-bottom: 2rem;
  }

  .description-text {
    font-size: 1.05rem;
    line-height: 1.75;
    color: var(--text-dark);
    margin-bottom: 1.25rem;
    font-weight: 500;
  }

  /* Commitment Box Polish */
  .commitment-box {
    background: rgba(255, 255, 255, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.5);
    padding: 35px;
    border-radius: 12px;
  }

  .box-title {
    font-size: 0.9rem;
    font-weight: 800;
    letter-spacing: 2px;
    color: var(--navy-blue);
    border-bottom: 1px solid rgba(0, 33, 71, 0.1);
    padding-bottom: 15px;
  }

  .feature-list {
    list-style: none;
    padding: 0;
  }

  .feature-list li {
    position: relative;
    padding-left: 25px;
    margin-bottom: 12px;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--navy-blue);
  }

  .feature-list li::before {
    content: '';
    position: absolute;
    left: 0;
    top: 7px;
    width: 8px;
    height: 8px;
    background: var(--navy-blue);
    border-radius: 2px;
    transform: rotate(45deg);
  }

  /* Purpose Section Continuity */
  .purpose-divider {
    position: relative;
    text-align: center;
    border-bottom: 1px solid rgba(0, 33, 71, 0.1);
  }

  .divider-text {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--sand-beige);
    padding: 0 20px;
    font-size: 0.8rem;
    text-transform: uppercase;
    font-weight: 800;
    letter-spacing: 2px;
    color: var(--navy-blue);
    opacity: 0.6;
  }

  .purpose-item {
    padding: 10px 0;
  }

  .purpose-title {
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--navy-blue);
    margin-bottom: 15px;
  }

  .purpose-body {
    font-size: 0.95rem;
    line-height: 1.6;
    color: var(--text-light);
  }

  .border-left-highlight {
    border-left: 2px solid rgba(0, 33, 71, 0.15);
    padding-left: 25px;
  }

  /* Responsive Adjustments */
  @media (max-width: 991px) {
    .about-page { padding: 40px 20px; }
    .corporate-title { font-size: 2.25rem; }
    .intro-text { font-size: 1.15rem; }
    .sub-image-frame { bottom: -20px; }
    .border-left-highlight { border-left: none; padding-left: 0; border-top: 1px solid rgba(0, 33, 71, 0.1); padding-top: 20px; }
  }

  @media (max-width: 767px) {
    .about-image-composition { margin-bottom: 60px; }
    .section-header { text-align: center; }
    .title-underline { margin: 15px auto; }
    .intro-text { text-align: center; }
    .commitment-box { padding: 25px; }
  }

  /* Subtle Animations */
  .animated { animation-duration: 0.8s; animation-fill-mode: both; }
  .delay-1s { animation-delay: 0.25s; }
  .delay-2s { animation-delay: 0.5s; }
  .delay-3s { animation-delay: 0.75s; }

  @keyframes fadeInLeft { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
  @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
  @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
  @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

  .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
</style>
