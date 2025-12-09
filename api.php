<?php
// Forces all responses from this file to be JSON-formatted.
header('Content-Type: application/json');

require_once __DIR__ . '/core/dbConfig.php';   // Loads database connection.
require_once __DIR__ . '/core/functions.php';  // Loads helper functions (respond, auth, file tools).

// Reads raw JSON request body and converts it into an array.
$input = json_decode(file_get_contents('php://input'), true) ?: [];

// Identifies which action the client wants to perform.
$action = $input['action'] ?? '';

switch($action){

    // --- Authentication ---
    case 'login':
        // Extracts login credentials and performs basic validation.
        $username = trim($input['username'] ?? '');
        $password = $input['password'] ?? '';
        if(!$username || !$password) respond(['success'=>false,'message'=>'All fields required']);

        // Retrieves user by username for login check.
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username=?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        // Verifies password, account status, and creates a session.
        if($user && $user['suspended']==0 && password_verify($password,$user['password'])){
            $_SESSION['user'] = [
                'id'=>$user['id'],
                'username'=>$user['username'],
                'firstname'=>$user['firstname'],
                'lastname'=>$user['lastname'],
                'is_admin'=>(int)$user['is_admin'],
                'is_super'=>(int)$user['is_super'],
                'suspended'=>(int)$user['suspended'],
            ];
            respond(['success'=>true]);
        }
        respond(['success'=>false,'message'=>'Invalid credentials']);
        break;

    case 'register':
        // Collects and validates required registration data.
        $username = trim($input['username'] ?? '');
        $firstname = trim($input['firstname'] ?? '');
        $lastname = trim($input['lastname'] ?? '');
        $password = $input['password'] ?? '';
        $confirm = $input['confirm'] ?? '';

        if(!$username || !$firstname || !$lastname || !$password || !$confirm)
            respond(['success'=>false,'message'=>'All fields are required']);
        if($password !== $confirm) respond(['success'=>false,'message'=>'Passwords do not match']);

        // Ensures the username is unique before creating account.
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username=?");
        $stmt->execute([$username]);
        if($stmt->fetch()) respond(['success'=>false,'message'=>'Username exists']);

        // Saves new user with hashed password for security.
        $hash = password_hash($password,PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users(username,firstname,lastname,password) VALUES(?,?,?,?)");
        $success = $stmt->execute([$username,$firstname,$lastname,$hash]);
        respond(['success'=>$success,'message'=>$success?'Registered':'Failed']);
        break;

    case 'logout':
        // Ends session to fully log out the user.
        session_destroy();
        respond(['success'=>true]);
        break;

    // --- Sections (cover, hero, stats, about, personality, tech, education) ---
    case preg_match('/^(get|update)_(cover|hero|stats|about|personality|tech|education)$/', $action, $matches) ? true : false:

        $operation = $matches[1]; // Extracts operation name (get/update).
        $section = $matches[2];   // Extracts section to operate on.

        // Loads a section’s data from DB and returns it.
        if($operation === 'get'){
            $stmt = fetch_section($pdo, $section);
            respond(['success'=>true,'data'=>$stmt]);
        }

        // Updates a section with new JSON data.
        if($operation === 'update'){
            require_auth(); // Ensures only authenticated users can edit.
            $data = $input['data'] ?? [];

            // Cover is special since uploads are passed as base64 or path.
            if($section === 'cover' && !empty($input['image'])){
                $data = ['image' => $input['image']];
            }

            $success = update_section($pdo, $section, $data);
            respond(['success'=>$success]);
        }
        break;

    // --- Projects / Certifications ---
    case preg_match('/^(add|update|delete)_(project|cert)$/', $action, $matches) ? true : false:
        require_auth(); // Restricts all project operations to logged-in users.

        $operation = $matches[1];                     // add/update/delete
        $entityType = $matches[2] === 'project' 
                        ? 'Project' 
                        : 'Certification';           // Maps cert/project to DB type.

        // Creates a new project or certification record.
        if($operation === 'add'){
            $title = trim($input['title'] ?? '');
            $desc  = trim($input['description'] ?? '');
            $image = trim($input['image'] ?? '');
            if(!$title || !$desc) respond(['success'=>false,'message'=>'Title & Description required']);

            $stmt = $pdo->prepare("INSERT INTO projects(title,description,type,image,added_by) VALUES(?,?,?,?,?)");
            $success = $stmt->execute([$title,$desc,$entityType,$image,$_SESSION['user']['id']]);
            respond(['success'=>$success]);
        }

        // Updates an existing project/cert by ID.
        if($operation === 'update'){
            $id = (int)($input['id'] ?? 0);
            $title = trim($input['title'] ?? '');
            $desc = trim($input['description'] ?? '');
            $image = trim($input['image'] ?? '');
            if(!$id || !$title || !$desc) respond(['success'=>false,'message'=>'Required fields missing']);

            $stmt = $pdo->prepare("UPDATE projects SET title=?,description=?,image=? WHERE id=?");
            $success = $stmt->execute([$title,$desc,$image,$id]);
            respond(['success'=>$success]);
        }

        // Deletes a project/cert and removes associated image file.
        if($operation === 'delete'){
            $id = (int)($input['id'] ?? 0);
            if(!$id) respond(['success'=>false,'message'=>'Invalid ID']);

            // Fetches image path to delete after DB removal.
            $stmt = $pdo->prepare("SELECT image FROM projects WHERE id=?");
            $stmt->execute([$id]);
            $row = $stmt->fetch();
            if(!$row) respond(['success'=>false,'message'=>'Not found']);

            // Performs database delete operation.
            $stmt = $pdo->prepare("DELETE FROM projects WHERE id=?");
            $success = $stmt->execute([$id]);

            // Deletes file only if DB delete succeeded.
            if($success && $row['image']) delete_file($row['image']);

            respond(['success'=>$success]);
        }
        break;

    default:
        // Fallback for unknown or unsupported API actions.
        respond(['success'=>false,'message'=>'Invalid action']);
}
