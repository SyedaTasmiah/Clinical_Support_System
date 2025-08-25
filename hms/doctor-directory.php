<?php
include('include/config.php');
error_reporting(0);

// Get search parameters
$search_specialization = isset($_GET['specialization']) ? $_GET['specialization'] : '';
$search_name = isset($_GET['name']) ? trim($_GET['name']) : '';
$search_degree = isset($_GET['degree']) ? trim($_GET['degree']) : '';
$min_experience = isset($_GET['min_experience']) ? intval($_GET['min_experience']) : 0;
$search_language = isset($_GET['language']) ? trim($_GET['language']) : '';

// Build the query with filters
$where_conditions = [];
$query_params = [];

if (!empty($search_specialization)) {
    $where_conditions[] = "d.specilization = ?";
    $query_params[] = $search_specialization;
}

if (!empty($search_name)) {
    $where_conditions[] = "d.doctorName LIKE ?";
    $query_params[] = '%' . $search_name . '%';
}

if (!empty($search_degree)) {
    $where_conditions[] = "d.degrees LIKE ?";
    $query_params[] = '%' . $search_degree . '%';
}

if ($min_experience > 0) {
    $where_conditions[] = "d.years_of_experience >= ?";
    $query_params[] = $min_experience;
}

if (!empty($search_language)) {
    $where_conditions[] = "d.languages_spoken LIKE ?";
    $query_params[] = '%' . $search_language . '%';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get all specializations for the filter dropdown
$specializations_query = "SELECT DISTINCT specilization FROM doctorspecilization ORDER BY specilization";
$specializations_result = mysqli_query($con, $specializations_query);

// Build the final query with proper filtering
$doctors_query = "SELECT d.* FROM doctors d $where_clause ORDER BY d.specilization, d.doctorName";

// Execute query with proper parameter binding if there are conditions
if (!empty($where_conditions)) {
    $stmt = mysqli_prepare($con, $doctors_query);
    if ($stmt) {
        // Create types string for bind_param
        $types = str_repeat('s', count($query_params));
        mysqli_stmt_bind_param($stmt, $types, ...$query_params);
        mysqli_stmt_execute($stmt);
        $doctors_result = mysqli_stmt_get_result($stmt);
    } else {
        // Fallback to simple query if prepare fails
        $doctors_result = mysqli_query($con, "SELECT d.* FROM doctors d ORDER BY d.specilization, d.doctorName");
    }
} else {
    // No filters, get all doctors
    $doctors_result = mysqli_query($con, $doctors_query);
}

// Group doctors by specialization
$doctors_by_spec = [];
if ($doctors_result) {
    while ($doctor = mysqli_fetch_array($doctors_result)) {
        $spec = $doctor['specilization'] ? $doctor['specilization'] : 'General';
        if (!isset($doctors_by_spec[$spec])) {
            $doctors_by_spec[$spec] = [];
        }
        $doctors_by_spec[$spec][] = $doctor;
    }
}

// Get total counts
$total_doctors = 0;
foreach ($doctors_by_spec as $doctors) {
    $total_doctors += count($doctors);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Directory - Clinical Support System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .directory-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
            margin-bottom: 30px;
        }
        
        .directory-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .search-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .stats-bar {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        
        .stat-item {
            text-align: center;
            padding: 15px;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
            display: block;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .specialization-card {
            background: white;
            border-radius: 15px;
            margin-bottom: 30px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }
        
        .specialization-title {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
            margin: 0;
        }
        
        .specialization-title h3 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 600;
        }
        
        .doctors-grid {
            padding: 25px;
        }
        
        .doctor-card {
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            background: white;
        }
        
        .doctor-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: #667eea;
        }
        
        .doctor-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e9ecef;
        }
        
        .doctor-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
        }
        
        .doctor-specialization {
            color: #667eea;
            font-weight: 500;
            margin-bottom: 10px;
        }
        
        .doctor-info {
            font-size: 0.9rem;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .doctor-tags {
            margin-top: 15px;
        }
        
        .tag {
            display: inline-block;
            background: #e9ecef;
            color: #495057;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        .tag.experience {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .tag.degree {
            background: #d4edda;
            color: #155724;
        }
        
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .no-results i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #dee2e6;
        }
        
        .btn-view-profile {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            color: white;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }
        
        .btn-view-profile:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            color: white;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 10px 15px;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
        }
        
        .btn-outline-secondary {
            border-radius: 8px;
            padding: 10px 25px;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <div class="directory-header">
        <div class="container">
            <h1><i class="fa fa-user-md"></i> Doctor Directory</h1>
            <p class="lead">Find the right specialist for your healthcare needs</p>
            <p>Explore our complete network of qualified healthcare professionals</p>
            <?php if (empty($_GET)): ?>
                <div style="margin-top: 20px;">
                    <span class="badge" style="background: rgba(255,255,255,0.2); padding: 8px 15px; font-size: 16px;">
                        Showing All Doctors - No Login Required
                    </span>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <!-- Search Section -->
        <div class="search-section">
            <form method="GET" action="">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="fa fa-stethoscope"></i> Specialization</label>
                        <select name="specialization" class="form-select">
                            <option value="">All Specializations</option>
                            <?php
                            if ($specializations_result) {
                                while ($spec = mysqli_fetch_array($specializations_result)) {
                                    $selected = ($search_specialization == $spec['specilization']) ? 'selected' : '';
                                    echo "<option value='" . htmlentities($spec['specilization']) . "' $selected>" . htmlentities($spec['specilization']) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label"><i class="fa fa-user"></i> Doctor Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Search by doctor name" value="<?php echo htmlentities($search_name); ?>">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><i class="fa fa-graduation-cap"></i> Degree</label>
                        <input type="text" name="degree" class="form-control" placeholder="e.g., MBBS, MD" value="<?php echo htmlentities($search_degree); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><i class="fa fa-clock"></i> Min. Experience (Years)</label>
                        <select name="min_experience" class="form-select">
                            <option value="0">Any Experience</option>
                            <option value="1" <?php echo ($min_experience == 1) ? 'selected' : ''; ?>>1+ Years</option>
                            <option value="3" <?php echo ($min_experience == 3) ? 'selected' : ''; ?>>3+ Years</option>
                            <option value="5" <?php echo ($min_experience == 5) ? 'selected' : ''; ?>>5+ Years</option>
                            <option value="10" <?php echo ($min_experience == 10) ? 'selected' : ''; ?>>10+ Years</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label"><i class="fa fa-language"></i> Language</label>
                        <input type="text" name="language" class="form-control" placeholder="e.g., English" value="<?php echo htmlentities($search_language); ?>">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fa fa-search"></i> Search Doctors
                        </button>
                        <a href="doctor-directory.php" class="btn btn-outline-secondary">
                            <i class="fa fa-refresh"></i> Clear All
                        </a>
                    </div>
                </div>
                
                <div class="row" style="margin-top: 15px;">
                    <div class="col-md-8">
                        <?php if (!empty($search_specialization) || !empty($search_name) || !empty($search_degree) || $min_experience > 0): ?>
                            <a href="doctor-directory.php" class="btn btn-default btn-sm">
                                <i class="fa fa-times"></i> Clear Filters & Show All Doctors
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4 text-right">
                        <?php if (empty($_GET)): ?>
                            <span class="text-success">
                                <i class="fa fa-check-circle"></i> Showing All Available Doctors
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- Stats Bar -->
        <div class="stats-bar">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo $total_doctors; ?></span>
                        <span class="stat-label">Total Doctors</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <span class="stat-number"><?php echo count($doctors_by_spec); ?></span>
                        <span class="stat-label">Specializations</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <span class="stat-number">24/7</span>
                        <span class="stat-label">Healthcare Support</span>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <span class="stat-number">Free</span>
                        <span class="stat-label">Consultation Booking</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results -->
        <?php if (empty($doctors_by_spec)): ?>
            <div class="no-results">
                <i class="fa fa-search"></i>
                <h3>No doctors found</h3>
                <?php if (!empty($_GET)): ?>
                    <p>Try adjusting your search criteria or <a href="doctor-directory.php">view all doctors</a></p>
                <?php else: ?>
                    <p>No doctors are currently registered in the system.</p>
                    <p><a href="test-doctor-directory.php">Click here to run system diagnostics</a></p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($doctors_by_spec as $specialization => $doctors): ?>
                <div class="specialization-card">
                    <div class="specialization-title">
                        <h3><i class="fa fa-stethoscope"></i> <?php echo htmlentities($specialization); ?></h3>
                        <small><?php echo count($doctors); ?> doctor(s) available</small>
                    </div>
                    
                    <div class="doctors-grid">
                        <div class="row">
                            <?php foreach ($doctors as $doctor): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="doctor-card">
                                        <div class="d-flex align-items-start mb-3">
                                            <img src="<?php echo !empty($doctor['profile_picture']) ? 'doctor/uploads/profile_pictures/doctors/' . $doctor['profile_picture'] : 'doctor/assets/images/media-user.png'; ?>" 
                                                 alt="Dr. <?php echo htmlentities($doctor['doctorName']); ?>" 
                                                 class="doctor-avatar me-3">
                                            <div class="flex-grow-1">
                                                <div class="doctor-name"><?php echo htmlentities($doctor['doctorName']); ?></div>
                                                <div class="doctor-specialization"><?php echo htmlentities($doctor['specilization']); ?></div>
                                            </div>
                                        </div>
                                        
                                        <div class="doctor-info">
                                            <i class="fa fa-envelope"></i> <?php echo htmlentities($doctor['docEmail']); ?>
                                        </div>
                                        
                                        <?php if (!empty($doctor['years_of_experience'])): ?>
                                            <div class="doctor-info">
                                                <i class="fa fa-clock"></i> <?php echo $doctor['years_of_experience']; ?> years experience
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($doctor['consultation_fee_range'])): ?>
                                            <div class="doctor-info">
                                                <i class="fa fa-money"></i> Consultation Fee: <?php echo htmlentities($doctor['consultation_fee_range']); ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="doctor-tags">
                                            <?php if (!empty($doctor['years_of_experience'])): ?>
                                                <span class="tag experience"><?php echo $doctor['years_of_experience']; ?> yrs exp</span>
                                            <?php endif; ?>
                                            
                                            <?php 
                                            if (!empty($doctor['degrees'])) {
                                                $degrees = json_decode($doctor['degrees'], true);
                                                if (is_array($degrees)) {
                                                    foreach (array_slice($degrees, 0, 2) as $degree) {
                                                        echo '<span class="tag degree">' . htmlentities($degree) . '</span>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </div>
                                        
                                        <div class="mt-3">
                                            <a href="doctor-profile.php?id=<?php echo $doctor['id']; ?>" class="btn-view-profile">
                                                <i class="fa fa-user"></i> View Full Profile
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Back to Home -->
        <div class="text-center mb-5">
            <a href="../index.php" class="btn btn-outline-primary">
                <i class="fa fa-home"></i> Back to Home
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
