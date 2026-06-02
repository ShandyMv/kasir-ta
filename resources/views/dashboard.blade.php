<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Spike Free Bootstrap Admin Template by WrapPixel</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
   <link rel="stylesheet" href="../assets/css/styles.min.css" />
   <style>
       .profile-dropdown .dropdown-menu { min-width: 200px; }
       @media (max-width: 768px) {
           .profile-dropdown { position: static !important; text-align: right; padding: 10px; }
       }

       .app-header {
           position: fixed;
           top: 0;
           left: 0;
           right: 0;
           z-index: 1030;
           background: #fff;
           box-shadow: 0 2px 4px rgba(0,0,0,.08);
           height: 64px;
       }

       .hamburger-btn {
           background: none;
           border: none;
           cursor: pointer;
           padding: 8px 12px;
           border-radius: 8px;
           transition: background .15s;
       }
       .hamburger-btn:hover { background: #f1f1f1; }
       .hamburger-btn svg { display: block; }

       #main-wrapper[data-sidebartype="mini-sidebar"] .body-wrapper { margin-left: 0 !important; }

       #main-wrapper[data-sidebartype="mini-sidebar"].show-sidebar .body-wrapper { margin-left: 0 !important; }

       #main-wrapper[data-sidebartype="mini-sidebar"] .left-sidebar {
           left: -270px;
           transition: left .2s ease;
           z-index: 1040 !important;
       }

       #main-wrapper[data-sidebartype="mini-sidebar"].show-sidebar .left-sidebar {
           left: 0;
           transition: left .2s ease;
           box-shadow: 2px 0 8px rgba(0,0,0,.15);
       }

       .sidebar-overlay {
           display: none;
           position: fixed;
           inset: 0;
           background: rgba(0,0,0,.35);
           z-index: 1035;
       }
       #main-wrapper.show-sidebar .sidebar-overlay { display: block; }

       .body-wrapper .container-fluid, .body-wrapper .container-sm, .body-wrapper .container-md, .body-wrapper .container-lg, .body-wrapper .container-xl, .body-wrapper .container-xxl {
           padding-top: 84px !important;
       }
   </style>
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="mini-sidebar"
     data-sidebar-position="fixed" data-header-position="fixed">

     <header class="app-header d-flex align-items-center px-3">
         <button class="hamburger-btn sidebartoggler" id="sidebarToggle" aria-label="Toggle sidebar">
             <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                 <line x1="3" y1="6" x2="21" y2="6"></line>
                 <line x1="3" y1="12" x2="21" y2="12"></line>
                 <line x1="3" y1="18" x2="21" y2="18"></line>
             </svg>
         </button>
         <div class="ms-auto">
             <div class="profile-dropdown position-relative" style="z-index: 1050;">
                 <div class="dropdown">
                     <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center" type="button" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                         <img src="../assets/images/profile/user1.jpg" alt="Profile" class="rounded-circle me-2" width="36" height="36">
                         <span>{{ auth()->user()->username }}</span>
                     </button>
                     <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                         <li><a class="dropdown-item" href="#"><i class="ti ti-user me-2"></i>My Profile</a></li>
                         <li><a class="dropdown-item" href="#"><i class="ti ti-settings me-2"></i>Settings</a></li>
                         <li><hr class="dropdown-divider"></li>
                         <li>
                             <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                 @csrf
                                 <button type="submit" class="dropdown-item"><i class="ti ti-logout me-2"></i>Logout</button>
                             </form>
                         </li>
                     </ul>
                 </div>
             </div>
         </div>
     </header>

     <div class="sidebar-overlay sidebartoggler"></div>

     <aside class="left-sidebar">
       <div>
         <div class="brand-logo d-flex align-items-center justify-content-between px-3 pt-3">
           <a href="./index.html" class="text-nowrap logo-img">
             <img src="../assets/images/logos/logo.svg" alt="" />
           </a>
           <div class="close-btn d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
             <i class="ti ti-x fs-8"></i>
           </div>
         </div>
         <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
           <ul id="sidebarnav">
             <li class="nav-small-cap">
               <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
               <span class="hide-menu">Home</span>
             </li>
             <li class="sidebar-item">
               <a class="sidebar-link primary-hover-bg" href={{ route('dashboard') }} aria-expanded="false">
                 <iconify-icon icon="solar:atom-line-duotone"></iconify-icon>
                 <span class="hide-menu">Dashboard</span>
               </a>
             </li>
             
             <li class="sidebar-item">
               <a class="sidebar-link primary-hover-bg" href="./sample-page.html" aria-expanded="false">
                 <iconify-icon icon="solar:box-broken"></iconify-icon>
                 <span class="hide-menu">Inventory</span>
               </a>
             </li>
           </ul>
         </nav>
       </div>
     </aside>

     <div class="body-wrapper">
       <div class="body-wrapper-inner">
         <div class="container-fluid">

           <div class="row">
             <div class="col-lg-8 d-flex align-items-strech">
               <div class="card w-100">
                 <div class="card-body">
                   <div class="d-flex align-items-center justify-content-between mb-1">
                     <div class="">
                       <h5 class="card-title fw-semibold">Profit & Expenses</h5>
                     </div>
                     <div class="dropdown">
                       <button id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"
                         class="rounded-circle btn-transparent rounded-circle btn-sm px-1 btn shadow-none">
                         <i class="ti ti-dots-vertical fs-7 d-block"></i>
                       </button>
                       <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                         <li><a class="dropdown-item" href="#">Action</a></li>
                         <li>
                           <a class="dropdown-item" href="#">Another action</a>
                         </li>
                         <li>
                           <a class="dropdown-item" href="#">Something else here</a>
                         </li>
                       </ul>
                     </div>
                   </div>
                   <div id="profit"></div>
                 </div>
               </div>
             </div>
             <div class="col-lg-4">
               <div class="row">
                 <div class="col-lg-12 col-sm-6">
                   <div class="card overflow-hidden">
                     <div class="card-body p-4">
                       <h5 class="card-title mb-10 fw-semibold">Traffic Distribution</h5>
                       <div class="row align-items-center">
                         <div class="col-7">
                           <h4 class="fw-semibold mb-3">$36,358</h4>
                           <div class="d-flex align-items-center mb-2">
                             <span
                               class="me-1 rounded-circle bg-light-success round-20 d-flex align-items-center justify-content-center">
                               <i class="ti ti-arrow-up-left text-success"></i>
                             </span>
                             <p class="text-dark me-1 fs-3 mb-0">+9%</p>
                             <p class="fs-3 mb-0">last year</p>
                           </div>
                           <div class="d-flex align-items-center">
                             <div class="me-3">
                               <span class="round-8 bg-primary rounded-circle me-2 d-inline-block"></span>
                               <span class="fs-2">Oragnic</span>
                             </div>
                             <div>
                               <span class="round-8 bg-danger rounded-circle me-2 d-inline-block"></span>
                               <span class="fs-2">Refferal</span>
                             </div>
                           </div>
                         </div>
                         <div class="col-5">
                           <div class="d-flex justify-content-center">
                             <div id="grade"></div>
                           </div>
                         </div>
                       </div>
                     </div>
                   </div>
                 </div>
                 <div class="col-lg-12 col-sm-6">
                   <div class="card">
                     <div class="card-body">
                       <div class="row alig n-items-start">
                         <div class="col-8">
                           <h5 class="card-title mb-10 fw-semibold"> Product Sales</h5>
                           <h4 class="fw-semibold mb-3">$6,820</h4>
                           <div class="d-flex align-items-center pb-1">
                             <span
                               class="me-2 rounded-circle bg-light-danger round-20 d-flex align-items-center justify-content-center">
                               <i class="ti ti-arrow-down-right text-danger"></i>
                             </span>
                             <p class="text-dark me-1 fs-3 mb-0">+9%</p>
                             <p class="fs-3 mb-0">last year</p>
                           </div>
                         </div>
                         <div class="col-4">
                           <div class="d-flex justify-content-end">
                             <div
                               class="text-white bg-danger rounded-circle p-6 d-flex align-items-center justify-content-center">
                               <i class="ti ti-currency-dollar fs-6"></i>
                             </div>
                           </div>
                         </div>
                       </div>
                     </div>
                     <div id="earning"></div>
                   </div>
                 </div>
               </div>
             </div>
           </div>
           <div class="row">
             <div class="col-lg-4 d-flex align-items-stretch">
               <div class="card w-100">
                 <div class="card-body p-4">
                   <div class="mb-4">
                     <h5 class="card-title fw-semibold">Upcoming Schedules</h5>
                   </div>
                   <ul class="timeline-widget mb-0 position-relative mb-n5">
                     <li class="timeline-item d-flex position-relative overflow-hidden">
                       <div class="timeline-time text-dark flex-shrink-0 text-end">09:30</div>
                       <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                         <span class="timeline-badge border-2 border border-primary flex-shrink-0 my-2"></span>
                         <span class="timeline-badge-border d-block flex-shrink-0"></span>
                       </div>
                       <div class="timeline-desc fs-3 text-dark mt-n1">Payment received from John Doe of $385.90</div>
                     </li>
                     <li class="timeline-item d-flex position-relative overflow-hidden">
                       <div class="timeline-time text-dark flex-shrink-0 text-end">10:00 am</div>
                       <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                         <span class="timeline-badge border-2 border border-info flex-shrink-0 my-2"></span>
                         <span class="timeline-badge-border d-block flex-shrink-0"></span>
                       </div>
                       <div class="timeline-desc fs-3 text-dark mt-n1 fw-semibold">New sale recorded <a
                           href="javascript:void(0)" class="text-primary d-block fw-normal">#ML-3467</a>
                       </div>
                     </li>
                     <li class="timeline-item d-flex position-relative overflow-hidden">
                       <div class="timeline-time text-dark flex-shrink-0 text-end">12:00 am</div>
                       <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                         <span class="timeline-badge border-2 border border-success flex-shrink-0 my-2"></span>
                         <span class="timeline-badge-border d-block flex-shrink-0"></span>
                       </div>
                       <div class="timeline-desc fs-3 text-dark mt-n1">Payment was made of $64.95 to Michael</div>
                     </li>
                     <li class="timeline-item d-flex position-relative overflow-hidden">
                       <div class="timeline-time text-dark flex-shrink-0 text-end">09:30 am</div>
                       <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                         <span class="timeline-badge border-2 border border-warning flex-shrink-0 my-2"></span>
                         <span class="timeline-badge-border d-block flex-shrink-0"></span>
                       </div>
                       <div class="timeline-desc fs-3 text-dark mt-n1 fw-semibold">New sale recorded <a
                           href="javascript:void(0)" class="text-primary d-block fw-normal">#ML-3467</a>
                       </div>
                     </li>
                     <li class="timeline-item d-flex position-relative overflow-hidden">
                       <div class="timeline-time text-dark flex-shrink-0 text-end">09:30 am</div>
                       <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                         <span class="timeline-badge border-2 border border-danger flex-shrink-0 my-2"></span>
                         <span class="timeline-badge-border d-block flex-shrink-0"></span>
                       </div>
                       <div class="timeline-desc fs-3 text-dark mt-n1 fw-semibold">New arrival recorded
                       </div>
                     </li>
                     <li class="timeline-item d-flex position-relative overflow-hidden">
                       <div class="timeline-time text-dark flex-shrink-0 text-end">12:00 am</div>
                       <div class="timeline-badge-wrap d-flex flex-column align-items-center">
                         <span class="timeline-badge border-2 border border-success flex-shrink-0 my-2"></span>
                       </div>
                       <div class="timeline-desc fs-3 text-dark mt-n1">Payment Done</div>
                     </li>
                   </ul>
                 </div>
               </div>
             </div>
             <div class="col-lg-8 d-flex align-items-stretch">
               <div class="card w-100">
                 <div class="card-body p-4">
                   <div class="d-flex mb-4 justify-content-between align-items-center">
                     <h5 class="mb-0 fw-bold">Top Paying Clients</h5>

                     <div class="dropdown">
                       <button id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false"
                         class="rounded-circle btn-transparent rounded-circle btn-sm px-1 btn shadow-none">
                         <i class="ti ti-dots-vertical fs-7 d-block"></i>
                       </button>
                       <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                         <li><a class="dropdown-item" href="#">Action</a></li>
                         <li>
                           <a class="dropdown-item" href="#">Another action</a>
                         </li>
                         <li>
                           <a class="dropdown-item" href="#">Something else here</a>
                         </li>
                       </ul>
                     </div>
                   </div>

                   <div class="table-responsive" data-simplebar>
                     <table class="table table-borderless align-middle text-nowrap">
                       <thead>
                         <tr>
                           <th scope="col">Profile</th>
                           <th scope="col">Hour Rate</th>
                           <th scope="col">Extra classes</th>
                           <th scope="col">Status</th>
                         </tr>
                       </thead>
                       <tbody>
                         <tr>
                           <td>
                             <div class="d-flex align-items-center">
                               <div class="me-4">
                                 <img src="../assets/images/profile/user1.jpg" width="50" class="rounded-circle"
                                   alt="" />
                               </div>

                               <div>
                                 <h6 class="mb-1 fw-bolder">Mark J. Freeman</h6>
                                 <p class="fs-3 mb-0">Prof. English</p>
                               </div>
                             </div>
                           </td>
                           <td>
                             <p class="fs-3 fw-normal mb-0">$150/hour</p>
                           </td>
                           <td>
                             <p class="fs-3 fw-normal mb-0 text-success">
                               +53
                             </p>
                           </td>
                           <td>
                             <span
                               class="badge bg-light-success rounded-pill text-success px-3 py-2 fs-3">Available</span>
                           </td>
                         </tr>

                         <tr>
                           <td>
                             <div class="d-flex align-items-center">
                               <div class="me-4">
                                 <img src="../assets/images/profile/user2.jpg" width="50" class="rounded-circle"
                                   alt="" />
                               </div>

                               <div>
                                 <h6 class="mb-1 fw-bolder">Nina R. Oldman</h6>
                                 <p class="fs-3 mb-0">Prof. History</p>
                               </div>
                             </div>
                           </td>
                           <td>
                             <p class="fs-3 fw-normal mb-0">$150/hour</p>
                           </td>
                           <td>
                             <p class="fs-3 fw-normal mb-0 text-success">
                               +68
                             </p>
                           </td>
                           <td>
                             <span class="badge bg-light-primary rounded-pill text-primary px-3 py-2 fs-3">In
                               Class</span>
                           </td>
                         </tr>

                         <tr>
                           <td>
                             <div class="d-flex align-items-center">
                               <div class="me-4">
                                 <img src="../assets/images/profile/user3.jpg" width="50" class="rounded-circle"
                                   alt="" />
                               </div>

                               <div>
                                 <h6 class="mb-1 fw-bolder">Arya H. Shah</h6>
                                 <p class="fs-3 mb-0">Prof. Maths</p>
                               </div>
                             </div>
                           </td>
                           <td>
                             <p class="fs-3 fw-normal mb-0">$150/hour</p>
                           </td>
                           <td>
                             <p class="fs-3 fw-normal mb-0 text-success">
                               +94
                             </p>
                           </td>
                           <td>
                             <span class="badge bg-light-danger rounded-pill text-danger px-3 py-2 fs-3">Absent</span>
                           </td>
                         </tr>

                         <tr>
                           <td>
                             <div class="d-flex align-items-center">
                               <div class="me-4">
                                 <img src="../assets/images/profile/user4.jpg" width="50" class="rounded-circle"
                                   alt="" />
                               </div>

                               <div>
                                 <h6 class="mb-1 fw-bolder">June R. Smith</h6>
                                 <p class="fs-3 mb-0">Prof. Arts</p>
                               </div>
                             </div>
                           </td>
                           <td>
                             <p class="fs-3 fw-normal mb-0">$150/hour</p>
                           </td>
                           <td>
                             <p class="fs-3 fw-normal mb-0 text-success">
                               +27
                             </p>
                           </td>
                           <td>
                             <span class="badge bg-light-warning rounded-pill text-warning px-3 py-2 fs-3">On
                               Leave</span>
                           </td>
                         </tr>
                       </tbody>
                     </table>
                   </div>
                 </div>
               </div>
             </div>
           </div>
           
           <div class="py-6 px-6 text-center">
             <p class="mb-0 fs-4">Design and Developed by <a href="https://www.wrappixel.com/" target="_blank"
                 class="pe-1 text-primary text-decoration-underline">wrappixel.com</a> Distributed by <a href="https://themewagon.com/">ThemeWagon</a></p>
           </div>
         </div>
       </div>
     </div>
   </div>
  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/sidebarmenu.js"></script>
  <script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
  <script src="../assets/js/dashboard.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.sidebartoggler').on('click', function() {
        $('#main-wrapper').toggleClass('show-sidebar');
      });
    });
  </script>
</body>

</html>
