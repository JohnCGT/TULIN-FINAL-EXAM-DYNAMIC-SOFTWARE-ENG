<?php
require_once "core/dbConfig.php";
require_once "core/fetchData.php";

$socialIconsMap = [
    'GitHub' => 'fab fa-github',
    'Messenger' => 'fab fa-facebook-messenger',
    'Instagram' => 'fab fa-instagram',
    'WhatsApp' => 'fab fa-whatsapp',
    'Text Message' => 'fas fa-comment',
    'Twitter' => 'fab fa-twitter',
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>John Carlo Tulin | Full Stack Web Developer</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>
        :root {
            --primary-accent: #212529; /* Dark/Professional */
            --secondary-bg: #f8f9fa;
            --card-hover-shadow: 0 15px 30px rgba(0,0,0,0.1);
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #ffffff;
            color: #333;
            overflow-x: hidden;
        }

        /* Utilities */
        .text-justify { text-align: justify; }
        .fw-800 { font-weight: 800; }
        .ls-2 { letter-spacing: 2px; }
        
        /* Interactive Elements */
        .hover-lift {
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
        }
        .hover-lift:hover {
            transform: translateY(-8px);
            box-shadow: var(--card-hover-shadow) !important;
        }

        .hover-scale img {
            transition: transform 0.5s ease;
        }
        .hover-scale:hover img {
            transform: scale(1.05);
        }

        /* Custom badge styles */
        .badge-custom {
            padding: 10px 20px;
            border-radius: 50px;
            font-weight: 500;
            letter-spacing: 1px;
        }

        /* Section Styling */
        section { position: relative; }
        
        /* Hero Specifics */
        .hero-title {
            background: linear-gradient(45deg, #000, #555);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Edit Button Floater styling */
        .edit-btn-float {
            z-index: 10;
            opacity: 0.7;
            transition: 0.2s;
        }
        .edit-btn-float:hover { opacity: 1; }

        /* Login Button */
        .auth-btn {
            backdrop-filter: blur(10px);
            background: rgba(255,255,255,0.8);
            border: 1px solid rgba(0,0,0,0.1);
            color: #000;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .auth-btn:hover {
            background: #fff;
            transform: translateY(-2px);
        }

    </style>
</head>
<body>

    <?php if($loggedIn): ?>
        <a href="logout.php" class="btn btn-danger rounded-pill position-fixed top-0 end-0 m-4 px-4 shadow" style="z-index: 1000;">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </a>
    <?php else: ?>
        <a href="login.php" class="btn auth-btn rounded-pill position-fixed top-0 end-0 m-4 px-4" style="z-index: 1000;">
            <i class="fas fa-sign-in-alt me-2"></i> Login
        </a>
    <?php endif; ?>

    <section class="w-100 position-relative shadow-sm" 
        style="height: 65vh; 
               <?= !empty($cover) ? "background: url('" . htmlspecialchars($cover) . "') center/cover no-repeat;" : 'background: #eee;' ?>
               border-bottom-left-radius: 0px; border-bottom-right-radius: 0px;">
        <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(0,0,0,0.1), rgba(0,0,0,0.0));"></div>

        <?php if($loggedIn): ?>
            <a href="manage.php?page=edit&section=cover" class="btn btn-light btn-sm shadow edit-btn-float position-absolute top-0 start-0 m-4 rounded-pill">
                <i class="fas fa-camera me-1"></i> Edit Cover
            </a>
        <?php endif; ?>
    </section>

    <section class="py-5 bg-white position-relative mt-n5" style="z-index: 2; margin-top: -50px; border-radius: 40px 40px 0 0; border-top: 1px solid rgba(0,0,0,0.05);">
        <div class="container py-4">
            <div class="row align-items-center g-5">
                <div class="col-lg-7">
                    <span class="badge bg-dark text-white mb-3 ls-2 px-3 py-2 rounded-1">
                        <?= htmlspecialchars($hero['badge'] ?? 'FULL STACK DEVELOPER') ?>
                    </span>
                    <h1 class="fw-800 display-2 text-dark mb-3 lh-1 hero-title">
                        <?= nl2br(htmlspecialchars($hero['title'] ?? "JOHN\nCARLO\nTULIN")) ?>
                    </h1>
                    
                    <div class="ps-4 border-start border-4 border-dark mt-4">
                        <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($hero['subtitle'] ?? 'Computer Science Student') ?></h4>
                        <p class="text-secondary mb-3 h6"><?= htmlspecialchars($hero['school'] ?? 'Emilio Aguinaldo College Cavite') ?></p>
                        <p class="fst-italic text-muted small mb-0">"<?= htmlspecialchars($hero['quote'] ?? 'When you do good, no one remembers; but when you do bad, everyone forgets.') ?>"</p>
                    </div>

                    <?php if($loggedIn): ?>
                        <div class="mt-4">
                            <a href="manage.php?page=edit&section=hero" class="btn btn-outline-primary btn-sm rounded-pill px-4">Edit Hero</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-5">
                    <div class="card bg-dark text-white border-0 rounded-4 p-5 text-center shadow-lg hover-lift mb-4 position-relative overflow-hidden">
                        <div class="position-absolute top-0 end-0 bg-white opacity-10 rounded-circle" style="width: 150px; height: 150px; margin: -50px;"></div>
                        
                        <h1 class="fw-800 display-1 mb-0" style="font-size: 6rem;"><?= htmlspecialchars($hero['year'] ?? '4TH') ?></h1>
                        <p class="ls-2 mb-0 opacity-75">YEAR STUDENT</p>
                    </div>

                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <?php if(isset($hero['social']) && is_array($hero['social'])): ?>
                            <?php foreach($hero['social'] as $social): ?>
                                <?php if(isset($socialIconsMap[$social])): ?>
                                    <a href="#" class="btn btn-outline-dark rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; transition: 0.3s;">
                                        <i class="<?= $socialIconsMap[$social] ?>"></i>
                                    </a>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <?php 
                $statsLabels = [
                    'technologies' => 'Technologies',
                    'projects' => 'Major Projects',
                    'certifications' => 'Certification',
                    'ambitions' => 'Ambitions'
                ];
                $s_index = 0;
                foreach($statsLabels as $key => $label): 
                ?>
                <div class="col-6 col-md-3">
                    <div class="card border-0 shadow-sm h-100 text-center py-4 rounded-4 hover-lift <?= $s_index % 2 == 1 ? 'bg-white' : 'bg-dark text-white' ?>">
                        <div class="card-body">
                            <h2 class="fw-800 display-5 mb-1"><?= htmlspecialchars($stats[$key] ?? '0') ?></h2>
                            <p class="small text-uppercase ls-2 mb-0 opacity-75"><?= $label ?></p>
                        </div>
                    </div>
                </div>
                <?php 
                $s_index++;
                endforeach; 
                ?>
            </div>
            <?php if($loggedIn): ?>
                <div class="text-center mt-3">
                    <a href="manage.php?page=edit&section=stats" class="btn btn-sm btn-secondary rounded-pill">Edit Stats</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="row align-items-start g-5">
                <div class="col-lg-4">
                    <h1 class="fw-800 display-4 mb-4 text-uppercase">About<br><span class="text-secondary opacity-25">Me.</span></h1>
                    <div class="d-flex flex-column gap-4">
                        <div class="p-4 bg-light rounded-4 border-start border-4 border-dark">
                            <h6 class="fw-bold text-uppercase text-muted small ls-2 mb-2">Strengths</h6>
                            <p class="mb-0 fw-medium"><?= htmlspecialchars($about['strengths'] ?? 'Video & image editing, gaming (Valorant & Overwatch)') ?></p>
                        </div>
                        <div class="p-4 bg-light rounded-4 border-start border-4 border-dark">
                            <h6 class="fw-bold text-uppercase text-muted small ls-2 mb-2">Talents</h6>
                            <p class="mb-0 fw-medium"><?= htmlspecialchars($about['talents'] ?? 'Multi-instrumentalist (playing in a band)') ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-4 mt-lg-5 pt-lg-5">
                        <div class="p-4 bg-light rounded-4 border-start border-4 border-secondary">
                            <h6 class="fw-bold text-uppercase text-muted small ls-2 mb-2">Inspiration</h6>
                            <p class="mb-0 fw-medium"><?= htmlspecialchars($about['inspiration'] ?? 'John Mayer') ?></p>
                        </div>
                        <div class="p-4 bg-dark text-white rounded-4 shadow">
                            <h6 class="fw-bold text-uppercase text-white-50 small ls-2 mb-2">Biggest Fear</h6>
                            <p class="mb-0 fst-italic">"<?= htmlspecialchars($about['fear'] ?? 'Losing someone who loves me the most') ?>"</p>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h3 class="fw-bold mb-4"><i class="fas fa-check-double me-2 text-warning"></i> Bucket List</h3>
                            <ul class="list-group list-group-flush">
                                <?php 
                                $bucketList = $about['bucket'] ?? ['Travel abroad', 'Perform on a well-known platform', 'Get a decent life'];
                                foreach($bucketList as $item): 
                                ?>
                                    <li class="list-group-item bg-transparent border-bottom py-3 px-0 d-flex align-items-center">
                                        <i class="far fa-circle me-3 text-secondary"></i>
                                        <?= htmlspecialchars($item) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if($loggedIn): ?>
                <div class="mt-4">
                    <a href="manage.php?page=edit&section=about" class="btn btn-primary rounded-pill px-4">Edit About Section</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="py-5 bg-dark text-white">
        <div class="container py-4">
            <div class="text-center mb-5">
                <h6 class="text-uppercase ls-2 text-white-50">Who I Am</h6>
                <h2 class="fw-bold display-5">Personality Traits</h2>
            </div>
            
            <div class="d-flex flex-wrap justify-content-center gap-3">
                <?php 
                    $traits = $personality['traits'] ?? $personality ?? ['COMPETITIVE', 'CREATIVE', 'AMBIVERT', 'RESOURCEFUL', 'ADVENTUROUS'];
                    if (!is_array($traits) || empty($traits)) {
                        $traits = ['COMPETITIVE', 'CREATIVE', 'AMBIVERT', 'RESOURCEFUL', 'ADVENTUROUS'];
                    }
                    
                    $t_index = 0;
                    foreach($traits as $trait): 
                        $traitText = is_array($trait) ? ($trait['name'] ?? '') : $trait;
                        if (empty($traitText)) continue;
                    ?>
                        <span class="badge-custom <?= $t_index % 2 == 0 ? 'bg-white text-dark' : 'border border-light text-white' ?> fs-6">
                            <?= htmlspecialchars(strtoupper($traitText)) ?>
                        </span>
                    <?php 
                    $t_index++;
                endforeach; 
                ?>
            </div>
            <?php if($loggedIn): ?>
                <div class="text-center mt-4">
                    <a href="manage.php?page=edit&section=personality" class="btn btn-outline-light btn-sm rounded-pill">Edit Personality</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container py-4">
            <h1 class="fw-800 display-4 mb-5 text-center">Tech Stack</h1>
            <div class="row g-4 justify-content-center">
                <?php foreach($tech ?? [] as $t): ?>
                <div class="col-6 col-md-3 col-lg-2">
                    <div class="card border-0 shadow-sm h-100 text-center py-4 rounded-4 hover-lift bg-white">
                        <div class="card-body">
                            <i class="<?= htmlspecialchars($t['icon']) ?> fs-1 mb-3 text-dark"></i>
                            <p class="fw-bold mb-0 small text-uppercase ls-2"><?= htmlspecialchars($t['name']) ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php if($loggedIn): ?>
                <div class="text-center mt-4">
                    <a href="manage.php?page=edit&section=tech" class="btn btn-primary rounded-pill">Edit Tech Stack</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-end mb-5">
                <div>
                    <h6 class="text-uppercase ls-2 text-muted">Portfolio</h6>
                    <h1 class="fw-800 display-4 mb-0">Featured Projects</h1>
                </div>
                <?php if($loggedIn): ?>
                    <a href="manage.php?page=add_project" class="btn btn-success rounded-pill px-4"><i class="fas fa-plus me-2"></i> Add</a>
                <?php endif; ?>
            </div>

            <div class="row g-4">
                <?php foreach($projects as $p): ?>
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden hover-scale hover-lift">
                            <div class="ratio ratio-16x9 bg-light">
                                <img src="<?= htmlspecialchars($p['image']) ?>" class="object-fit-cover" alt="<?= htmlspecialchars($p['title']) ?>">
                            </div>
                            <div class="card-body p-4">
                                <h4 class="fw-bold mb-2"><?= htmlspecialchars($p['title']) ?></h4>
                                <p class="text-secondary mb-4"><?= htmlspecialchars($p['description']) ?></p>
                                
                                <?php if($loggedIn): ?>
                                    <div class="d-flex gap-2">
                                        <a href="manage.php?page=edit_project&id=<?= $p['id'] ?>" class="btn btn-sm btn-light rounded-pill">Edit</a>
                                        <button class="btn btn-outline-danger btn-sm rounded-pill px-3 delete-project" data-id="<?= $p['id'] ?>">Delete</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container py-4">
            <h1 class="fw-800 display-4 mb-5 text-center">Education</h1>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-5">
                    <div class="card border-0 shadow rounded-4 p-4 h-100">
                        <div class="card-body text-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex p-3 mb-3">
                                <i class="fas fa-university fs-3"></i>
                            </div>
                            <h5 class="fw-bold text-uppercase">College</h5>
                            <hr class="w-25 mx-auto my-3 text-primary opacity-100">
                            <p class="lead mb-0"><?= htmlspecialchars($education['college'] ?? 'Emilio Aguinaldo College – Cavite') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card border-0 shadow rounded-4 p-4 h-100 bg-dark text-white">
                        <div class="card-body text-center">
                            <div class="bg-white bg-opacity-25 text-white rounded-circle d-inline-flex p-3 mb-3">
                                <i class="fas fa-school fs-3"></i>
                            </div>
                            <h5 class="fw-bold text-uppercase">High School</h5>
                            <hr class="w-25 mx-auto my-3 text-white opacity-100">
                            <p class="lead mb-0"><?= htmlspecialchars($education['highschool'] ?? 'Tanza National Comprehensive High School') ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php if($loggedIn): ?>
                <div class="text-center mt-4">
                    <a href="manage.php?page=edit&section=education" class="btn btn-primary rounded-pill">Edit Education</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="py-5 bg-white">
        <div class="container py-4">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <h1 class="fw-800 display-4 mb-0">Certifications</h1>
                <?php if($loggedIn): ?>
                    <a href="manage.php?page=add_cert" class="btn btn-success rounded-pill px-4"><i class="fas fa-plus me-2"></i> Add</a>
                <?php endif; ?>
            </div>

            <div class="row g-4">
                <?php foreach($certifications as $c): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 h-100 hover-lift">
                            <div class="p-3 pb-0">
                                <img src="<?= htmlspecialchars($c['image']) ?>" class="img-fluid rounded-3 border w-100" alt="<?= htmlspecialchars($c['title']) ?>">
                            </div>
                            <div class="card-body p-4">
                                <h5 class="fw-bold"><?= htmlspecialchars($c['title']) ?></h5>
                                <p class="text-muted small mb-3"><?= htmlspecialchars($c['description']) ?></p>
                                <?php if($loggedIn): ?>
                                    <div class="mt-3 border-top pt-3">
                                        <a href="manage.php?page=edit_cert&id=<?= $c['id'] ?>" class="btn btn-sm btn-light rounded-pill">Edit</a>
                                        <button class="btn btn-sm btn-danger rounded-pill delete-cert" data-id="<?= $c['id'] ?>">Delete</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-5 bg-dark text-white text-center">
        <div class="container py-5">
            <h1 class="fw-800 display-3 mb-2">Let's Work Together</h1>
            <p class="lead text-white-50 mb-5">Have a project in mind? Let's build something amazing.</p>
            
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="#" class="btn btn-light btn-lg rounded-pill px-5 fw-bold hover-lift">
                    <i class="fas fa-envelope me-2"></i> Email Me
                </a>
                <a href="#" class="btn btn-outline-light btn-lg rounded-pill px-4 hover-lift">
                    <i class="fab fa-linkedin"></i>
                </a>
                <a href="#" class="btn btn-outline-light btn-lg rounded-pill px-4 hover-lift">
                    <i class="fab fa-github"></i>
                </a>
            </div>

            <div class="mt-5 pt-5 border-top border-white border-opacity-10">
                <p class="small text-white-50 fw-bold mb-0">© 2025 JOHN CARLO TULIN. ALL RIGHTS RESERVED.</p>
            </div>
        </div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="assets/delete.js"></script>

</body>
</html>