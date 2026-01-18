<?php
session_start();  // Start the session
?>

<script>
  document.addEventListener("DOMContentLoaded", function() {
      <?php if (isset($_SESSION['error'])): ?>
          // Keep the dropdown open by adding 'show' class
          alert("<?php echo $_SESSION['error']; ?>");
      <?php endif; ?>
  });
</script>



<!DOCTYPE html>
<html data-bs-theme="light" lang="en-US" dir="ltr">

  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>Conchu | Barangay Information System</title>

    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicons/favicon-16x16.png">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicons/favicon.ico">
    <link rel="manifest" href="assets/img/favicons/manifest.json">
    <meta name="msapplication-TileImage" content="assets/img/favicons/mstile-150x150.png">
    <meta name="theme-color" content="#ffffff">
    <script src="assets/js/config.js"></script>
    <script src="vendors/simplebar/simplebar.min.js"></script>

    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link href="vendors/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
    <link href="vendors/simplebar/simplebar.min.css" rel="stylesheet">
    <link href="assets/css/theme-rtl.min.css" rel="stylesheet" id="style-rtl">
    <link href="assets/css/theme.min.css" rel="stylesheet" id="style-default">
    <link href="assets/css/user-rtl.min.css" rel="stylesheet" id="user-style-rtl">
    <link href="assets/css/user.min.css" rel="stylesheet" id="user-style-default">
    <script>
      var isRTL = JSON.parse(localStorage.getItem('isRTL'));
      if (isRTL) {
        var linkDefault = document.getElementById('style-default');
        var userLinkDefault = document.getElementById('user-style-default');
        linkDefault.setAttribute('disabled', true);
        userLinkDefault.setAttribute('disabled', true);
        document.querySelector('html').setAttribute('dir', 'rtl');
      } else {
        var linkRTL = document.getElementById('style-rtl');
        var userLinkRTL = document.getElementById('user-style-rtl');
        linkRTL.setAttribute('disabled', true);
        userLinkRTL.setAttribute('disabled', true);
      }
    </script>
  </head>

  <body>
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
      <nav class="navbar navbar-standard navbar-expand-lg fixed-top navbar-dark" data-navbar-darken-on-scroll="data-navbar-darken-on-scroll">
        <div class="container"><a class="navbar-brand" href="index.php"><span class="text-white dark__text-white">BRGY. CONCHU</span></a><button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#navbarStandard" aria-controls="navbarStandard" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
          <div class="collapse navbar-collapse scrollbar" id="navbarStandard">
          
            <ul class="navbar-nav ms-auto">
              <li class="nav-item d-flex align-items-center me-2">
                <div class="nav-link theme-switch-toggle fa-icon-wait p-0"><input class="form-check-input ms-0 theme-switch-toggle-input" id="themeControlToggle" type="checkbox" data-theme-control="theme" value="dark"><label class="mb-0 theme-switch-toggle-label theme-switch-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch to light theme"><span class="fas fa-sun"></span></label><label class="mb-0 py-2 theme-switch-toggle-light d-lg-none" for="themeControlToggle"><span>Switch to light theme</span></label><label class="mb-0 theme-switch-toggle-label theme-switch-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch to dark theme"><span class="fas fa-moon"></span></label><label class="mb-0 py-2 theme-switch-toggle-dark d-lg-none" for="themeControlToggle"><span>Switch to dark theme</span></label></div>
              </li>
              <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" id="navbarDropdownLogin" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Login</a>
              <div class="dropdown-menu dropdown-caret dropdown-menu-end dropdown-menu-card" aria-labelledby="navbarDropdownLogin" id="loginDropdown">
                  <div class="card shadow-none navbar-card-login">
                      <div class="card-body fs--1 p-4 fw-normal">
                          <div class="row text-start justify-content-between align-items-center mb-2">
                              <div class="col-auto">
                                  <h5 class="mb-0">Log in</h5>
                              </div>
                          </div>

                          <!-- Display error message if available -->
                          <?php if (isset($_SESSION['error'])): ?>
                              <div class="alert alert-danger" role="alert">
                                  <?php echo $_SESSION['error']; ?>
                              </div>
                              <?php unset($_SESSION['error']); ?>  <!-- Clear the error after displaying -->
                          <?php endif; ?>

                          <form method="POST" action="server.php">
                              <div class="mb-3">
                                  <input class="form-control" type="email" name="email" placeholder="Email address" required />
                              </div>
                              <div class="mb-3">
                                  <input class="form-control" type="password" name="password" placeholder="Password" required />
                              </div>
                              <div class="row flex-between-center">
                                  <div class="col-auto">
                                      <div class="form-check mb-0">
                                          <input class="form-check-input" type="checkbox" id="modal-checkbox" />
                                          <label class="form-check-label mb-0" for="modal-checkbox">Remember me</label>
                                      </div>
                                  </div>
                                  <div class="col-auto">
                                      <a class="fs--1" href="forgot-password.php">Forgot Password?</a>
                                  </div>
                              </div>
                              <div class="mb-3">
                                  <button class="btn btn-primary d-block w-100 mt-3" type="submit" name="login">Log in</button>
                              </div>
                          </form>
                      </div>
                  </div>
              </div>
          </li>
              

              <!-- <li class="nav-item"><a class="nav-link" href="#!" data-bs-toggle="modal" data-bs-target="#exampleModal">Register</a></li> -->
            </ul>
          </div>
        </div>
      </nav>
      <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-body p-4">
              <div class="row text-start justify-content-between align-items-center mb-2">
                <div class="col-auto">
                  <h5 id="modalLabel">Register</h5>
                </div>
                <div class="col-auto">
                  <p class="fs--1 text-600 mb-0">Have an account? <a href="pages/authentication/simple/login.php">Login</a></p>
                </div>
              </div>
              <form>
                <div class="mb-3"><input class="form-control" type="text" autocomplete="on" placeholder="Name" /></div>
                <div class="mb-3"><input class="form-control" type="email" autocomplete="on" placeholder="Email address" /></div>
                <div class="row gx-2">
                  <div class="mb-3 col-sm-6"><input class="form-control" type="password" autocomplete="on" placeholder="Password" /></div>
                  <div class="mb-3 col-sm-6"><input class="form-control" type="password" autocomplete="on" placeholder="Confirm Password" /></div>
                </div>
                <div class="form-check"><input class="form-check-input" type="checkbox" id="modal-register-checkbox" /><label class="form-label" for="modal-register-checkbox">I accept the <a href="#!">terms </a>and <a href="#!">privacy policy</a></label></div>
                <div class="mb-3"><button class="btn btn-primary d-block w-100 mt-3" type="submit" name="submit">Register</button></div>
              </form>
              <div class="position-relative mt-4">
                <hr />
                <div class="divider-content-center">or register with</div>
              </div>
              <div class="row g-2 mt-2">
                <div class="col-sm-6"><a class="btn btn-outline-google-plus btn-sm d-block w-100" href="#"><span class="fab fa-google-plus-g me-2" data-fa-transform="grow-8"></span> google</a></div>
                <div class="col-sm-6"><a class="btn btn-outline-facebook btn-sm d-block w-100" href="#"><span class="fab fa-facebook-square me-2" data-fa-transform="grow-8"></span> facebook</a></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ============================================-->
      <!-- <section> begin ============================-->
      <section class="py-0 overflow-hidden" id="banner" data-bs-theme="light">
        <div class="bg-holder overlay" style="background-image:url(assets/img/generic/bg-1.jpg);background-position: center bottom;"></div>
        <!--/.bg-holder-->
        <div class="container">
          <div class="row flex-center pt-8 pt-lg-10 pb-lg-9 pb-xl-0">
            <div class="col-md-11 col-lg-8 col-xl-4 pb-7 pb-xl-9 text-center text-xl-start">
              <h1 class="text-white fw-light">BARANGAY INFORMATION   <br> <span class="typed-text fw-bold" data-typed-text='["HEALTH CARE","QUEUEING"]'></span><br />SYSTEM</h1>
              <p class="lead text-white opacity-75">The quality of care services and the government's role in them are very important, especially in healthcare. As performance and patient feedback become more crucial, healthcare technology gains importance. A health management system with barangay registration and a queue system worked well, guiding me to the right services.</p><a class="btn btn-outline-light border-2 rounded-pill btn-lg mt-4 fs-0 py-2" href="queuing_system.php">Get queuing number<span class="fas fa-play ms-2" data-fa-transform="shrink-6 down-1"></span></a>
            </div>
            <div class="col-xl-7 offset-xl-1 align-self-end mt-4 mt-xl-0"><a class="img-landing-banner rounded" href="index.php"><img class="img-fluid" src="assets/img/generic/dashboard-alt.png" alt="" /></a></div>
          </div>
        </div><!-- end of .container-->
      </section><!-- <section> close ============================-->
      <!-- ============================================-->



      <!-- ============================================-->
      <!-- <section> begin ============================-->
      <section class="py-3 bg-light shadow-sm">
        <div class="container">
          <div class="row flex-center">
            <div class="col-3 col-sm-auto my-1 my-sm-3 px-x1"><img class="landing-cta-img" height="40" src="assets/img/logos/conchu_logo.png" alt="" /></div>
            <div class="col-3 col-sm-auto my-1 my-sm-3 px-x1"><img class="landing-cta-img" height="40" src="assets/img/logos/bagong_trece.png" alt="" /></div>
            <div class="col-3 col-sm-auto my-1 my-sm-3 px-x1"><img class="landing-cta-img" height="40" src="assets/img/logos/bagong_trece_sagisag.png" alt="" /></div>
          </div>
        </div><!-- end of .container-->
      </section><!-- <section> close ============================-->
      <!-- ============================================-->



      <!-- ============================================-->
      <!-- <section> begin ============================-->
      <section>
        <div class="container">
          <div class="row justify-content-center text-center">
            <div class="col-lg-8 col-xl-7 col-xxl-6">
              <h1 class="fs-2 fs-sm-4 fs-md-5">Welcome to Barangay Conchu</h1>
              <p class="lead">Barangay Conchu is a clean, quiet and progressive community made up of united people with the goal of improving the quality of life. To develop programs that will improve the livelihood of the people and meet the needs of every family towards a bright future.</p>
            </div>
          </div>
          <div class="row flex-center mt-8">
            <div class="col-md col-lg-5 col-xl-4 ps-lg-6"><img class="img-fluid px-6 px-md-0" src="assets/img/icons/spot-illustrations/land_conchu.png" alt="" /></div>
            <div class="col-md col-lg-5 col-xl-4 mt-4 mt-md-0">
          
              <p>Land: 519.14 </p>
              <p>Population: 9,341</p>
              <p>Household: 2,233</p>
            </div>
          </div>
      
        </div><!-- end of .container-->
      </section><!-- <section> close ============================-->
      <!-- ============================================-->



      <!-- ============================================-->
      <!-- <section> begin ============================-->
      
      <!-- ============================================-->





      <!-- ============================================-->
      <!-- <section> begin ============================-->
      <section class="bg-dark pt-8 pb-4" data-bs-theme="light">
        <div class="container">
          <div class="position-absolute btn-back-to-top bg-dark"><a class="text-600" href="#banner" data-bs-offset-top="0"><span class="fas fa-chevron-up" data-fa-transform="rotate-45"></span></a></div>
          <div class="row">
            <div class="col-lg-4">
              <h5 class="text-uppercase text-white opacity-85 mb-3">Our Mission</h5>
              <p class="text-600">The mission of Barangay Conchu in Trece Martires City, Cavite is to improve the quality of life and develop programs to meet the needs of every family. The goal is to create a bright future for the community by developing programs that improve the livelihood of the people. 
              </p>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-4">
              <h5 class="text-uppercase text-white opacity-85 mb-3">Our Vision</h5>
              <p class="text-600">The vision of Barangay Conchu in Trece Martires City, Cavite is to be a clean, quiet, and progressive community with united residents striving for improved quality of life.  
              </p>
            </div>
          </div>

          <div class="row">
            <div class="col-lg-4">
              <h5 class="text-uppercase text-white opacity-85 mb-3">Barangay Conchu Official Facebook</h5>
 
              <div class="icon-group mt-4"><a class="icon-item bg-white text-facebook" href="https://www.facebook.com/sangguniangbarangay.conchu"><span class="fab fa-facebook-f"></span></a></div>
            </div>
          </div>

        </div><!-- end of .container-->
      </section><!-- <section> close ============================-->
      <!-- ============================================-->



      <!-- ============================================-->
      <!-- <section> begin ============================-->
      <section class="py-0 bg-dark" data-bs-theme="light">
        <div>
          <hr class="my-0 text-600 opacity-25" />
          <div class="container py-3">
            <div class="row justify-content-between fs--1">
              <div class="col-12 col-sm-auto text-center">
                <p class="mb-0 text-600 opacity-85">HEALTH CARE INFORMATION SYSTEM WITH QUEUEING SYSTEM OF <i>BARANGAY</i> CONCHU, TRECE MARTIRES CITY, CAVITE <span class="d-none d-sm-inline-block">| </span><br class="d-sm-none" /> 2024</p>
              </div>
              <div class="col-12 col-sm-auto text-center">
                <p class="mb-0 text-600 opacity-85">v1.0.0</p>
              </div>
            </div>
          </div>
        </div><!-- end of .container-->
      </section><!-- <section> close ============================-->
      <!-- ============================================-->

      <div class="modal fade" id="authentication-modal" tabindex="-1" role="dialog" aria-labelledby="authentication-modal-label" aria-hidden="true">
        <div class="modal-dialog mt-6" role="document">
          <div class="modal-content border-0">
            <div class="modal-header px-5 position-relative modal-shape-header bg-shape">
              <div class="position-relative z-1" data-bs-theme="light">
                <h4 class="mb-0 text-white" id="authentication-modal-label">Register</h4>
                <p class="fs--1 mb-0 text-white">Please create your free Falcon account</p>
              </div><button class="btn-close btn-close-white position-absolute top-0 end-0 mt-2 me-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4 px-5">
              <form>
                <div class="mb-3"><label class="form-label" for="modal-auth-name">Name</label><input class="form-control" type="text" autocomplete="on" id="modal-auth-name" /></div>
                <div class="mb-3"><label class="form-label" for="modal-auth-email">Email address</label><input class="form-control" type="email" autocomplete="on" id="modal-auth-email" /></div>
                <div class="row gx-2">
                  <div class="mb-3 col-sm-6"><label class="form-label" for="modal-auth-password">Password</label><input class="form-control" type="password" autocomplete="on" id="modal-auth-password" /></div>
                  <div class="mb-3 col-sm-6"><label class="form-label" for="modal-auth-confirm-password">Confirm Password</label><input class="form-control" type="password" autocomplete="on" id="modal-auth-confirm-password" /></div>
                </div>
                <div class="form-check"><input class="form-check-input" type="checkbox" id="modal-auth-register-checkbox" /><label class="form-label" for="modal-auth-register-checkbox">I accept the <a href="#!">terms </a>and <a href="#!">privacy policy</a></label></div>
                <div class="mb-3"><button class="btn btn-primary d-block w-100 mt-3" type="submit" name="submit">Register</button></div>
              </form>
              <div class="position-relative mt-5">
                <hr />
                <div class="divider-content-center">or register with</div>
              </div>
              <div class="row g-2 mt-2">
                <div class="col-sm-6"><a class="btn btn-outline-google-plus btn-sm d-block w-100" href="#"><span class="fab fa-google-plus-g me-2" data-fa-transform="grow-8"></span> google</a></div>
                <div class="col-sm-6"><a class="btn btn-outline-facebook btn-sm d-block w-100" href="#"><span class="fab fa-facebook-square me-2" data-fa-transform="grow-8"></span> facebook</a></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main><!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->

    <div class="offcanvas offcanvas-end settings-panel border-0" id="settings-offcanvas" tabindex="-1" aria-labelledby="settings-offcanvas">
      <div class="offcanvas-header settings-panel-header bg-shape">
        <div class="z-1 py-1" data-bs-theme="light">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <h5 class="text-white mb-0 me-2"><span class="fas fa-palette me-2 fs-0"></span>Settings</h5><button class="btn btn-primary btn-sm rounded-pill mt-0 mb-0" data-theme-control="reset" style="font-size:12px"> <span class="fas fa-redo-alt me-1" data-fa-transform="shrink-3"></span>Reset</button>
          </div>
          <p class="mb-0 fs--1 text-white opacity-75"> Set your own customized style</p>
        </div><button class="btn-close btn-close-white z-1 mt-0" type="button" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body scrollbar-overlay px-x1 h-100" id="themeController">
        <h5 class="fs-0">Color Scheme</h5>
        <p class="fs--1">Choose the perfect color mode for your app.</p>
        <div class="btn-group d-block w-100 btn-group-navbar-style">
          <div class="row gx-2">
            <div class="col-6"><input class="btn-check" id="themeSwitcherLight" name="theme-color" type="radio" value="light" data-theme-control="theme" /><label class="btn d-inline-block btn-navbar-style fs--1" for="themeSwitcherLight"> <span class="hover-overlay mb-2 rounded d-block"><img class="img-fluid img-prototype mb-0" src="assets/img/generic/falcon-mode-default.jpg" alt=""/></span><span class="label-text">Light</span></label></div>
            <div class="col-6"><input class="btn-check" id="themeSwitcherDark" name="theme-color" type="radio" value="dark" data-theme-control="theme" /><label class="btn d-inline-block btn-navbar-style fs--1" for="themeSwitcherDark"> <span class="hover-overlay mb-2 rounded d-block"><img class="img-fluid img-prototype mb-0" src="assets/img/generic/falcon-mode-dark.jpg" alt=""/></span><span class="label-text"> Dark</span></label></div>
          </div>
        </div>
        <hr />
        <div class="d-flex justify-content-between">
          <div class="d-flex align-items-start"><img class="me-2" src="assets/img/icons/left-arrow-from-left.svg" width="20" alt="" />
            <div class="flex-1">
              <h5 class="fs-0">RTL Mode</h5>
              <p class="fs--1 mb-0">Switch your language direction </p><a class="fs--1" href="documentation/customization/configuration.php">RTL Documentation</a>
            </div>
          </div>
          <div class="form-check form-switch"><input class="form-check-input ms-0" id="mode-rtl" type="checkbox" data-theme-control="isRTL" /></div>
        </div>
        <hr />
        <div class="d-flex justify-content-between">
          <div class="d-flex align-items-start"><img class="me-2" src="assets/img/icons/arrows-h.svg" width="20" alt="" />
            <div class="flex-1">
              <h5 class="fs-0">Fluid Layout</h5>
              <p class="fs--1 mb-0">Toggle container layout system </p><a class="fs--1" href="documentation/customization/configuration.php">Fluid Documentation</a>
            </div>
          </div>
          <div class="form-check form-switch"><input class="form-check-input ms-0" id="mode-fluid" type="checkbox" data-theme-control="isFluid" /></div>
        </div>
        <hr />
        <div class="d-flex align-items-start"><img class="me-2" src="assets/img/icons/paragraph.svg" width="20" alt="" />
          <div class="flex-1">
            <h5 class="fs-0 d-flex align-items-center">Navigation Position</h5>
            <p class="fs--1 mb-2">Select a suitable navigation system for your web application </p>
            <div><select class="form-select form-select-sm" aria-label="Navbar position" data-theme-control="navbarPosition">
                <option value="vertical">Vertical</option>
                <option value="top">Top</option>
                <option value="combo">Combo</option>
                <option value="double-top">Double Top</option>
              </select></div>
          </div>
        </div>
        <hr />
        <h5 class="fs-0 d-flex align-items-center">Vertical Navbar Style</h5>
        <p class="fs--1 mb-0">Switch between styles for your vertical navbar </p>
        <p> <a class="fs--1" href="modules/components/navs-and-tabs/vertical-navbar.php#navbar-styles">See Documentation</a></p>
        <div class="btn-group d-block w-100 btn-group-navbar-style">
          <div class="row gx-2">
            <div class="col-6"><input class="btn-check" id="navbar-style-transparent" type="radio" name="navbarStyle" value="transparent" data-theme-control="navbarStyle" /><label class="btn d-block w-100 btn-navbar-style fs--1" for="navbar-style-transparent"> <img class="img-fluid img-prototype" src="assets/img/generic/default.png" alt="" /><span class="label-text"> Transparent</span></label></div>
            <div class="col-6"><input class="btn-check" id="navbar-style-inverted" type="radio" name="navbarStyle" value="inverted" data-theme-control="navbarStyle" /><label class="btn d-block w-100 btn-navbar-style fs--1" for="navbar-style-inverted"> <img class="img-fluid img-prototype" src="assets/img/generic/inverted.png" alt="" /><span class="label-text"> Inverted</span></label></div>
            <div class="col-6"><input class="btn-check" id="navbar-style-card" type="radio" name="navbarStyle" value="card" data-theme-control="navbarStyle" /><label class="btn d-block w-100 btn-navbar-style fs--1" for="navbar-style-card"> <img class="img-fluid img-prototype" src="assets/img/generic/card.png" alt="" /><span class="label-text"> Card</span></label></div>
            <div class="col-6"><input class="btn-check" id="navbar-style-vibrant" type="radio" name="navbarStyle" value="vibrant" data-theme-control="navbarStyle" /><label class="btn d-block w-100 btn-navbar-style fs--1" for="navbar-style-vibrant"> <img class="img-fluid img-prototype" src="assets/img/generic/vibrant.png" alt="" /><span class="label-text"> Vibrant</span></label></div>
          </div>
        </div>
        <div class="text-center mt-5"><img class="mb-4" src="assets/img/icons/spot-illustrations/47.png" alt="" width="120" />
          <h5>Like What You See?</h5>
          <p class="fs--1">Get Falcon now and create beautiful dashboards with hundreds of widgets.</p><a class="mb-3 btn btn-primary" href="https://themes.getbootstrap.com/product/falcon-admin-dashboard-webapp-template/" target="_blank">Purchase</a>
        </div>
      </div>
    </div>
    </a>

    

    <!-- ===============================================-->
    <!--    JavaScripts-->
    <!-- ===============================================-->
    <script src="vendors/popper/popper.min.js"></script>
    <script src="vendors/bootstrap/bootstrap.min.js"></script>
    <script src="vendors/anchorjs/anchor.min.js"></script>
    <script src="vendors/is/is.min.js"></script>
    <script src="vendors/swiper/swiper-bundle.min.js"> </script>
    <script src="vendors/typed.js/typed.js"></script>
    <script src="vendors/fontawesome/all.min.js"></script>
    <script src="vendors/lodash/lodash.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src="vendors/list.js/list.min.js"></script>
    <script src="assets/js/theme.js"></script>
    <!-- Display Error Alert if session error is set -->
    
  </body>

</html>