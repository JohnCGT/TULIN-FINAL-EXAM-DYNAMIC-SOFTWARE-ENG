<?php
// Include database configuration and form handling functions
require_once "core/dbConfig.php";
require_once "core/handleForms.php";

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in, redirect to login page if not
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <!-- Dynamic page title based on current page -->
    <title><?= ucfirst($page) ?></title>
    
    <!-- External CSS dependencies -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 for beautiful alerts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* CSS Custom Properties for consistent theming */
        :root {
            --primary-accent: #212529;
            --card-hover-shadow: 0 10px 20px rgba(0,0,0,0.08);
            --input-bg: #f8f9fa;
        }

        /* Global body styling with Poppins font and light background */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            color: #333;
            min-height: 100vh;
        }

        /* Main card container with rounded corners and subtle shadow */
        .main-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 0 40px rgba(0,0,0,0.05);
            background: #ffffff;
        }

        /* Form input fields with modern styling */
        .form-control {
            background-color: var(--input-bg);
            border: 1px solid transparent;
            border-radius: 12px;
            padding: 12px 15px;
            transition: 0.3s ease;
        }
        
        /* Focus state for form inputs with border and shadow */
        .form-control:focus {
            background-color: #fff;
            border-color: #333;
            box-shadow: 0 0 0 4px rgba(0,0,0,0.05);
        }
        
        /* Label styling with uppercase and letter spacing */
        label {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #666;
            margin-bottom: 8px;
        }

        /* Technology card styling with hover effect */
        .tech-card {
            transition: 0.3s ease;
            border: 1px solid #eee !important;
            border-radius: 15px !important;
            background: #fff;
        }
        
        /* Lift effect on hover for tech cards */
        .tech-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--card-hover-shadow);
        }

        /* Button base styling with rounded pill shape */
        .btn {
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 500;
            transition: 0.3s;
        }
        
        /* Success button with custom green color */
        .btn-success {
            background-color: #198754;
            border: none;
        }
        
        /* Hover effect for success button with shadow and lift */
        .btn-success:hover {
            box-shadow: 0 4px 15px rgba(25, 135, 84, 0.3);
            transform: translateY(-2px);
        }

        /* Custom checkbox sizing for better visibility */
        .form-check-input {
            cursor: pointer;
            width: 1.2em;
            height: 1.2em;
        }
        
        /* Wrapper for social media checkboxes with hover effect */
        .social-checkbox-wrapper {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 10px 15px;
            margin-right: 10px;
            margin-bottom: 10px;
            border: 1px solid transparent;
            transition: 0.2s;
        }
        
        /* Hover state for social checkbox wrapper */
        .social-checkbox-wrapper:hover {
            background: #e9ecef;
        }
    </style>
</head>
<body class="py-5">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="main-card p-5">

                <!-- EDIT SECTION: Display edit form for different portfolio sections -->
                <?php if ($page === 'edit' && $section): ?>
                    <!-- Page header with back button -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="fw-bold display-6 mb-0">Edit <span class="text-primary"><?= ucfirst($section) ?></span></h1>
                        <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-2"></i>Back</a>
                    </div>
                    <hr class="mb-5 opacity-10">

                    <!-- Display error message if any -->
                    <?php if (!empty($error)) echo '<div class="alert alert-danger rounded-4 shadow-sm">' . $error . '</div>'; ?>

                    <!-- Form with conditional enctype for file uploads (cover section) -->
                    <form method="POST" <?= in_array($section, ['cover']) ? 'enctype="multipart/form-data"' : '' ?>>
                        <?php
                        // Switch between different portfolio sections
                        switch ($section):
                            
                            // COVER IMAGE SECTION: Upload and display cover image
                            case 'cover':
                                $img = $value['image'] ?? '';
                        ?>
                            <!-- Display current cover image -->
                            <div class="mb-4 text-center bg-light rounded-4 p-4 border border-dashed">
                                <label class="d-block mb-3">Current Cover Image</label>
                                <?php if ($img): ?>
                                    <img src="<?= htmlspecialchars($img) ?>" class="img-fluid rounded-3 shadow-sm" style="max-height: 300px; object-fit: cover;">
                                <?php else: ?>
                                    <p class="text-muted fst-italic">No image set</p>
                                <?php endif; ?>
                            </div>
                            
                            <!-- File input for uploading new cover image -->
                            <div class="mb-4">
                                <label>Upload New Cover Image</label>
                                <input type="file" name="cover_image" class="form-control">
                            </div>
                        <?php break; ?>

                        <?php 
                        // HERO SECTION: Edit hero banner content (title, subtitle, etc.)
                        case 'hero':
                            // Loop through hero fields
                            foreach (['badge','name','subtitle','school','quote','year'] as $f):
                                $val = $value[$f] ?? '';
                        ?>
                            <div class="mb-4">
                                <label><?= ucfirst($f) ?></label>
                                <!-- Use textarea for title and quote, input for others -->
                                <?php if($f === 'title' || $f === 'quote'): ?>
                                    <textarea name="data[<?= $f ?>]" class="form-control" rows="<?= $f === 'title' ? 3 : 2 ?>"><?= htmlspecialchars($val) ?></textarea>
                                <?php else: ?>
                                    <input name="data[<?= $f ?>]" value="<?= htmlspecialchars($val) ?>" class="form-control">
                                <?php endif; ?>
                            </div>
                        <?php endforeach;

                            // Handle social media data (convert string to array if needed)
                            $socialData = $value['social'] ?? [];
                            if (is_string($socialData)) {
                                $socialData = array_filter(array_map('trim', explode("\n", $socialData)));
                            }
                        ?>
                            <?php
                            // Define available social media options
                            $socialOptions = ['GitHub', 'Messenger', 'Instagram', 'WhatsApp', 'Text Message', 'Twitter'];
                            $selectedSocials = is_array($socialData) ? $socialData : [];
                            ?>
                            
                            <!-- Social media checkboxes for selecting which icons to display -->
                            <div class="mb-4">
                                <label class="d-block mb-3">Social Icons Display</label>
                                <div class="d-flex flex-wrap">
                                <?php foreach ($socialOptions as $option): ?>
                                    <div class="social-checkbox-wrapper form-check form-check-inline d-flex align-items-center">
                                        <input 
                                            class="form-check-input mt-0 me-2" 
                                            type="checkbox" 
                                            name="data[social][]" 
                                            value="<?= $option ?>" 
                                            id="social-<?= $option ?>" 
                                            <?= in_array($option, $selectedSocials) ? 'checked' : '' ?>
                                        >
                                        <label class="form-check-label mb-0 cursor-pointer text-dark text-capitalize" style="letter-spacing: 0; font-size: 1rem;" for="social-<?= $option ?>">
                                            <?= $option ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                                </div>
                            </div>

                        <?php break; ?>

                        <?php 
                        // STATS SECTION: Edit portfolio statistics
                        case 'stats':
                            // Loop through stat fields with their labels
                            foreach (['technologies'=>'Technologies','projects'=>'Projects','certifications'=>'Certifications','ambitions'=>'Ambitions'] as $k => $label):
                                $val = $value[$k] ?? '';
                        ?>
                            <!-- Number input for each stat -->
                            <div class="mb-4">
                                <label><?= $label ?></label>
                                <input type="number" name="data[<?= $k ?>]" value="<?= htmlspecialchars($val) ?>" class="form-control form-control-lg">
                            </div>
                        <?php endforeach; break; ?>

                        <?php 
                        // ABOUT SECTION: Edit about me information
                        case 'about':
                            // Loop through about fields
                            foreach (['strengths','talents','inspiration','fear'] as $f):
                                $val = $value[$f] ?? '';
                                // Convert array to newline-separated string if needed
                                if (is_array($val)) $val = implode("\n", $val);
                        ?>
                            <!-- Textarea for about fields -->
                            <div class="mb-4">
                                <label><?= ucfirst($f) ?></label>
                                <textarea name="data[<?= $f ?>]" class="form-control" rows="3"><?= htmlspecialchars($val) ?></textarea>
                            </div>
                        <?php endforeach;

                        // Handle bucket list data (convert string to array if needed)
                        $bucketData = $value['bucket'] ?? [];
                        if (is_string($bucketData)) {
                            $bucketData = array_filter(array_map('trim', explode("\n", $bucketData)));
                        }
                        ?>
                        
                        <!-- Dynamic bucket list editor with add/remove functionality -->
                        <div class="mb-4">
                            <label><strong>Bucket List</strong></label>
                            <div id="bucket-container">
                                <?php foreach ($bucketData as $item): ?>
                                    <!-- Each bucket item with delete button -->
                                    <div class="input-group mb-3 bucket-row shadow-sm rounded-4 overflow-hidden">
                                        <input type="text" name="data[bucket][]" class="form-control border-0" value="<?= htmlspecialchars($item) ?>" placeholder="Enter bucket item">
                                        <button type="button" class="btn btn-danger px-4 rounded-0" onclick="removeBucket(this)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <!-- Button to add new bucket list item -->
                            <button type="button" class="btn btn-outline-primary mt-2" onclick="addBucket()">
                                <i class="fas fa-plus me-2"></i> Add New Item
                            </button>
                        </div>

                        <script>
                        // JavaScript function to add new bucket list item dynamically
                        function addBucket() {
                            const container = document.getElementById('bucket-container');
                            const newRow = document.createElement('div');
                            newRow.className = 'input-group mb-3 bucket-row shadow-sm rounded-4 overflow-hidden';
                            newRow.innerHTML = `
                                <input type="text" name="data[bucket][]" class="form-control border-0" placeholder="Enter bucket item">
                                <button type="button" class="btn btn-danger px-4 rounded-0" onclick="removeBucket(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            `;
                            container.appendChild(newRow);
                        }

                        // JavaScript function to remove bucket list item
                        function removeBucket(button) {
                            button.closest('.bucket-row').remove();
                        }
                        </script>
                        <?php break; ?>

                        <?php 
                        // PERSONALITY SECTION: Edit personality traits
                        case 'personality':
                            // Handle traits data (check if it's nested or direct)
                            $traitsData = isset($value['traits']) ? $value['traits'] : $value;
                            $traits = is_array($traitsData) ? $traitsData : [];
                        ?>
                            <!-- Dynamic personality traits editor with add/remove functionality -->
                            <div class="mb-4">
                                <label class="form-label"><strong>Personality Traits</strong></label>
                                <div id="traits-container">
                                    <?php foreach ($traits as $i => $trait): ?>
                                        <!-- Each trait with delete button -->
                                        <div class="input-group mb-3 trait-row shadow-sm rounded-4 overflow-hidden">
                                            <input type="text" name="traits[]" class="form-control border-0" 
                                                value="<?= htmlspecialchars($trait) ?>" placeholder="Enter personality trait">
                                            <button type="button" class="btn btn-danger px-4 rounded-0" onclick="removeTrait(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <!-- Button to add new personality trait -->
                                <button type="button" class="btn btn-outline-primary mt-2" onclick="addTrait()">
                                    <i class="fas fa-plus me-2"></i> Add New Trait
                                </button>
                            </div>

                            <script>
                            // JavaScript function to add new personality trait dynamically
                            function addTrait() {
                                const container = document.getElementById('traits-container');
                                const newRow = document.createElement('div');
                                newRow.className = 'input-group mb-3 trait-row shadow-sm rounded-4 overflow-hidden';
                                newRow.innerHTML = `
                                    <input type="text" name="traits[]" class="form-control border-0" placeholder="Enter personality trait">
                                    <button type="button" class="btn btn-danger px-4 rounded-0" onclick="removeTrait(this)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                `;
                                container.appendChild(newRow);
                            }

                            // JavaScript function to remove personality trait
                            function removeTrait(button) {
                                button.closest('.trait-row').remove();
                            }
                            </script>
                        <?php break; ?>

                        <?php 
                        // TECH SECTION: Manage technology skills with visual cards
                        case 'tech':
                            // Initialize tech arrays
                            $techs = is_array($value) ? $value : []; 
                            $techList = $techList ?? [];            
                            $techListKeys = array_keys($techList);
                        ?>
                            <h5 class="fw-bold mb-4">Select Technologies</h5>
                            <div class="row g-3">
                                <?php foreach ($techListKeys as $i => $techName): 
                                    // Get icon for each technology
                                    $icon = $techList[$techName];
                                    $bgColor = 'white';
                                    $textColor = 'black';
                                    // Ensure techs is array and check if technology is already selected
                                    $techs = is_array($techs) ? $techs : [];
                                    $selected = in_array($techName, array_column($techs, 'name'));
                                ?>
                                <div class="col-md-3 col-6">
                                    <!-- Technology card with icon and add/delete buttons -->
                                    <div class="tech-card h-100 p-4 text-center position-relative" style="background-color:<?= $bgColor ?>; color:<?= $textColor ?>;">
                                        <!-- Technology icon -->
                                        <div class="mb-3"><i class="<?= $icon ?>" style="font-size:32px;"></i></div>
                                        <h6 class="fw-bold mb-3"><?= htmlspecialchars($techName) ?></h6>
                                        
                                        <?php if (!$selected): ?>
                                            <!-- Add button if technology not selected -->
                                            <a href="manage.php?page=edit&section=tech&add=<?= urlencode($techName) ?>" class="btn btn-outline-primary btn-sm rounded-pill w-100 stretched-link">Add</a>
                                        <?php else: ?>
                                            <!-- Show badge and delete button if technology already selected -->
                                            <div class="badge bg-success mb-2 px-3 py-2 rounded-pill d-inline-block text-center">Added</div>
                                            <a 
                                                href="javascript:;" 
                                                class="badge bg-danger mb-2 px-3 py-2 rounded-pill d-inline-block text-center text-decoration-none delete-tech"
                                                data-delete-url="manage.php?page=edit&section=tech&delete=<?= array_search($techName, array_column($techs, 'name')) ?>"
                                            >
                                                Delete
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php break; ?>

                        <?php 
                        // EDUCATION SECTION: Edit educational background
                        case 'education':
                            $college = $value['college'] ?? '';
                            $hs      = $value['highschool'] ?? '';
                        ?>
                            <!-- College/University input -->
                            <div class="mb-4">
                                <label>College / University</label>
                                <input name="data[college]" value="<?= htmlspecialchars($college) ?>" class="form-control form-control-lg">
                            </div>
                            <!-- High School input -->
                            <div class="mb-4">
                                <label>High School</label>
                                <input name="data[highschool]" value="<?= htmlspecialchars($hs) ?>" class="form-control form-control-lg">
                            </div>
                        <?php endswitch; ?>

                        <!-- Form action buttons (Save/Cancel for most sections, Done for tech section) -->
                        <div class="mt-5 pt-3 border-top d-flex gap-2">
                            <?php if ($section !== 'tech'): ?>
                                <button type="submit" class="btn btn-success shadow px-4">Save Changes</button>
                                <a href="index.php" class="btn btn-light text-secondary px-4">Cancel</a>
                            <?php else: ?>
                                <!-- Tech section uses "Done Editing" instead of save -->
                                <a href="index.php" class="btn btn-primary shadow px-5">Done Editing</a>
                            <?php endif; ?>
                        </div>
                    </form>
                <?php endif; ?>

                <!-- ADD PROJECT PAGE: Form to add new project -->
                <?php if($page === 'add_project'): ?>
                <h1 class="fw-bold display-6 mb-4">Add New Project</h1>
                
                <!-- Display error message if any -->
                <?php if (!empty($error)) echo '<div class="alert alert-danger rounded-4 shadow-sm">' . $error . '</div>'; ?>

                <!-- Project form with file upload capability -->
                <form method="POST" enctype="multipart/form-data">
                    <!-- Project title input (required) -->
                    <div class="mb-4">
                        <label>Project Title</label>
                        <input type="text" name="title" class="form-control form-control-lg" required>
                    </div>
                    <!-- Project description textarea (required) -->
                    <div class="mb-4">
                        <label>Project Description</label>
                        <textarea name="description" class="form-control" rows="5" required></textarea>
                    </div>
                    <!-- Project image upload (optional) -->
                    <div class="mb-4">
                        <label>Project Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle me-1"></i> Optional: upload an image</small>
                    </div>
                    <!-- Form action buttons -->
                    <div class="mt-5 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-success shadow px-4">Add Project</button>
                        <a href="index.php" class="btn btn-light text-secondary px-4">Cancel</a>
                    </div>
                </form>
                <?php endif; ?>

                <!-- EDIT PROJECT PAGE: Form to edit existing project -->
                <?php if($page === 'edit_project' && isset($value)): ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="fw-bold display-6 mb-0">Edit Project</h1>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-2"></i>Back</a>
                </div>
                <hr class="mb-5 opacity-10">
                
                <!-- Display error message if any -->
                <?php if (!empty($error)) echo '<div class="alert alert-danger rounded-4 shadow-sm">' . $error . '</div>'; ?>

                <!-- Project edit form with file upload capability -->
                <form method="POST" enctype="multipart/form-data">
                    <!-- Display current project image -->
                    <?php if(!empty($value['image'])): ?>
                    <div class="mb-4 text-center bg-light rounded-4 p-4 border border-dashed">
                        <label class="d-block mb-3">Current Project Image</label>
                        <img src="<?= htmlspecialchars($value['image']) ?>" class="img-fluid rounded-3 shadow-sm" style="max-height: 300px; object-fit: cover;">
                    </div>
                    <?php endif; ?>
                    
                    <!-- Project title input (required) -->
                    <div class="mb-4">
                        <label>Project Title</label>
                        <input type="text" name="title" class="form-control form-control-lg" value="<?= htmlspecialchars($value['title'] ?? '') ?>" required>
                    </div>
                    
                    <!-- Project description textarea (required) -->
                    <div class="mb-4">
                        <label>Project Description</label>
                        <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($value['description'] ?? '') ?></textarea>
                    </div>
                    
                    <!-- Current image path (hidden field) -->
                    <input type="hidden" name="image" value="<?= htmlspecialchars($value['image'] ?? '') ?>">
                    
                    <!-- Project image upload (optional - to replace existing) -->
                    <div class="mb-4">
                        <label>Replace Project Image (Optional)</label>
                        <input type="file" name="new_image" class="form-control" accept="image/*">
                        <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle me-1"></i> Leave empty to keep current image</small>
                    </div>
                    
                    <!-- Form action buttons -->
                    <div class="mt-5 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-success shadow px-4">Update Project</button>
                        <a href="index.php" class="btn btn-light text-secondary px-4">Cancel</a>
                    </div>
                </form>
                <?php endif; ?>

                <!-- ADD CERTIFICATION PAGE: Form to add new certification -->
                <?php if($page === 'add_cert'): ?>
                <h1 class="fw-bold display-6 mb-4">Add New Certification</h1>

                <!-- Display error message if any -->
                <?php if (!empty($error)) echo '<div class="alert alert-danger rounded-4 shadow-sm">' . $error . '</div>'; ?>

                <!-- Certification form with file upload capability -->
                <form method="POST" enctype="multipart/form-data">
                    <!-- Certification title input (required) -->
                    <div class="mb-4">
                        <label>Certification Title</label>
                        <input type="text" name="title" class="form-control form-control-lg" required>
                    </div>
                    <!-- Certification description textarea (required) -->
                    <div class="mb-4">
                        <label>Certification Description</label>
                        <textarea name="description" class="form-control" rows="5" required></textarea>
                    </div>
                    <!-- Certification image upload (optional) -->
                    <div class="mb-4">
                        <label>Certification Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle me-1"></i> Optional: upload an image</small>
                    </div>
                    <!-- Form action buttons -->
                    <div class="mt-5 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-success shadow px-4">Add Certification</button>
                        <a href="index.php" class="btn btn-light text-secondary px-4">Cancel</a>
                    </div>
                </form>
                <?php endif; ?>

                <!-- EDIT CERTIFICATION PAGE: Form to edit existing certification -->
                <?php if($page === 'edit_cert' && isset($value)): ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="fw-bold display-6 mb-0">Edit Certification</h1>
                    <a href="index.php" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-2"></i>Back</a>
                </div>
                <hr class="mb-5 opacity-10">

                <!-- Display error message if any -->
                <?php if (!empty($error)) echo '<div class="alert alert-danger rounded-4 shadow-sm">' . $error . '</div>'; ?>

                <!-- Certification edit form with file upload capability -->
                <form method="POST" enctype="multipart/form-data">
                    <!-- Display current certification image -->
                    <?php if(!empty($value['image'])): ?>
                    <div class="mb-4 text-center bg-light rounded-4 p-4 border border-dashed">
                        <label class="d-block mb-3">Current Certification Image</label>
                        <img src="<?= htmlspecialchars($value['image']) ?>" class="img-fluid rounded-3 shadow-sm" style="max-height: 300px; object-fit: cover;">
                    </div>
                    <?php endif; ?>
                    
                    <!-- Certification title input (required) -->
                    <div class="mb-4">
                        <label>Certification Title</label>
                        <input type="text" name="title" class="form-control form-control-lg" value="<?= htmlspecialchars($value['title'] ?? '') ?>" required>
                    </div>
                    
                    <!-- Certification description textarea (required) -->
                    <div class="mb-4">
                        <label>Certification Description</label>
                        <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($value['description'] ?? '') ?></textarea>
                    </div>
                    
                    <!-- Current image path (hidden field) -->
                    <input type="hidden" name="image" value="<?= htmlspecialchars($value['image'] ?? '') ?>">
                    
                    <!-- Certification image upload (optional - to replace existing) -->
                    <div class="mb-4">
                        <label>Replace Certification Image (Optional)</label>
                        <input type="file" name="new_image" class="form-control" accept="image/*">
                        <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle me-1"></i> Leave empty to keep current image</small>
                    </div>
                    
                    <!-- Form action buttons -->
                    <div class="mt-5 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-success shadow px-4">Update Certification</button>
                        <a href="index.php" class="btn btn-light text-secondary px-4">Cancel</a>
                    </div>
                </form>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

    <!-- Include external JavaScript for save functionality -->
    <script src="assets/save.js"></script>
    <!-- Include SweetAlert2 -->
    <script>
    document.querySelectorAll('.delete-tech').forEach(btn => {
        btn.addEventListener('click', function() {
            const url = this.getAttribute('data-delete-url');

            Swal.fire({
                title: 'Are you sure?',
                text: "This tech will be deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });
</script>
</body>
</html>