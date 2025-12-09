<?php

echo "<!-- Cover path: " . htmlspecialchars($cover ?? 'NOT SET') . " -->";

// Defines a predefined list of tech names mapped to Font Awesome icon classes.
$techList = [
    "HTML" => "fa-brands fa-html5",
    "CSS" => "fa-brands fa-css3-alt",
    "JavaScript" => "fa-brands fa-js",
    "PHP" => "fa-brands fa-php",
    "Python" => "fa-brands fa-python",
    "Java" => "fa-brands fa-java",
    "C++" => "fa-solid fa-code",
    "C#" => "fa-solid fa-code",
    "SQL" => "fa-solid fa-database",
    "Laravel" => "fa-brands fa-laravel",
    "React" => "fa-brands fa-react",
    "Vue.js" => "fa-brands fa-vuejs",
    "Node.js" => "fa-brands fa-node",
    "Bootstrap" => "fa-brands fa-bootstrap",
    "TailwindCSS" => "fa-solid fa-wind",
];

// Reads current page, section, and optional ID from the query string.
$page = $_GET['page'] ?? '';
$section = $_GET['section'] ?? '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handles deleting a personality trait by index.
if ($section === 'personality' && isset($_GET['delete'])) {
    $stmt = $pdo->prepare("SELECT * FROM resume_sections WHERE section=? LIMIT 1");
    $stmt->execute(['personality']);
    $sec = $stmt->fetch();
    $value = json_decode($sec['value'] ?? '[]', true);
    
    // Supports both old and new formats for personality traits.
    $traits = isset($value['traits']) ? $value['traits'] : $value;
    if (!is_array($traits)) {
        $traits = [];
    }

    $deleteIndex = intval($_GET['delete']);
    if (isset($traits[$deleteIndex])) {
        unset($traits[$deleteIndex]);
        $traits = array_values($traits);
    }

    $json = json_encode(['traits' => $traits]);
    $stmt = $pdo->prepare("INSERT INTO resume_sections(section,value) VALUES (?,?) ON DUPLICATE KEY UPDATE value=?");
    $stmt->execute(['personality', $json, $json]);

    header("Location: manage.php?page=edit&section=personality");
    exit;
}

// Handles saving personality traits from the form.
if ($section === 'personality' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $traits = $_POST['traits'] ?? [];
    // Cleans up empty traits and trims spacing.
    $traits = array_values(array_filter(array_map('trim', $traits)));
    
    $json = json_encode(['traits' => $traits]);
    $stmt = $pdo->prepare("INSERT INTO resume_sections(section,value) VALUES (?,?) ON DUPLICATE KEY UPDATE value=?");
    $stmt->execute(['personality', $json, $json]);

    header("Location: index.php");
    exit;
}

// Handles deleting a tech stack item by index.
if ($section === 'tech' && isset($_GET['delete'])) {
    $stmt = $pdo->prepare("SELECT * FROM resume_sections WHERE section=? LIMIT 1");
    $stmt->execute(['tech']);
    $sec = $stmt->fetch();
    $value = json_decode($sec['value'] ?? '[]', true);
    
    // Ensures decoded tech data is always an array.
    if (!is_array($value)) {
        $value = [];
    }

    $deleteIndex = intval($_GET['delete']);
    if (isset($value[$deleteIndex])) {
        unset($value[$deleteIndex]);
        $value = array_values($value);
    }

    $json = json_encode($value);
    $stmt = $pdo->prepare("INSERT INTO resume_sections(section,value) VALUES (?,?) ON DUPLICATE KEY UPDATE value=?");
    $stmt->execute(['tech', $json, $json]);

    header("Location: manage.php?page=edit&section=tech");
    exit;
}

// Handles adding or updating a tech stack entry via manual form.
if ($section === 'tech' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tech_action'])) {
    $stmt = $pdo->prepare("SELECT * FROM resume_sections WHERE section=? LIMIT 1");
    $stmt->execute(['tech']);
    $sec = $stmt->fetch();
    $value = json_decode($sec['value'] ?? '[]', true);
    
    // Ensures decoded value is always an array before editing.
    if (!is_array($value)) {
        $value = [];
    }

    $techData = [
        'name' => $_POST['tech_name'] ?? '',
        'icon' => $_POST['tech_icon'] ?? '',
        'bg' => $_POST['tech_bg'] ?? ''
    ];

    if ($_POST['tech_action'] === 'add') {
        $value[] = $techData;
    } elseif ($_POST['tech_action'] === 'update') {
        $editIndex = intval($_POST['edit_index']);
        if (isset($value[$editIndex])) {
            $value[$editIndex] = $techData;
        }
    }

    $json = json_encode($value);
    $stmt = $pdo->prepare("INSERT INTO resume_sections(section,value) VALUES (?,?) ON DUPLICATE KEY UPDATE value=?");
    $stmt->execute(['tech', $json, $json]);

    header("Location: manage.php?page=edit&section=tech");
    exit;
}

// Handles adding tech stack items from the predefined checkbox list.
if ($page === 'add_tech' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT value FROM resume_sections WHERE section='tech' LIMIT 1");
    $stmt->execute();
    $result = $stmt->fetchColumn();
    $techData = $result ? json_decode($result, true) : [];
    
    // Ensures the existing tech data is always an array.
    if (!is_array($techData)) {
        $techData = [];
    }

    $final = $techData;
    if (isset($_POST['tech'])) {
        foreach ($_POST['tech'] as $name => $data) {
            if (!isset($data['checked'])) continue;
            
            // Checks safely if the tech already exists by name.
            $existingNames = is_array($final) && !empty($final) ? array_column($final, "name") : [];
            if (in_array($name, $existingNames)) continue;

            $final[] = [
                "name" => $name,
                "icon" => $data["icon"],
                "bg"   => $data["bg"] ?? "dark"
            ];
        }
    }

    $json = json_encode($final);
    $stmt = $pdo->prepare("INSERT INTO resume_sections (section, value) VALUES ('tech', ?) ON DUPLICATE KEY UPDATE value = ?");
    $stmt->execute([$json, $json]);

    header("Location: index.php");
    exit;
}

// Handles generic section edit form submissions, excluding tech and personality.
if($page === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST' && $section !== 'tech' && $section !== 'personality'){
    $data = $_POST['data'] ?? [];

    // Special handling for cover section image upload.
    if($section === 'cover' && isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK){
        $uploadDir = 'uploads/';
        if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $filename = time().'_'.basename($_FILES['cover_image']['name']);
        $targetFile = $uploadDir . $filename;

        if(move_uploaded_file($_FILES['cover_image']['tmp_name'], $targetFile)){
            $data['image'] = $targetFile; // This should be 'uploads/filename.ext'
        }
    }

    // Converts multiline about fields into arrays of trimmed lines.
    if(in_array($section, ['about'])){
        foreach($data as $k=>$v){
            if(is_string($v) && strpos($v, "\n") !== false){
                $data[$k] = array_filter(array_map('trim', explode("\n", $v)));
            }
        }
    }

    $json = json_encode($data);
    $stmt = $pdo->prepare("INSERT INTO resume_sections(section,value) VALUES(?,?) ON DUPLICATE KEY UPDATE value=?");
    $stmt->execute([$section,$json,$json]);

    header("Location: index.php");
    exit;
}

// Handles adding a new project with optional image upload.
if($page === 'add_project' && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $imagePath = '';

    // Manages the project image upload with basic validation.
    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg','jpeg','png','gif'];

        if(in_array($fileExt, $allowedExts)){
            $uploadDir = 'uploads/projects/';
            if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $newFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/','', $fileName);
            $destPath = $uploadDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $destPath)){
                $imagePath = $destPath;
            } else {
                $error = "Failed to move uploaded file.";
            }
        } else {
            $error = "Invalid file type. Only jpg, jpeg, png, gif allowed.";
        }
    }

    // Validates required project fields.
    if(!$title || !$desc){
        $error = "Title and Description are required.";
    }

    if(empty($error)){
        $stmt = $pdo->prepare("INSERT INTO projects(title,description,type,image,added_by) VALUES(?,?,?,?,?)");
        $stmt->execute([$title,$desc,'Project',$imagePath,$_SESSION['user']['id']]);
        header("Location: index.php");
        exit;
    }
}

// Handles editing an existing project record.
if($page === 'edit_project' && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? ''); // Keep existing image by default

    // Handle new image upload if provided
    if(isset($_FILES['new_image']) && $_FILES['new_image']['error'] === UPLOAD_ERR_OK){
        $fileTmpPath = $_FILES['new_image']['tmp_name'];
        $fileName = $_FILES['new_image']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg','jpeg','png','gif'];

        if(in_array($fileExt, $allowedExts)){
            $uploadDir = 'uploads/projects/';
            if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $newFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/','', $fileName);
            $destPath = $uploadDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $destPath)){
                $image = $destPath; // Use new image
            }
        }
    }

    if($title && $description){
        $stmt = $pdo->prepare("UPDATE projects SET title=?, description=?, image=? WHERE id=?");
        $stmt->execute([$title, $description, $image, $id]);
        header("Location: index.php");
        exit;
    } else {
        $error = "Title and Description are required.";
    }
}

// Handles adding a certification entry with optional image upload.
if($page === 'add_cert' && $_SERVER['REQUEST_METHOD'] === 'POST'){
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $imagePath = '';

    if(isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK){
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedExtensions = ['jpg','jpeg','png','gif'];
        if(in_array($fileExtension, $allowedExtensions)){
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = 'uploads/';
            $destPath = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $destPath)){
                $imagePath = $destPath;
            } else {
                $error = "There was an error moving the uploaded file.";
            }
        } else {
            $error = "Upload failed. Allowed file types: " . implode(',', $allowedExtensions);
        }
    }

    if($title && $desc && empty($error)){
        $stmt = $pdo->prepare("INSERT INTO projects(title, description, type, image, added_by) VALUES(?,?,?,?,?)");
        $stmt->execute([$title, $desc, 'Certification', $imagePath, $_SESSION['user']['id']]);
        header("Location: index.php");
        exit;
    } elseif(!isset($error)){
        $error = "Title and Description are required.";
    }
}

// Fetches data for section edit forms when editing a resume section.
if($page === 'edit' && $section){
    $stmt = $pdo->prepare("SELECT * FROM resume_sections WHERE section=? LIMIT 1");
    $stmt->execute([$section]);
    $sec = $stmt->fetch();

    // Uses different defaults for tech vs other sections and ensures arrays.
    if($section === 'tech') {
        $value = json_decode($sec['value'] ?? '[]', true);
        if (!is_array($value)) {
            $value = [];
        }
    } else {
        $value = json_decode($sec['value'] ?? '{}', true);
        if (!is_array($value)) {
            $value = [];
        }
    }
}

// Loads an existing project when editing by ID.
if($page === 'edit_project' && $id){
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id=? LIMIT 1");
    $stmt->execute([$id]);
    $project = $stmt->fetch();
    if(!$project){
        echo "Project not found.";
        exit;
    }

    $value = $project;
}

// Loads an existing certification for editing
if ($page === 'edit_cert' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id=? AND type='Certification' LIMIT 1");
    $stmt->execute([$id]);
    $cert = $stmt->fetch();
    if (!$cert) {
        echo "Certification not found.";
        exit;
    }
    $value = $cert;
}

// Handles editing an existing certification
if ($page === 'edit_cert' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? ''); // Keep existing image by default

    // Handle new image upload if provided
    if(isset($_FILES['new_image']) && $_FILES['new_image']['error'] === UPLOAD_ERR_OK){
        $fileTmpPath = $_FILES['new_image']['tmp_name'];
        $fileName = $_FILES['new_image']['name'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg','jpeg','png','gif'];

        if(in_array($fileExt, $allowedExts)){
            $uploadDir = 'uploads/';
            if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $newFileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\-\.]/','', $fileName);
            $destPath = $uploadDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $destPath)){
                $image = $destPath; // Use new image
            }
        }
    }

    if ($title && $description) {
        $stmt = $pdo->prepare("UPDATE projects SET title=?, description=?, image=? WHERE id=? AND type='Certification'");
        $stmt->execute([$title, $description, $image, $id]);
        header("Location: index.php");
        exit;
    } else {
        $error = "Title and Description are required.";
    }
}


// Handles adding a tech item directly from the predefined tech list via query param.
if ($section === 'tech' && isset($_GET['add'])) {
    // Fetches existing tech entries for the tech section.
    $stmt = $pdo->prepare("SELECT value FROM resume_sections WHERE section='tech' LIMIT 1");
    $stmt->execute();
    $result = $stmt->fetchColumn();
    
    // Decodes previously saved techs or starts from an empty array.
    $techs = $result ? json_decode($result, true) : [];
    
    // Ensures $techs is always an array before using array_column.
    if (!is_array($techs)) {
        $techs = [];
    }

    // Ensures predefined tech list exists as a fallback.
    if (!isset($techList)) {
        $techList = [
            "HTML" => "fa-brands fa-html5",
            "CSS" => "fa-brands fa-css3-alt",
            "JavaScript" => "fa-brands fa-js",
            "PHP" => "fa-brands fa-php",
            "Python" => "fa-brands fa-python",
            "Java" => "fa-brands fa-java",
            "C++" => "fa-solid fa-code",
            "C#" => "fa-solid fa-code",
            "SQL" => "fa-solid fa-database",
            "Laravel" => "fa-brands fa-laravel",
            "React" => "fa-brands fa-react",
            "Vue.js" => "fa-brands fa-vuejs",
            "Node.js" => "fa-brands fa-node",
            "Bootstrap" => "fa-brands fa-bootstrap",
            "TailwindCSS" => "fa-solid fa-wind",
        ];
    }

    $techName = $_GET['add'];
    
    // Collects existing tech names to avoid duplicates.
    $existingNames = (!empty($techs) && is_array($techs)) ? array_column($techs, 'name') : [];
    
    if (!in_array($techName, $existingNames) && isset($techList[$techName])) {
        $techs[] = [
            'name' => $techName,
            'icon' => $techList[$techName],
            'bg' => count($techs) % 2 === 0 ? 'white' : 'black'
        ];
    }

    $json = json_encode($techs);
    $stmt = $pdo->prepare("INSERT INTO resume_sections(section,value) VALUES('tech',?) ON DUPLICATE KEY UPDATE value=?");
    $stmt->execute([$json, $json]);

    header("Location: manage.php?page=edit&section=tech");
    exit;
}
