<?php
// Checks if the user is logged in by verifying the session.
$loggedIn = isset($_SESSION['user']);

// Fetches all resume sections and returns them as key-value pairs.
$sectionsStmt = $pdo->query("SELECT section, value FROM resume_sections");
$sectionsData = $sectionsStmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Decodes each section's JSON data so it's usable as arrays.
$hero = isset($sectionsData['hero']) ? json_decode($sectionsData['hero'], true) : [];
$stats = isset($sectionsData['stats']) ? json_decode($sectionsData['stats'], true) : [];
$about = isset($sectionsData['about']) ? json_decode($sectionsData['about'], true) : [];
$personality = isset($sectionsData['personality']) ? json_decode($sectionsData['personality'], true) : [];
$tech = isset($sectionsData['tech']) ? json_decode($sectionsData['tech'], true) : [];
$education = isset($sectionsData['education']) ? json_decode($sectionsData['education'], true) : [];

// Ensures the social list always exists to prevent UI errors.
if (!isset($hero['social']) || !is_array($hero['social'])) {
    $hero['social'] = ['facebook', 'twitter', 'linkedin'];
}

// Loads the cover section and extracts the image path.
$coverSection = $sectionsData['cover'] ?? '{}';
$coverData = json_decode($coverSection, true);
$cover = $coverData['image'] ?? '';

// Converts local image paths into proper relative URLs.
if ($cover) {
    // Only process if it's not already a full URL
    if (!str_starts_with($cover, 'http://') && !str_starts_with($cover, 'https://')) {
        // Remove any leading ./ to normalize the path
        $cover = ltrim($cover, './');
        // Ensure it starts with uploads/ (no leading slash)
        if (!str_starts_with($cover, 'uploads/')) {
            $cover = 'uploads/' . basename($cover);
        }
    }
}
// Fetches all project entries sorted by newest first.
$projectsStmt = $pdo->query("SELECT * FROM projects WHERE type='Project' ORDER BY date_added DESC");
$projects = $projectsStmt->fetchAll();

// Fetches all certification entries sorted by newest first.
$certStmt = $pdo->query("SELECT * FROM projects WHERE type='Certification' ORDER BY date_added DESC");
$certifications = $certStmt->fetchAll();
?>