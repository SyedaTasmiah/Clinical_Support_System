<?php
session_start();
error_reporting(0);
include('include/config.php');
if(strlen($_SESSION['id'])==0) {
    header('location:logout.php');
    exit();
}

$doctor_id = $_SESSION['id'];

// Handle prescription status updates
if(isset($_POST['update_status'])) {
    $prescription_id = $_POST['prescription_id'];
    $new_status = $_POST['new_status'];
    
    $update_sql = "UPDATE prescriptions SET status='$new_status' WHERE id='$prescription_id' AND doctor_id='$doctor_id'";
    if(mysqli_query($con, $update_sql)) {
        echo "<script>alert('Prescription status updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating prescription status.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Doctor | Manage Prescriptions</title>
    
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
        .prescription-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            background-color: #fff;
        }
        .prescription-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .status-active { color: #28a745; font-weight: bold; }
        .status-completed { color: #6c757d; }
        .status-discontinued { color: #dc3545; }
        .status-on-hold { color: #ffc107; }
        .patient-info {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .filter-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
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
                                <h1 class="mainTitle"><i class="fa fa-pills"></i> Manage Prescriptions</h1>
                            </div>
                            <ol class="breadcrumb">
                                <li><span>Doctor</span></li>
                                <li class="active"><span>Prescriptions</span></li>
                            </ol>
                        </div>
                    </section>

                    <div class="container-fluid container-fullw bg-white">
                        <!-- FILTER SECTION -->
                        <div class="filter-section">
                            <div class="row">
                                <div class="col-md-3">
                                    <select class="form-control" id="statusFilter" onchange="filterPrescriptions()">
                                        <option value="">All Statuses</option>
                                        <option value="Active">Active</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Discontinued">Discontinued</option>
                                        <option value="On Hold">On Hold</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="patientSearch" placeholder="Search by patient name..." onkeyup="filterPrescriptions()">
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="medicationSearch" placeholder="Search by medication..." onkeyup="filterPrescriptions()">
                                </div>
                                <div class="col-md-3">
                                    <button class="btn btn-primary" onclick="clearFilters()">
                                        <i class="fa fa-refresh"></i> Clear Filters
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- PRESCRIPTIONS LIST -->
                        <div class="row">
                            <div class="col-md-12">
                                <div id="prescriptionsContainer">
                                    <?php
                                    $prescriptions_query = mysqli_query($con, "
                                        SELECT p.*, u.fullname as patient_name, u.email as patient_email, u.gender 
                                        FROM prescriptions p 
                                        JOIN users u ON p.patient_id = u.id 
                                        WHERE p.doctor_id = '$doctor_id' 
                                        ORDER BY p.prescribed_date DESC, p.status ASC
                                    ");

                                    if(mysqli_num_rows($prescriptions_query) > 0) {
                                        while($prescription = mysqli_fetch_array($prescriptions_query)) {
                                            $status_class = 'status-' . strtolower(str_replace(' ', '-', $prescription['status']));
                                    ?>
                                    <div class="prescription-card" data-status="<?php echo $prescription['status']; ?>" 
                                         data-patient="<?php echo strtolower($prescription['patient_name']); ?>" 
                                         data-medication="<?php echo strtolower($prescription['medication_name']); ?>">
                                        
                                        <div class="prescription-header">
                                            <div class="row">
                                                <div class="col-md-8">
                                                    <h5><i class="fa fa-pill"></i> <?php echo htmlentities($prescription['medication_name']); ?></h5>
                                                    <div class="patient-info">
                                                        <strong>Patient:</strong> <?php echo htmlentities($prescription['patient_name']); ?> 
                                                        (<?php echo htmlentities($prescription['patient_email']); ?>)
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-right">
                                                    <span class="<?php echo $status_class; ?>">
                                                        <i class="fa fa-circle"></i> <?php echo $prescription['status']; ?>
                                                    </span>
                                                    <br><small class="text-muted">Prescribed: <?php echo date('M d, Y', strtotime($prescription['prescribed_date'])); ?></small>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <p><strong>Dosage:</strong> <?php echo htmlentities($prescription['dosage']); ?></p>
                                                <p><strong>Frequency:</strong> <?php echo htmlentities($prescription['frequency']); ?></p>
                                                <p><strong>Duration:</strong> <?php echo htmlentities($prescription['duration']); ?></p>
                                                <?php if($prescription['instructions']) { ?>
                                                <p><strong>Instructions:</strong> <?php echo htmlentities($prescription['instructions']); ?></p>
                                                <?php } ?>
                                            </div>
                                            <div class="col-md-6">
                                                <?php if($prescription['start_date']) { ?>
                                                <p><strong>Start Date:</strong> <?php echo date('M d, Y', strtotime($prescription['start_date'])); ?></p>
                                                <?php } ?>
                                                <?php if($prescription['end_date']) { ?>
                                                <p><strong>End Date:</strong> <?php echo date('M d, Y', strtotime($prescription['end_date'])); ?></p>
                                                <?php } ?>
                                                <?php if($prescription['refills_remaining'] > 0) { ?>
                                                <p><strong>Refills Remaining:</strong> <?php echo $prescription['refills_remaining']; ?></p>
                                                <?php } ?>
                                                
                                                <!-- Status Update Form -->
                                                <form method="post" style="margin-top: 15px;">
                                                    <input type="hidden" name="prescription_id" value="<?php echo $prescription['id']; ?>">
                                                    <div class="input-group">
                                                        <select class="form-control" name="new_status" required>
                                                            <option value="">Change Status...</option>
                                                            <option value="Active" <?php echo ($prescription['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                                                            <option value="Completed" <?php echo ($prescription['status'] == 'Completed') ? 'selected' : ''; ?>>Completed</option>
                                                            <option value="Discontinued" <?php echo ($prescription['status'] == 'Discontinued') ? 'selected' : ''; ?>>Discontinued</option>
                                                            <option value="On Hold" <?php echo ($prescription['status'] == 'On Hold') ? 'selected' : ''; ?>>On Hold</option>
                                                        </select>
                                                        <span class="input-group-btn">
                                                            <button type="submit" name="update_status" class="btn btn-primary">Update</button>
                                                        </span>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <?php 
                                        }
                                    } else {
                                        echo '<div class="alert alert-info"><i class="fa fa-info-circle"></i> No prescriptions found. Start by updating patient medical history to add prescriptions.</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>

                        <!-- STATISTICS -->
                        <div class="row" style="margin-top: 30px;">
                            <div class="col-md-12">
                                <div class="panel panel-white">
                                    <div class="panel-heading">
                                        <h5 class="panel-title"><i class="fa fa-chart-bar"></i> Prescription Statistics</h5>
                                    </div>
                                    <div class="panel-body">
                                        <?php
                                        $stats_query = mysqli_query($con, "
                                            SELECT 
                                                status,
                                                COUNT(*) as count 
                                            FROM prescriptions 
                                            WHERE doctor_id = '$doctor_id' 
                                            GROUP BY status
                                        ");
                                        
                                        $total_prescriptions = 0;
                                        $stats = [];
                                        while($stat = mysqli_fetch_array($stats_query)) {
                                            $stats[$stat['status']] = $stat['count'];
                                            $total_prescriptions += $stat['count'];
                                        }
                                        ?>
                                        <div class="row">
                                            <div class="col-md-3 text-center">
                                                <h4 class="text-primary"><?php echo $total_prescriptions; ?></h4>
                                                <p>Total Prescriptions</p>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <h4 class="text-success"><?php echo isset($stats['Active']) ? $stats['Active'] : 0; ?></h4>
                                                <p>Active</p>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <h4 class="text-secondary"><?php echo isset($stats['Completed']) ? $stats['Completed'] : 0; ?></h4>
                                                <p>Completed</p>
                                            </div>
                                            <div class="col-md-3 text-center">
                                                <h4 class="text-danger"><?php echo isset($stats['Discontinued']) ? $stats['Discontinued'] : 0; ?></h4>
                                                <p>Discontinued</p>
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
        function filterPrescriptions() {
            const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
            const patientSearch = document.getElementById('patientSearch').value.toLowerCase();
            const medicationSearch = document.getElementById('medicationSearch').value.toLowerCase();
            const cards = document.querySelectorAll('.prescription-card');
            
            cards.forEach(card => {
                const status = card.getAttribute('data-status').toLowerCase();
                const patient = card.getAttribute('data-patient');
                const medication = card.getAttribute('data-medication');
                
                const statusMatch = !statusFilter || status === statusFilter;
                const patientMatch = !patientSearch || patient.includes(patientSearch);
                const medicationMatch = !medicationSearch || medication.includes(medicationSearch);
                
                if (statusMatch && patientMatch && medicationMatch) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
        
        function clearFilters() {
            document.getElementById('statusFilter').value = '';
            document.getElementById('patientSearch').value = '';
            document.getElementById('medicationSearch').value = '';
            filterPrescriptions();
        }
        
        jQuery(document).ready(function() {
            Main.init();
            FormElements.init();
        });
    </script>
</body>
</html>
