
<?php
include_once('hms/include/config.php');
if(isset($_POST['submit']))
{
$name=$_POST['fullname'];
$email=$_POST['emailid'];
$mobileno=$_POST['mobileno'];
$dscrption=$_POST['description'];
$query=mysqli_query($con,"insert into tblcontactus(fullname,email,contactno,message) value('$name','$email','$mobileno','$dscrption')");
echo "<script>alert('Your information succesfully submitted');</script>";
echo "<script>window.location.href ='index.php'</script>";

} ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>HMS - Advanced Clinical Management System</title>

    <link rel="shortcut icon" href="assets/images/fav.jpg">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/fontawsom-all.min.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css" />
    <link rel="stylesheet" type="text/css" href="assets/css/modern-styles.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Enhanced Landing Page CSS */
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary-color: #7c3aed;
            --accent-color: #06b6d4;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --light-color: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }

        body {
            font-family: 'Inter', 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--gray-700);
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        /* Enhanced Header */
        .modern-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--gray-200);
            box-shadow: var(--shadow-lg);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .header-nav {
            padding: 1rem 0;
        }

        .logo-section {
            transition: all 0.3s ease;
        }

        .logo-main {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo-subtitle {
            color: var(--gray-600);
            font-weight: 500;
        }

        .modern-nav li a {
            position: relative;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .modern-nav li a:hover {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white !important;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .modern-nav li a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            transition: all 0.3s ease;
            transform: translateX(-50%);
        }

        .modern-nav li a:hover::before {
            width: 80%;
        }

        .btn-appointment {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
        }

        .btn-appointment:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-xl);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            color: white;
            text-decoration: none;
        }

                 /* Enhanced Hero Section */
         .hero-section {
             margin-top: 80px;
             position: relative;
             overflow: hidden;
             min-height: 100vh;
             display: flex;
             align-items: center;
             justify-content: center;
         }
 
         .hero-background {
             position: absolute;
             top: 0;
             left: 0;
             right: 0;
             bottom: 0;
             background: url('assets/images/slider/slider_2.jpg') center center/cover no-repeat;
             z-index: 1;
         }
 
         .hero-overlay {
             background: linear-gradient(135deg, rgba(37, 99, 235, 0.85), rgba(124, 58, 237, 0.85));
             position: absolute;
             top: 0;
             left: 0;
             right: 0;
             bottom: 0;
             z-index: 2;
         }
 
         .hero-content {
             position: relative;
             z-index: 3;
             color: white;
             text-align: center;
             width: 100%;
             display: flex;
             justify-content: center;
             align-items: center;
             min-height: 100vh;
             padding: 2rem 0;
         }

         .hero-content .container {
             display: flex;
             justify-content: center;
             align-items: center;
             min-height: 100vh;
         }

         .hero-content .row {
             width: 100%;
             justify-content: center;
             text-align: center;
         }

                 .hero-title {
             font-size: 3.5rem;
             font-weight: 800;
             color: white;
             text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.7);
             margin-bottom: 1.5rem;
             text-align: center;
         }

                 .hero-subtitle {
             font-size: 1.3rem;
             color: white;
             margin-bottom: 2rem;
             line-height: 1.6;
             text-align: center;
             text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
         }

                 .hero-actions {
             margin-bottom: 3rem;
             display: flex;
             justify-content: center;
             align-items: center;
             flex-wrap: wrap;
             gap: 1rem;
         }

                 .btn-hero-primary, .btn-hero-secondary {
             padding: 15px 30px;
             border-radius: 30px;
             font-weight: 600;
             font-size: 1.1rem;
             margin: 0;
             transition: all 0.3s ease;
             border: none;
             text-decoration: none;
             display: inline-block;
             text-align: center;
         }

        .btn-hero-primary {
            background: linear-gradient(135deg, var(--success-color), var(--accent-color));
            color: white;
            box-shadow: var(--shadow-lg);
        }

        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .btn-hero-primary:hover, .btn-hero-secondary:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-xl);
            color: white;
            text-decoration: none;
        }

        .hero-stats {
            display: flex;
            justify-content: space-around;
            margin-top: 3rem;
        }

        .stat-item {
            text-align: center;
            color: white;
        }

        .stat-item i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--accent-color);
        }

        .stat-number {
            display: block;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1rem;
            opacity: 0.9;
        }

        /* Enhanced Login Portal */
        .login-portal {
            padding: 5rem 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .section-header {
            margin-bottom: 4rem;
        }

        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--dark-color);
            margin-bottom: 1rem;
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: var(--gray-600);
            margin-bottom: 1.5rem;
        }

        .title-divider {
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            margin: 0 auto;
            border-radius: 2px;
        }

        .login-card {
            background: white;
            border-radius: 20px;
            padding: 2rem;
            box-shadow: var(--shadow-lg);
            transition: all 0.3s ease;
            border: 1px solid var(--gray-200);
            height: 100%;
        }

        .login-card:hover {
            transform: translateY(-10px);
            box-shadow: var(--shadow-xl);
        }

        .card-icon {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .card-icon i {
            font-size: 3rem;
            padding: 1.5rem;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            box-shadow: var(--shadow-md);
        }

        .card-content h4 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 1rem;
            text-align: center;
        }

        .card-content p {
            color: var(--gray-600);
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin-bottom: 2rem;
        }

        .feature-list li {
            padding: 0.5rem 0;
            color: var(--gray-700);
        }

        .feature-list li i {
            color: var(--success-color);
            margin-right: 0.5rem;
        }

        .btn-login-primary, .btn-login-secondary, .btn-login-tertiary {
            width: 100%;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-login-primary {
            background: linear-gradient(135deg, var(--success-color), var(--accent-color));
            color: white;
        }

        .btn-login-secondary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .btn-login-tertiary {
            background: linear-gradient(135deg, var(--warning-color), var(--danger-color));
            color: white;
        }

        .btn-login-primary:hover, .btn-login-secondary:hover, .btn-login-tertiary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            color: white;
            text-decoration: none;
        }

        /* Enhanced Services Section */
        .key-features {
            padding: 6rem 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            position: relative;
            overflow: hidden;
        }

        .key-features::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%23e2e8f0" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .inner-title {
            position: relative;
            z-index: 2;
        }

        .inner-title h2 {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
            margin-bottom: 1rem;
            position: relative;
        }

        .inner-title h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }

        .inner-title p {
            font-size: 1.3rem;
            color: var(--gray-600);
            text-align: center;
            margin-bottom: 4rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        .single-key {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 3rem 2rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: var(--shadow-lg);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 2px solid transparent;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            z-index: 2;
        }

        .single-key::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent, rgba(37, 99, 235, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .single-key:hover::before {
            left: 100%;
        }

        .single-key:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary-color);
            background: rgba(255, 255, 255, 1);
        }

        .single-key i {
            font-size: 4rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1.5rem;
            display: block;
            transition: all 0.3s ease;
        }

        .single-key:hover i {
            transform: scale(1.1) rotate(5deg);
        }

        .single-key h5 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark-color);
            margin: 0 0 1rem 0;
            position: relative;
        }

        .single-key h5::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 2px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 1px;
            transition: width 0.3s ease;
        }

        .single-key:hover h5::after {
            width: 60px;
        }

        .single-key .feature-description {
            color: var(--gray-600);
            font-size: 0.95rem;
            line-height: 1.6;
            margin-top: 1rem;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }

        .single-key:hover .feature-description {
            opacity: 1;
            transform: translateY(0);
        }

        .single-key .feature-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: linear-gradient(135deg, var(--success-color), var(--accent-color));
            color: white;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 600;
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.3s ease;
        }

        .single-key:hover .feature-badge {
            opacity: 1;
            transform: scale(1);
        }

        /* Enhanced About Section */
        .about-hospital {
            padding: 5rem 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .about-image {
            position: relative;
        }

        .about-image img {
            border-radius: 20px;
            box-shadow: var(--shadow-xl);
        }

        .about-stats {
            position: absolute;
            bottom: -30px;
            right: -30px;
            display: flex;
            gap: 1rem;
        }

        .stat-box {
            background: white;
            padding: 1rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: var(--shadow-lg);
            min-width: 120px;
        }

        .stat-box i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-number {
            display: block;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--dark-color);
        }

        .stat-text {
            font-size: 0.9rem;
            color: var(--gray-600);
        }

        .about-content {
            padding-left: 3rem;
        }

        .section-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .about-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .about-description {
            font-size: 1.1rem;
            color: var(--gray-600);
            margin-bottom: 2rem;
            line-height: 1.7;
        }

        .about-features {
            margin-bottom: 2rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .feature-item i {
            color: var(--success-color);
            margin-right: 1rem;
            font-size: 1.2rem;
        }

        .feature-item span {
            color: var(--gray-700);
            font-weight: 500;
        }

        .about-actions {
            display: flex;
            gap: 1rem;
        }

        .btn-about-primary, .btn-about-secondary {
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: 600;
            border: none;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-about-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .btn-about-secondary {
            background: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-about-primary:hover, .btn-about-secondary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            text-decoration: none;
        }

        /* Enhanced Gallery */
        .gallery {
            padding: 6rem 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            position: relative;
            overflow: hidden;
        }

        .gallery::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="gallery-grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="%23e2e8f0" stroke-width="0.3"/></pattern></defs><rect width="100" height="100" fill="url(%23gallery-grid)"/></svg>');
            opacity: 0.4;
        }

        .gallery .inner-title {
            position: relative;
            z-index: 2;
        }

        .gallery .inner-title h2 {
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-align: center;
            margin-bottom: 1rem;
            position: relative;
        }

        .gallery .inner-title h2::after {
            content: '';
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 2px;
        }

        .gallery .inner-title p {
            font-size: 1.3rem;
            color: var(--gray-600);
            text-align: center;
            margin-bottom: 3rem;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        .gallery-filter {
            text-align: center;
            margin-bottom: 4rem;
            position: relative;
            z-index: 2;
        }

        .filter-button {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 2px solid var(--gray-300);
            color: var(--gray-600);
            padding: 12px 24px;
            margin: 0 8px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .filter-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, transparent, rgba(37, 99, 235, 0.1), transparent);
            transition: left 0.6s ease;
        }

        .filter-button:hover::before {
            left: 100%;
        }

        .filter-button:hover, .filter-button.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-color: var(--primary-color);
            color: white;
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .filter-button.active {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-color: var(--primary-color);
            color: white;
            box-shadow: var(--shadow-lg);
        }

        .gallery_product {
            margin-bottom: 2.5rem;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            background: white;
            z-index: 2;
        }

        .gallery_product::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(124, 58, 237, 0.1));
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: 1;
        }

        .gallery_product:hover::before {
            opacity: 1;
        }

        .gallery_product:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--shadow-xl);
        }

        .gallery_product img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            transition: all 0.4s ease;
            position: relative;
            z-index: 2;
        }

        .gallery_product:hover img {
            transform: scale(1.1);
        }

        .gallery_product .gallery-overlay {
            position: absolute;
            bottom: -100%;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.9), rgba(124, 58, 237, 0.9));
            color: white;
            padding: 1.5rem;
            transition: bottom 0.4s ease;
            z-index: 3;
        }

        .gallery_product:hover .gallery-overlay {
            bottom: 0;
        }

        .gallery_product .gallery-overlay h4 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .gallery_product .gallery-overlay p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
        }

        .gallery_product .gallery-badge {
            position: absolute;
            top: 15px;
            left: 15px;
            background: linear-gradient(135deg, var(--success-color), var(--accent-color));
            color: white;
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            z-index: 3;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .gallery_product:hover .gallery-badge {
            opacity: 1;
            transform: translateY(0);
        }

        .gallery_product .gallery-actions {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 0.5rem;
            z-index: 3;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .gallery_product:hover .gallery-actions {
            opacity: 1;
            transform: translateY(0);
        }

        .gallery_product .gallery-actions .btn-gallery {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.9);
            border: none;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .gallery_product .gallery-actions .btn-gallery:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.1);
        }

        /* Enhanced Contact Section */
        .contact-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .contact-form-wrapper {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            box-shadow: var(--shadow-xl);
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .form-header h3 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 1rem;
        }

        .form-header p {
            color: var(--gray-600);
            font-size: 1.1rem;
        }

        .modern-input {
            border: 2px solid var(--gray-200);
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .modern-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            outline: none;
        }

        .btn-contact-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 25px;
            padding: 15px 30px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-contact-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .contact-info {
            text-align: center;
            margin-top: 1rem;
            color: var(--gray-600);
        }

        /* Enhanced Footer */
        .modern-footer {
            background: var(--dark-color);
            color: white;
            position: relative;
        }

        .footer-main {
            padding: 2.5rem 0 1.5rem;
        }

        .footer-logo-main {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .footer-logo-main i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-right: 1rem;
        }

        .footer-logo-text {
            font-size: 1.8rem;
            font-weight: 800;
        }

        .footer-logo-subtitle {
            color: var(--gray-300);
            font-size: 1rem;
        }

        .footer-description {
            color: var(--gray-300);
            line-height: 1.6;
            margin: 1rem 0;
        }

        .footer-contact-info {
            margin: 1.5rem 0;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
            color: var(--gray-300);
        }

        .contact-item i {
            color: var(--primary-color);
            margin-right: 0.8rem;
            margin-top: 0.2rem;
            min-width: 16px;
        }

        .contact-item span {
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .footer-social {
            display: flex;
            gap: 0.8rem;
            margin-top: 1rem;
        }

        .social-link {
            display: inline-block;
            width: 42px;
            height: 42px;
            background: var(--gray-700);
            color: white;
            text-align: center;
            line-height: 42px;
            border-radius: 50%;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .social-link:hover {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            transform: translateY(-3px);
            color: white;
            text-decoration: none;
            box-shadow: var(--shadow-md);
        }

        .footer-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: white;
            position: relative;
        }

        .footer-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            width: 30px;
            height: 2px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 1px;
        }

        .footer-links {
            list-style: none;
            padding: 0;
        }

        .footer-links li {
            margin-bottom: 0.6rem;
        }

        .footer-links a {
            color: var(--gray-300);
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
        }

        .footer-links a i {
            margin-right: 0.5rem;
            font-size: 0.8rem;
            color: var(--gray-400);
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            color: var(--primary-color);
            padding-left: 5px;
        }

        .footer-links a:hover i {
            color: var(--primary-color);
        }

        /* Newsletter Section */
        .footer-newsletter {
            background: var(--gray-800);
            padding: 1.5rem 0;
            border-top: 1px solid var(--gray-700);
        }

        .newsletter-content h4 {
            color: white;
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .newsletter-content h4 i {
            color: var(--primary-color);
            margin-right: 0.5rem;
        }

        .newsletter-content p {
            color: var(--gray-300);
            margin: 0;
            font-size: 0.95rem;
        }

        .newsletter-form {
            display: flex;
            gap: 1rem;
        }

        .newsletter-form .form-control {
            border: 2px solid var(--gray-600);
            background: var(--gray-700);
            color: white;
            border-radius: 25px;
            padding: 12px 20px;
            font-size: 0.95rem;
        }

        .newsletter-form .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background: var(--gray-600);
        }

        .newsletter-form .form-control::placeholder {
            color: var(--gray-400);
        }

        .btn-newsletter {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 25px;
            color: white;
            padding: 12px 24px;
            font-weight: 600;
            transition: all 0.3s ease;
            white-space: nowrap;
        }

        .btn-newsletter:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
            color: white;
        }

        .footer-bottom {
            background: var(--gray-800);
            padding: 1.5rem 0;
            border-top: 1px solid var(--gray-700);
        }

        .copyright {
            color: var(--gray-300);
            margin: 0;
            font-size: 0.9rem;
        }

        .footer-bottom-links {
            text-align: right;
        }

        .footer-bottom-links a {
            color: var(--gray-400);
            text-decoration: none;
            margin-left: 1.5rem;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .footer-bottom-links a:hover {
            color: var(--primary-color);
        }

        /* Responsive Footer */
        @media (max-width: 768px) {
            .footer-main {
                padding: 2rem 0 1rem;
            }
            
            .footer-section {
                margin-bottom: 1.5rem;
            }
            
            .newsletter-form {
                flex-direction: column;
                margin-top: 1rem;
            }
            
            .footer-bottom-links {
                text-align: center;
                margin-top: 1rem;
            }
            
            .footer-bottom-links a {
                margin: 0 0.75rem;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-subtitle {
                font-size: 1.1rem;
            }
            
            .section-title {
                font-size: 2rem;
            }
            
            .about-title {
                font-size: 2rem;
            }
            
            .hero-stats {
                flex-direction: column;
                gap: 2rem;
            }
            
            .about-actions {
                flex-direction: column;
            }
            
            .contact-form-wrapper {
                padding: 2rem;
            }

            .key-features {
                padding: 4rem 0;
            }

            .inner-title h2 {
                font-size: 2.5rem;
            }

            .inner-title p {
                font-size: 1.1rem;
                margin-bottom: 3rem;
            }

            .single-key {
                padding: 2rem 1.5rem;
                margin-bottom: 1.5rem;
            }

            .single-key i {
                font-size: 3rem;
            }

            .single-key h5 {
                font-size: 1.3rem;
            }
        }

        @media (max-width: 576px) {
            .key-features {
                padding: 3rem 0;
            }

            .inner-title h2 {
                font-size: 2rem;
            }

            .single-key {
                padding: 1.5rem 1rem;
            }

            .single-key .feature-description {
                font-size: 0.9rem;
            }

            .gallery {
                padding: 3rem 0;
            }

            .gallery .inner-title h2 {
                font-size: 2rem;
            }

            .gallery .inner-title p {
                font-size: 1.1rem;
                margin-bottom: 2rem;
            }

            .filter-button {
                padding: 10px 18px;
                margin: 0 4px;
                font-size: 0.9rem;
            }

            .gallery_product {
                margin-bottom: 1.5rem;
            }

            .gallery_product img {
                height: 220px;
            }

            .gallery_product .gallery-overlay {
                padding: 1rem;
            }

            .gallery_product .gallery-overlay h4 {
                font-size: 1.1rem;
            }

            .gallery_product .gallery-overlay p {
                font-size: 0.85rem;
            }
        }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Loading Animation */
        .fade-in {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, var(--primary-dark), var(--primary-color));
        }
    </style>
</head>

    <body>

    <!-- ################# Modern Header Starts Here#######################--->
    
    <header id="menu-jk" class="modern-header">
        <div id="nav-head" class="header-nav">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-2 col-md-3 col-sm-6">
                        <div class="logo-section" style="text-align: left; margin-left: -95px; padding-left: 0; position: relative; left: -10px;">
                            <div class="logo-main" style="justify-content: flex-start; align-items: center; display: flex; margin-left: 0;">
                                <i class="fas fa-hospital-symbol" style="font-size: 2.2rem; margin-right: 8px;"></i>
                                <span class="logo-text" style="font-size: 1.8rem; font-weight: 700;">HMS</span>
                            </div>
                            <span class="logo-subtitle" style="font-size: 0.9rem; font-weight: 500; display: block; margin-left: 0;">Clinical Management System</span>
                        </div>
                        <a data-toggle="collapse" data-target="#menu" href="#menu" class="mobile-menu-toggle">
                            <i class="fas fa-bars"></i>
                        </a>
                    </div>
                    <div id="menu" class="col-lg-8 col-md-9 d-none d-md-block nav-item">
                        <ul class="modern-nav" style="margin: 0; padding: 0; display: flex; justify-content: center; align-items: center; flex-wrap: nowrap;">
                                                         <li style="margin: 0 2px;"><a href="hms/doctor-directory.php" style="font-size: 0.95rem; font-weight: 500; padding: 6px 4px; display: block; text-decoration: none; color: inherit; transition: all 0.3s ease; white-space: nowrap;"><i class="fas fa-user-md"></i> Find Doctors</a></li>
                             <li style="margin: 0 2px;"><a href="#services" style="font-size: 0.95rem; font-weight: 500; padding: 6px 4px; display: block; text-decoration: none; color: inherit; transition: all 0.3s ease; white-space: nowrap;"><i class="fas fa-stethoscope"></i> Services</a></li>
                             <li style="margin: 0 2px;"><a href="#about_us" style="font-size: 0.95rem; font-weight: 500; padding: 6px 4px; display: block; text-decoration: none; color: inherit; transition: all 0.3s ease; white-space: nowrap;"><i class="fas fa-info-circle"></i> About Us</a></li>
                             <li style="margin: 0 2px;"><a href="#gallery" style="font-size: 0.95rem; font-weight: 500; padding: 6px 4px; display: block; text-decoration: none; color: inherit; transition: all 0.3s ease; white-space: nowrap;"><i class="fas fa-images"></i> Gallery</a></li>
                             <li style="margin: 0 2px;"><a href="#contact_us" style="font-size: 0.95rem; font-weight: 500; padding: 6px 4px; display: block; text-decoration: none; color: inherit; transition: all 0.3s ease; white-space: nowrap;"><i class="fas fa-envelope"></i> Contact</a></li>
                             <li style="margin: 0 2px;"><a href="#logins" style="font-size: 0.95rem; font-weight: 500; padding: 6px 4px; display: block; text-decoration: none; color: inherit; transition: all 0.3s ease; white-space: nowrap;"><i class="fas fa-sign-in-alt"></i> Login</a></li>  
                        </ul>
                    </div>
                    <div class="col-lg-2 d-none d-lg-block">
                        <a class="btn btn-appointment" href="hms/user-login.php" style="font-size: 0.95rem; font-weight: 500; padding: 10px 16px; white-space: nowrap; display: inline-block; text-align: center;">
                            <i class="fas fa-calendar-plus"></i>
                            Book Appointment
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
     <!-- ################# Modern Hero Section Starts Here#######################--->

         <section class="hero-section">
         <div class="hero-background"></div>
         <div class="hero-overlay"></div>
         <div class="hero-content">
             <div class="container">
                 <div class="row">
                     <div class="col-lg-8 col-md-10">
                         <h1 class="hero-title animated fadeInUp">
                             Intelligent Health Assistant
                         </h1>
                         <p class="hero-subtitle animated fadeInUp" style="animation-delay: 0.3s;">
                             Streamlining healthcare operations with cutting-edge technology. 
                             Providing comprehensive patient care management, doctor scheduling, 
                             and seamless hospital administration.
                         </p>
                         <div class="hero-actions animated fadeInUp" style="animation-delay: 0.6s;">
                             <a href="hms/user-login.php" class="btn btn-hero-primary">
                                 <i class="fas fa-calendar-check"></i>
                                 Book Appointment
                             </a>
                             <a href="#services" class="btn btn-hero-secondary">
                                 <i class="fas fa-info-circle"></i>
                                 Learn More
                             </a>
                         </div>
                         <div class="hero-stats animated fadeInUp" style="animation-delay: 0.9s;">
                             <div class="stat-item">
                                 <i class="fas fa-user-md"></i>
                                 <span class="stat-number">500+</span>
                                 <span class="stat-label">Expert Doctors</span>
                             </div>
                             <div class="stat-item">
                                 <i class="fas fa-hospital"></i>
                                 <span class="stat-number">50+</span>
                                 <span class="stat-label">Departments</span>
                             </div>
                             <div class="stat-item">
                                 <i class="fas fa-heart"></i>
                                 <span class="stat-number">10K+</span>
                                 <span class="stat-label">Happy Patients</span>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </section>
    
  <!--  ************************* Modern Login Portal ************************** -->
    
    <section id="logins" class="login-portal">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">Access Portal</h2>
                <p class="section-subtitle">Choose your role to access the Clinical Management System</p>
                <div class="title-divider"></div>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="login-card patient-card">
                        <div class="card-icon">
                            <i class="fas fa-user-injured"></i>
                        </div>
                        <div class="card-content">
                            <h4>Patient Portal</h4>
                            <p>Book appointments, view medical records, and manage your healthcare journey</p>
                            <ul class="feature-list">
                                <li><i class="fas fa-check"></i> Online Appointments</li>
                                <li><i class="fas fa-check"></i> Medical History</li>
                                <li><i class="fas fa-check"></i> Lab Reports</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="hms/user-login.php" class="btn btn-login-primary">
                                <i class="fas fa-sign-in-alt"></i>
                                Patient Login
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="login-card doctor-card">
                        <div class="card-icon">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div class="card-content">
                            <h4>Doctor Portal</h4>
                            <p>Manage patients, appointments, and medical records efficiently</p>
                            <ul class="feature-list">
                                <li><i class="fas fa-check"></i> Patient Management</li>
                                <li><i class="fas fa-check"></i> Schedule Management</li>
                                <li><i class="fas fa-check"></i> Prescription Tools</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="hms/doctor" class="btn btn-login-secondary">
                                <i class="fas fa-stethoscope"></i>
                                Doctor Login
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="login-card admin-card">
                        <div class="card-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="card-content">
                            <h4>Admin Portal</h4>
                            <p>Complete system administration and hospital management control</p>
                            <ul class="feature-list">
                                <li><i class="fas fa-check"></i> System Management</li>
                                <li><i class="fas fa-check"></i> Staff Administration</li>
                                <li><i class="fas fa-check"></i> Reports & Analytics</li>
                            </ul>
                        </div>
                        <div class="card-footer">
                            <a href="hms/admin" class="btn btn-login-tertiary">
                                <i class="fas fa-cogs"></i>
                                Admin Login
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>  







    <!-- ################# Our Departments Starts Here#######################--->


    <section id="services" class="key-features department">
        <div class="container">
            <div class="inner-title">

                <h2>Our Key Features</h2>
                <p>Take a look at some of our key features</p>
            </div>

            <div class="row">
                                 <div class="col-lg-4 col-md-6">
                     <div class="single-key">
                         <span class="feature-badge">Premium</span>
                         <i class="fas fa-heartbeat"></i>
                         <h5>Cardiology</h5>
                         <p class="feature-description">Advanced cardiac care with state-of-the-art diagnostic equipment and expert cardiologists.</p>
                     </div>
                 </div>

                 <div class="col-lg-4 col-md-6">
                     <div class="single-key">
                         <span class="feature-badge">Expert</span>
                         <i class="fas fa-ribbon"></i>
                         <h5>Orthopaedic</h5>
                         <p class="feature-description">Comprehensive bone and joint care with modern surgical techniques and rehabilitation.</p>
                     </div>
                 </div>

                 <div class="col-lg-4 col-md-6">
                     <div class="single-key">
                        <span class="feature-badge">Specialized</span>
                         <i class="fab fa-monero"></i>
                         <h5>Neurology</h5>
                         <p class="feature-description">Advanced neurological treatments and diagnostics for complex brain and nerve disorders.</p>
                     </div>
                 </div>

                 <div class="col-lg-4 col-md-6">
                     <div class="single-key">
                         <span class="feature-badge">Innovation</span>
                         <i class="fas fa-capsules"></i>
                         <h5>Pharma Pipeline</h5>
                         <p class="feature-description">Cutting-edge pharmaceutical research and development for breakthrough treatments.</p>
                     </div>
                 </div>

                 <div class="col-lg-4 col-md-6">
                     <div class="single-key">
                         <span class="feature-badge">Quality</span>
                         <i class="fas fa-prescription-bottle-alt"></i>
                         <h5>Pharma Team</h5>
                         <p class="feature-description">Experienced pharmaceutical professionals ensuring medication safety and efficacy.</p>
                     </div>
                 </div>

                 <div class="col-lg-4 col-md-6">
                     <div class="single-key">
                         <span class="feature-badge">Excellence</span>
                         <i class="far fa-thumbs-up"></i>
                         <h5>High Quality Treatments</h5>
                         <p class="feature-description">Evidence-based medical practices delivering exceptional patient outcomes and care.</p>
                     </div>
                 </div>
            </div>






        </div>

    </section>
    
    
  
    
    <!--  ************************* About Our Hospital ************************** -->
        
    <section id="about_us" class="about-hospital">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="about-image">
                        <img src="assets/images/why.jpg" alt="Modern Hospital Facility" class="img-fluid rounded-lg">
                        <div class="about-stats">
                            <div class="stat-box">
                                <i class="fas fa-award"></i>
                                <span class="stat-number">25+</span>
                                <span class="stat-text">Years Experience</span>
                            </div>
                            <div class="stat-box">
                                <i class="fas fa-users"></i>
                                <span class="stat-number">1000+</span>
                                <span class="stat-text">Staff Members</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="about-content">
                        <div class="section-badge">About Our Hospital</div>
                                                 <h2 class="about-title">Leading Healthcare Excellence with Compassionate Care</h2>
                         
                         <p class="about-description">We are committed to delivering exceptional healthcare services through advanced medical technology and a dedicated team of professionals. Our patient-centered approach ensures personalized care for every individual.</p>
                        
                        <div class="about-features">
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>State-of-the-art medical equipment</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>24/7 emergency medical services</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Expert medical professionals</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Patient-centered care approach</span>
                            </div>
                        </div>
                        
                        <div class="about-actions">
                            <a href="#services" class="btn btn-about-primary">
                                <i class="fas fa-stethoscope"></i>
                                Our Services
                            </a>
                            <a href="#contact_us" class="btn btn-about-secondary">
                                <i class="fas fa-phone"></i>
                                Contact Us
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>    
    
    
            <!--  ************************* Gallery Starts Here ************************** -->
        <div id="gallery" class="gallery">    
           <div class="container">
              <div class="inner-title">

                <h2>Our Gallery</h2>
                <p>View Our Gallery</p>
            </div>
              <div class="row">
                

        <div class="gallery-filter d-none d-sm-block">
            <button class="btn btn-default filter-button" data-filter="all">All</button>
            <button class="btn btn-default filter-button" data-filter="hdpe">Dental</button>
            <button class="btn btn-default filter-button" data-filter="sprinkle">Cardiology</button>
            <button class="btn btn-default filter-button" data-filter="spray"> Neurology</button>
            <button class="btn btn-default filter-button" data-filter="irrigation">Laboratry</button>
        </div>
        <br/>



                                                   <div class="gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter hdpe">
                  <span class="gallery-badge">Dental Care</span>
                  <img src="assets/images/gallery/gallery_01.jpg" class="img-responsive" alt="Dental Care">
                  <div class="gallery-overlay">
                      <h4>Modern Dental Suite</h4>
                      <p>State-of-the-art dental equipment and comfortable treatment rooms</p>
                  </div>
              </div>

              <div class="gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter sprinkle">
                  <span class="gallery-badge">Cardiology</span>
                  <img src="assets/images/gallery/gallery_02.jpg" class="img-responsive" alt="Cardiology Department">
                  <div class="gallery-overlay">
                      <h4>Cardiac Care Unit</h4>
                      <p>Advanced cardiac monitoring and emergency response facilities</p>
                  </div>
              </div>

              <div class="gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter hdpe">
                  <span class="gallery-badge">Dental Care</span>
                  <img src="assets/images/gallery/gallery_03.jpg" class="img-responsive" alt="Dental Treatment">
                  <div class="gallery-overlay">
                      <h4>Dental Treatment Room</h4>
                      <p>Comprehensive dental services with modern technology</p>
                  </div>
              </div>

              <div class="gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter irrigation">
                  <span class="gallery-badge">Laboratory</span>
                  <img src="assets/images/gallery/gallery_04.jpg" class="img-responsive" alt="Medical Laboratory">
                  <div class="gallery-overlay">
                      <h4>Medical Laboratory</h4>
                      <p>Advanced diagnostic testing and research facilities</p>
                  </div>
              </div>

              <div class="gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter spray">
                  <span class="gallery-badge">Neurology</span>
                  <img src="assets/images/gallery/gallery_05.jpg" class="img-responsive" alt="Neurology Department">
                  <div class="gallery-overlay">
                      <h4>Neurology Department</h4>
                      <p>Specialized neurological care and diagnostic imaging</p>
                  </div>
              </div>

              <div class="gallery_product col-lg-4 col-md-4 col-sm-4 col-xs-6 filter spray">
                  <span class="gallery-badge">Neurology</span>
                  <img src="assets/images/gallery/gallery_06.jpg" class="img-responsive" alt="Neurology Treatment">
                  <div class="gallery-overlay">
                      <h4>Neurology Treatment</h4>
                      <p>Advanced neurological treatments and patient care</p>
                  </div>
              </div>

        </div>
    </div>
       
       
       </div>
        <!-- ######## Gallery End ####### -->
    
    
     <!--  ************************* Contact Us Section ************************** -->
    
    <section id="contact_us" class="contact-section">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">Get In Touch</h2>
                <p class="section-subtitle">Have questions or need to schedule an appointment? We're here to help.</p>
                <div class="title-divider"></div>
            </div>
            
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="contact-form-wrapper">
                        <form method="post" class="modern-contact-form">
                            <div class="form-header">
                                <i class="fas fa-envelope"></i>
                                <h3>Send us a Message</h3>
                                <p>Fill out the form below and we'll get back to you as soon as possible.</p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="fullname">
                                            <i class="fas fa-user"></i>
                                            Full Name
                                        </label>
                                        <input type="text" id="fullname" name="fullname" class="form-control modern-input" placeholder="Enter your full name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="emailid">
                                            <i class="fas fa-envelope"></i>
                                            Email Address
                                        </label>
                                        <input type="email" id="emailid" name="emailid" class="form-control modern-input" placeholder="Enter your email address" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="mobileno">
                                    <i class="fas fa-phone"></i>
                                    Mobile Number
                                </label>
                                <input type="tel" id="mobileno" name="mobileno" class="form-control modern-input" placeholder="Enter your mobile number" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">
                                    <i class="fas fa-comment-alt"></i>
                                    Message
                                </label>
                                <textarea id="description" name="description" rows="5" class="form-control modern-input" placeholder="Tell us how we can help you..." required></textarea>
                            </div>
                            
                            <div class="form-actions">
                                <button type="submit" name="submit" class="btn btn-contact-primary">
                                    <i class="fas fa-paper-plane"></i>
                                    Send Message
                                </button>
                                <div class="contact-info">
                                    <span><i class="fas fa-clock"></i> We typically respond within 24 hours</span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    
    
    
    
    <!-- ################# Footer Starts Here#######################--->


    <footer class="modern-footer">
        <div class="footer-main">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-12">
                        <div class="footer-section">
                            <div class="footer-logo">
                                <div class="footer-logo-main">
                                    <i class="fas fa-hospital-symbol"></i>
                                    <span class="footer-logo-text">HMS</span>
                                </div>
                                <span class="footer-logo-subtitle">Clinical Management System</span>
                            </div>
                            <p class="footer-description">
                                Leading the digital transformation of healthcare with cutting-edge technology solutions. 
                                Our comprehensive Clinical Management System empowers healthcare professionals to deliver 
                                exceptional patient care while optimizing operational efficiency.
                            </p>

                            <div class="footer-social">
                                <a href="#" class="social-link" aria-label="Facebook" title="Follow us on Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="social-link" aria-label="Twitter" title="Follow us on Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-link" aria-label="LinkedIn" title="Connect on LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="social-link" aria-label="Instagram" title="Follow us on Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="social-link" aria-label="YouTube" title="Watch our videos">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 col-sm-6">
                        <div class="footer-section">
                            <h4 class="footer-title">Quick Links</h4>
                            <ul class="footer-links">
                                <li><a href="#about_us"><i class="fas fa-chevron-right"></i> About Hospital</a></li>
                                <li><a href="#services"><i class="fas fa-chevron-right"></i> Medical Services</a></li>
                                <li><a href="hms/doctor-directory.php"><i class="fas fa-chevron-right"></i> Find Doctors</a></li>
                                <li><a href="#gallery"><i class="fas fa-chevron-right"></i> Gallery</a></li>
                                <li><a href="#contact_us"><i class="fas fa-chevron-right"></i> Contact Us</a></li>
                                <li><a href="#logins"><i class="fas fa-chevron-right"></i> Access Portals</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 col-sm-6">
                        <div class="footer-section">
                            <h4 class="footer-title">Portal Access</h4>
                            <ul class="footer-links">
                                <li><a href="hms/user-login.php"><i class="fas fa-user-injured"></i> Patient Portal</a></li>
                                <li><a href="hms/doctor"><i class="fas fa-user-md"></i> Doctor Portal</a></li>
                                <li><a href="hms/admin"><i class="fas fa-user-shield"></i> Admin Portal</a></li>
                                <li><a href="hms/user-login.php"><i class="fas fa-calendar-plus"></i> Book Appointment</a></li>
                                <li><a href="hms/doctor-directory.php"><i class="fas fa-search"></i> Find Doctors</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 col-sm-6">
                        <div class="footer-section">
                            <h4 class="footer-title">Support</h4>
                            <ul class="footer-links">
                                <li><a href="#contact_us"><i class="fas fa-headset"></i> Help Center</a></li>
                                <li><a href="#contact_us"><i class="fas fa-question-circle"></i> FAQs</a></li>
                                <li><a href="#contact_us"><i class="fas fa-tools"></i> System Status</a></li>
                                <li><a href="#contact_us"><i class="fas fa-download"></i> Downloads</a></li>
                                <li><a href="#contact_us"><i class="fas fa-book"></i> Documentation</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-6 col-sm-6">
                        <div class="footer-section">
                            <h4 class="footer-title">Legal</h4>
                            <ul class="footer-links">
                                <li><a href="#" title="Privacy Policy"><i class="fas fa-shield-alt"></i> Privacy Policy</a></li>
                                <li><a href="#" title="Terms of Service"><i class="fas fa-file-contract"></i> Terms of Service</a></li>
                                <li><a href="#" title="Cookie Policy"><i class="fas fa-cookie-bite"></i> Cookie Policy</a></li>
                                <li><a href="#" title="Data Protection"><i class="fas fa-database"></i> Data Protection</a></li>
                                <li><a href="#" title="Accessibility"><i class="fas fa-universal-access"></i> Accessibility</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="footer-bottom">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 col-sm-12">
                        <p class="copyright">
                            &copy; 2025 <strong>HMS - Clinical Management System</strong>. All rights reserved. 
                            Designed for excellence in healthcare delivery.
                        </p>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <div class="footer-bottom-links">
                            <a href="#" title="Privacy Policy">Privacy</a>
                            <a href="#" title="Terms of Service">Terms</a>
                            <a href="#contact_us" title="Support">Support</a>
                            <a href="#" title="Sitemap">Sitemap</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    
    </body>

<script src="assets/js/jquery-3.2.1.min.js"></script>
<script src="assets/js/popper.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/plugins/scroll-nav/js/jquery.easing.min.js"></script>
<script src="assets/plugins/scroll-nav/js/scrolling-nav.js"></script>
<script src="assets/plugins/scroll-fixed/jquery-scrolltofixed-min.js"></script>

<script src="assets/js/script.js"></script>



</html>