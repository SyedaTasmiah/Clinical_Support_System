<?php
session_start();
error_reporting(0);
include('include/config.php');
if(strlen($_SESSION['id'])==0) {
    header('location:logout.php');
    exit();
}

$doctor_id = $_SESSION['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Patient EHR Overview | Doctor Portal</title>
    
    <link href="http://fonts.googleapis.com/css?family=Lato:300,400,400italic,600,700|Raleway:300,400,500,600,700|Crete+Round:400italic" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="vendor/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="vendor/themify-icons/themify-icons.min.css">
    <link href="vendor/animate.css/animate.min.css" rel="stylesheet" media="screen">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.min.css" rel="stylesheet" media="screen">
    <link href="vendor/switchery/switchery.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/plugins.css">
    <link rel="stylesheet" href="assets/css/themes/theme-1.css" id="skin_color" />
    
    <style>
        .patient-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .patient-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .allergy-alert {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 5px 8px;
            border-radius: 3px;
            font-size: 11px;
            margin-right: 5px;
            display: inline-block;
        }
        .condition-badge {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 11px;
            margin-right: 5px;
            display: inline-block;
        }
        .prescription-badge {
            background-color: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 3px 6px;
            border-radius: 3px;
            font-size: 11px;
            margin-right: 5px;
            display: inline-block;
        }
        .stats-card {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .search-box {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div id="app">
        <?php include('include/sidebar.php');?>
        <div class="app-content">
            <?php include('include/header.php');?>
            <div class="main-content">
                <div class="wrap-content container" id="container">
                    <!-- PAGE TITLE -->
                    <section id="page-title">
                        <div class="row">
                            <div class="col-sm-8">
                                <h1 class="mainTitle"><i class="fa fa-users"></i> Patient EHR Overview</h1>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Doctor</span></li>
                                <li><span>EHR</span></li>
                                <li class="active"><span>Patient Overview</span></li>
                            </ol>
                        </div>
                    </section>

                    <div class="container-fluid container-fullw bg-white">
                        <!-- STATISTICS -->
                        <div class="row">
                            <?php
                            // Get statistics
                            $total_patients = mysqli_query($con, "
                                SELECT COUNT(DISTINCT patient_id) as count 
                                FROM medical_consultations 
                                WHERE doctor_id = '$doctor_id'
                            ");
                            $patients_count = mysqli_fetch_array($total_patients)['count'];

                            $total_consultations = mysqli_query($con, "
                                SELECT COUNT(*) as count 
                                FROM medical_consultations 
                                WHERE doctor_id = '$doctor_id'
                            ");
                            $consultations_count = mysqli_fetch_array($total_consultations)['count'];

                            $total_prescriptions = mysqli_query($con, "
                                SELECT COUNT(*) as count 
                                FROM prescriptions 
                                WHERE doctor_id = '$doctor_id'
                            ");
                            $prescriptions_count = mysqli_fetch_array($total_prescriptions)['count'];

                            $active_prescriptions = mysqli_query($con, "
                                SELECT COUNT(*) as count 
                                FROM prescriptions 
                                WHERE doctor_id = '$doctor_id' AND status = 'Active'
                            ");
                            $active_prescriptions_count = mysqli_fetch_array($active_prescriptions)['count'];
                            ?>
                            <div class="col-md-3">
                                <div class="stats-card">
                                    <h3 class="text-primary"><?php echo $patients_count; ?></h3>
                                    <p>Patients Treated</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card">
                                    <h3 class="text-info"><?php echo $consultations_count; ?></h3>
                                    <p>Total Consultations</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card">
                                    <h3 class="text-success"><?php echo $prescriptions_count; ?></h3>
                                    <p>Total Prescriptions</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stats-card">
                                    <h3 class="text-warning"><?php echo $active_prescriptions_count; ?></h3>
                                    <p>Active Prescriptions</p>
                                </div>
                            </div>
                        </div>

                        <!-- SEARCH -->
                        <div class="search-box">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" id="patientSearch" placeholder="Search patients by name or email..." onkeyup="filterPatients()">
                                </div>
                                <div class="col-md-3">
                                    <select class="form-control" id="conditionFilter" onchange="filterPatients()">
                                        <option value="">Filter by Condition</option>
                                        <?php
                                        $conditions = mysqli_query($con, "
                                            SELECT DISTINCT condition_name 
                                            FROM patient_conditions pc
                                            JOIN medical_consultations mc ON pc.patient_id = mc.patient_id
                                            WHERE mc.doctor_id = '$doctor_id' AND pc.status = 'Active'
                                            ORDER BY condition_name
                                        ");
                                        while($condition = mysqli_fetch_array($conditions)) {
                                            echo '<option value="' . htmlentities($condition['condition_name']) . '">' . htmlentities($condition['condition_name']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-primary" onclick="clearFilters()">
                                        <i class="fa fa-refresh"></i> Clear Filters
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- PATIENTS LIST -->
                        <div class="row">
                            <div class="col-md-12">
                                <div id="patientsContainer">
                                    <?php
                                    $patients_query = mysqli_query($con, "
                                        SELECT DISTINCT u.id, u.fullname, u.email, u.gender, u.address, u.city,
                                               COUNT(DISTINCT mc.id) as consultation_count,
                                               COUNT(DISTINCT p.id) as prescription_count,
                                               MAX(mc.consultation_date) as last_visit
                                        FROM users u
                                        JOIN medical_consultations mc ON u.id = mc.patient_id
                                        LEFT JOIN prescriptions p ON u.id = p.patient_id AND p.doctor_id = '$doctor_id'
                                        WHERE mc.doctor_id = '$doctor_id'
                                        GROUP BY u.id
                                        ORDER BY last_visit DESC
                                    ");

                                    if(mysqli_num_rows($patients_query) > 0) {
                                        while($patient = mysqli_fetch_array($patients_query)) {
                                    ?>
                                    <div class="patient-card" 
                                         data-patient="<?php echo strtolower($patient['fullname'] . ' ' . $patient['email']); ?>" 
                                         data-conditions="">
                                        
                                        <div class="patient-header">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h5><i class="fa fa-user"></i> <?php echo htmlentities($patient['fullname']); ?></h5>
                                                    <p class="text-muted">
                                                        <?php echo htmlentities($patient['email']); ?> | 
                                                        <?php echo htmlentities($patient['gender']); ?> | 
                                                        <?php echo htmlentities($patient['address'] . ', ' . $patient['city']); ?>
                                                    </p>
                                                </div>
                                                <div class="col-md-4 text-right">
                                                    <p><strong>Last Visit:</strong> <?php echo date('M d, Y', strtotime($patient['last_visit'])); ?></p>
                                                    <p>
                                                        <span class="label label-info"><?php echo $patient['consultation_count']; ?> Consultations</span>
                                                        <span class="label label-success"><?php echo $patient['prescription_count']; ?> Prescriptions</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <h6><i class="fa fa-exclamation-triangle text-danger"></i> Active Allergies</h6>
                                                <?php
                                                $allergies = mysqli_query($con, "
                                                    SELECT allergy_name, severity 
                                                    FROM patient_allergies 
                                                    WHERE patient_id = '{$patient['id']}' AND is_active = 1 
                                                    ORDER BY severity DESC
                                                    LIMIT 3
                                                ");
                                                if(mysqli_num_rows($allergies) > 0) {
                                                    while($allergy = mysqli_fetch_array($allergies)) {
                                                        echo '<span class="allergy-alert">' . htmlentities($allergy['allergy_name']) . ' (' . $allergy['severity'] . ')</span>';
                                                    }
                                                } else {
                                                    echo '<small class="text-muted">No known allergies</small>';
                                                }
                                                ?>
                                            </div>
                                            <div class="col-md-4">
                                                <h6><i class="fa fa-heartbeat text-warning"></i> Active Conditions</h6>
                                                <?php
                                                $conditions = mysqli_query($con, "
                                                    SELECT condition_name 
                                                    FROM patient_conditions 
                                                    WHERE patient_id = '{$patient['id']}' AND status = 'Active' 
                                                    ORDER BY diagnosed_date DESC
                                                    LIMIT 3
                                                ");
                                                $condition_names = [];
                                                if(mysqli_num_rows($conditions) > 0) {
                                                    while($condition = mysqli_fetch_array($conditions)) {
                                                        $condition_names[] = $condition['condition_name'];
                                                        echo '<span class="condition-badge">' . htmlentities($condition['condition_name']) . '</span>';
                                                    }
                                                } else {
                                                    echo '<small class="text-muted">No active conditions</small>';
                                                }
                                                
                                                // Update the data-conditions attribute for filtering
                                                echo '<script>document.querySelector("[data-patient*=\'' . strtolower($patient['fullname']) . '\']").setAttribute("data-conditions", "' . strtolower(implode(' ', $condition_names)) . '");</script>';
                                                ?>
                                            </div>
                                            <div class="col-md-4">
                                                <h6><i class="fa fa-pills text-success"></i> Active Prescriptions</h6>
                                                <?php
                                                $active_rx = mysqli_query($con, "
                                                    SELECT medication_name 
                                                    FROM prescriptions 
                                                    WHERE patient_id = '{$patient['id']}' AND doctor_id = '$doctor_id' AND status = 'Active' 
                                                    ORDER BY prescribed_date DESC
                                                    LIMIT 3
                                                ");
                                                if(mysqli_num_rows($active_rx) > 0) {
                                                    while($rx = mysqli_fetch_array($active_rx)) {
                                                        echo '<span class="prescription-badge">' . htmlentities($rx['medication_name']) . '</span>';
                                                    }
                                                } else {
                                                    echo '<small class="text-muted">No active prescriptions</small>';
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <!-- ACTION BUTTONS -->
                                        <div class="row" style="margin-top: 15px;">
                                            <div class="col-md-12 text-right">
                                                <a href="patient-ehr.php?patient_id=<?php echo $patient['id']; ?>" class="btn btn-primary btn-sm">
                                                    <i class="fa fa-edit"></i> Update EHR
                                                </a>
                                                <a href="#" onclick="viewPatientSummary(<?php echo $patient['id']; ?>)" class="btn btn-info btn-sm">
                                                    <i class="fa fa-eye"></i> View Summary
                                                </a>
                                                <a href="export-patient-history.php?patient_id=<?php echo $patient['id']; ?>" class="btn btn-success btn-sm">
                                                    <i class="fa fa-download"></i> Export History
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php 
                                        }
                                    } else {
                                        echo '<div class="alert alert-info"><i class="fa fa-info-circle"></i> No patient EHR records found. Start by updating patient medical history during appointments.</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- FOOTER -->
        <?php include('include/footer.php');?>
        
        <!-- SETTINGS -->
        <?php include('include/setting.php');?>
    </div>

    <!-- JAVASCRIPTS -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/modernizr/modernizr.js"></script>
    <script src="vendor/jquery-cookie/jquery.cookie.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="vendor/switchery/switchery.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/form-elements.js"></script>
    <script>
        function filterPatients() {
            const patientSearch = document.getElementById('patientSearch').value.toLowerCase();
            const conditionFilter = document.getElementById('conditionFilter').value.toLowerCase();
            const cards = document.querySelectorAll('.patient-card');
            
            cards.forEach(card => {
                const patient = card.getAttribute('data-patient');
                const conditions = card.getAttribute('data-conditions');
                
                const patientMatch = !patientSearch || patient.includes(patientSearch);
                const conditionMatch = !conditionFilter || conditions.includes(conditionFilter);
                
                if (patientMatch && conditionMatch) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        function clearFilters() {
            document.getElementById('patientSearch').value = '';
            document.getElementById('conditionFilter').value = '';
            filterPatients();
        }
        
        function viewPatientSummary(patientId) {
            // Open patient EHR in a new tab
            window.open('patient-ehr.php?patient_id=' + patientId, '_self');
        }
        
        jQuery(document).ready(function() {
            Main.init();
            FormElements.init();
        });
    </script>
</body>
</html>
