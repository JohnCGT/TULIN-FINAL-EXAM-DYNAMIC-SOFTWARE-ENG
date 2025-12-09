<?php 
// Starts a session if it’s not active yet — needed for user login tracking.
if (session_status() === PHP_SESSION_NONE) session_start();

// Sends a JSON response to the client and stops the script immediately.
function respond($data){
    echo json_encode($data);
    exit;
}

// Checks if the user is logged in; otherwise returns an unauthorized response.
function require_auth(){
    if(!isset($_SESSION['user'])) respond(['success'=>false,'message'=>'Unauthorized']);
}

// Fetches a specific resume section using a secure prepared database query.
function fetch_section(PDO $pdo, string $section){
    $stmt = $pdo->prepare("SELECT * FROM resume_sections WHERE section=? LIMIT 1");
    $stmt->execute([$section]);
    return $stmt->fetch();
}

// Saves or updates a resume section by storing the data as JSON in the database.
function update_section(PDO $pdo, string $section, $data){
    $json = json_encode($data);
    $stmt = $pdo->prepare("INSERT INTO resume_sections(section,value) VALUES(?,?) ON DUPLICATE KEY UPDATE value=?");
    return $stmt->execute([$section, $json, $json]);
}

// Handles file uploads by ensuring the folder exists and saving the file with a unique timestamp.
function handle_file_upload(array $file, string $uploadDir = 'uploads/'){
    if(!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    $filename = time().'_'.basename($file['name']);
    $targetFile = $uploadDir . $filename;
    if(move_uploaded_file($file['tmp_name'], $targetFile)) return $targetFile;
    return false;
}

// Deletes a file if it exists, helping remove old or unused uploads.
function delete_file(string $path){
    if(file_exists($path)) unlink($path);
}
