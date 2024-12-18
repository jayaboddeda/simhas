<?php
// API base URL and authorization token
define('API_BASE_URL', 'https://admin.simhas.klads.co.in');
define('TOKEN', 'eVWpHfuEu9TzlRyxFSzNXQRQM-Io--ul');

// Fetch API function using cURL
function fetchAPI($endpoint, $method = 'GET', $body = null) {
    $url = API_BASE_URL . $endpoint;
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . TOKEN,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if ($body) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
    }

    $response = curl_exec($ch);
    curl_close($ch);

    return json_decode($response, true);
}

// Get project by ID
function getProjectById($id) {
    return fetchAPI("/items/projects/{$id}");
}

// Get project files (images)
function getProjectFiles($id) {
    if (is_array($id)) {
        $idList = implode(',', $id);
    } else {
        $idList = $id; // Handle case where it's a single ID
    }
    return fetchAPI("/items/projects_files?filter={\"id\":{\"_in\":[$idList]}}");
}


// Asset URL constant
define('ASSET_URL', API_BASE_URL . '/assets/');

// Get project ID from the URL
$project_id = $_GET['id'] ?? null;

if (!$project_id) {
    echo "Project ID not found in the URL.";
    exit;
}

// Fetch the project details and files
$project_data = getProjectById($project_id);
$project = $project_data['data'] ?? null;

if (!$project) {
    echo "Project details not found.";
    exit;
}

// Check if description is a string or an array
$project_description = '';
if (isset($project['description'])) {
    if (is_array($project['description'])) {
        $project_description = implode(', ', $project['description']);
    } else {
        $project_description = $project['description'];
    }
}

$project_files_data = getProjectFiles($project['internal_images']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <base href="http://localhost/www.simhas.com/">
    <meta name="description" content="<?= htmlspecialchars($project['page_description']) ?>">
    <meta name="author" content="Simhas">
    <meta name="keywords" content="<?= htmlspecialchars($project['page_keywords']) ?>">
    <!-- Title Page -->
    <title><?= htmlspecialchars($project['page_tittle']) ?></title>

    <!-- Icons font CSS -->
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="vendor/themify-font/themify-icons.css" rel="stylesheet" media="all">
    <!-- Bootstrap CSS -->
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">
    <!-- Vendor CSS -->
    <link href="vendor/animate.css/animate.min.css" rel="stylesheet" media="all">
    <link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <!-- Main CSS -->
    <link href="css/main.min.css" rel="stylesheet" media="all">

    <!-- Favicons -->
    <link rel="shortcut icon" href="assets/Icons/header-icon.png">
    <link rel="apple-touch-icon" href="assets/Icons/header-icon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="assets/Icons/header-icon.png">
    <link rel="apple-touch-icon" sizes="114x114" href="apple-icon-114x114.png">

    <style>
        .zoomD {
            width: 400px;
            height: 300px;
            cursor: pointer;
            margin: 10px 0;
            object-fit: cover;
        }

        #lb-back {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            visibility: hidden;
            opacity: 0;
            transition: all ease 0.4s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #lb-back.show {
            visibility: visible;
            opacity: 1;
            background-color: black;
        }

        #lb-img {
    position: relative;
    width: 80vw; /* Adjust based on your needs */
    height: 80vh; /* Adjust based on your needs */
    background-color: black;
    overflow: hidden;
}

#lb-img img {
    width: 100;
    height: 100%;
    object-fit: contain;
}




        #lb-close,
        #lb-prev,
        #lb-next {
            position: absolute;
            color: white;
            background-color: rgba(0, 0, 0, 0.5);
            border: none;
            padding: 15px; /* Increase padding to make the buttons larger */
            cursor: pointer;
            font-size: 30px; /* Increase font size for larger buttons */
            z-index: 1000;
        }

        #lb-close {
            top: 30px;
            right: 10px;
        }

        #lb-prev {
            left: 20px; /* Increase left position for better visibility */
            top: 50%;
            transform: translateY(-50%);
        }

        #lb-next {
            right: 20px; /* Increase right position for better visibility */
            top: 50%;
            transform: translateY(-50%);
        }

        .image-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 10px;
        }

        html,
        body {
            padding: 0;
            margin: 0;
        }
/* Default style for larger screens */
#mainimagecontainer{
    width: 350px;
    height: 350px;
}
#mainimagecontainer img{
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preserve-format {
  white-space: pre-wrap;
  word-wrap: break-word;
}

.description-container p{

    margin: 10px 0;
    line-height: 1.8;
    
}


    </style>
</head>

<body class="animsition js-preloader">
    <div class="page-wrapper">
        <!-- HEADER -->
        <header id="header">
            <div class="header header-1 d-none d-lg-block js-header-1">
                <div class="header__bar">
                    <div class="wrap wrap--w1790">
                        <div class="container-fluid">
                            <div class="header__content">
                                <div class="logo">
                                    <a href="index ">
                                        <img src="assets/Simhablk.svg" alt="simhas" />
                                    </a>
                                </div>
                                <div class="header__content-right">
                                    <nav class="header-nav-menu">
                                        <ul class="menu nav-menu">
                                            <li class="menu-item">
                                                <a href="index ">Home</a>
                                            </li>

                                            <li class="menu-item menu-item-has-children">
                                                <a href="proj ">Work</a>
                                                <ul class="sub-menu">
                                                    <li class="menu-item">
                                                        <a href="commercial ">Commercial</a>
                                                    </li>
                                                    <li class="menu-item">
                                                        <a href="residential ">Residential</a>
                                                    </li>
                                                    <li class="menu-item">
                                                        <a href="interiors ">Interiors</a>
                                                    </li>

                                                </ul>
                                            </li>
                                            <li class="menu-item">
                                                <a href="about-us ">About</a>
                                            </li>
                                            <li class="menu-item">
                                                <a href="Client ">Client</a>
                                            </li>
                                            <li class="menu-item">
                                                <a href="blog">Blogs</a>
                                            </li>
                                            <li class="menu-item">
                                                <a href="contact ">Contact</a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="header-mobile d-block d-lg-none">
                <div class="header-mobile__bar">
                    <div class="container-fluid">
                        <div class="header-mobile__bar-inner">
                            <a class="logo" href="index ">
                                <img src="assets/Simhablk.svg" alt="Simhas" />
                            </a>
                            <button class="hamburger hamburger--slider float-right" type="button">
                                <span class="hamburger-box">
                                    <span class="hamburger-inner"></span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
                <nav class="header-nav-menu-mobile">
                    <div class="container-fluid">
                        <ul class="menu nav-menu menu-mobile">
                            <li class="menu-item">
                                <a href="index ">Home</a>
                            </li>

                            <li class="menu-item menu-item-has-children">
                                <a href="proj ">Work</a>
                                <ul class="sub-menu">
                                    <li class="menu-item">
                                        <a href="commercial ">Commercial</a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="residential ">Residential</a>
                                    </li>
                                    <li class="menu-item">
                                        <a href="interiors ">Interiors</a>
                                    </li>
                                </ul>
                            </li>
                            <li class="menu-item">
                                <a href="about-us ">About</a>
                            </li>
                            <li class="menu-item">
                                <a href="Client ">Client</a>
                            </li>
                            <li class="menu-item">
                                                <a href="blog">Blogs</a>
                                            </li>
                            <li class="menu-item">
                                <a href="contact ">Contact</a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </div>

        </header>
        <!-- END HEADER -->

        <!-- MAIN -->
        <main id="main">
            <div class="container-fluid">
                <section class="m-3 m-md-5">
                    <div>
                        <div class="row">
                            <!-- Right Column (Image) -->
                            <div class="col-md-5 order-1 order-md-2 p-0 mb-3 mb-md-0" id="mainimagecontainer">
                                <img src="<?= ASSET_URL . $project['main_image'] ?>" id="mainimage" alt="<?= htmlspecialchars($project['name']) ?>" class="img-fluid">
                            </div>
                            <!-- Left Column (Text) -->
                            <div class="col-md-7 order-2 order-md-1 mb-4 p-0">
                                <h1 class="font-weight-bold"><?= htmlspecialchars($project['name']) ?></h1>
                                <div class="mt-4">
                                    <div class="d-flex mb-3">
                                        <div class="font-weight-bold" style="min-width: 120px;">Location:</div>
                                        <div><?= htmlspecialchars($project['location']) ?></div>
                                    </div>
                                    <div class="d-flex mb-3">
                                        <div class="font-weight-bold" style="min-width: 120px;">Status:</div>
                                        <div><?= htmlspecialchars($project['status']) ?></div>
                                    </div>
                                    <div class="d-flex mb-3">
                                        <div class="font-weight-bold" style="min-width: 120px;">Area:</div>
                                        <div><?= htmlspecialchars($project['area']) ?></div>
                                    </div>
                                </div>
                                <div class="description-container m-r-20">
                                <p id="project-description" class="mt-4">     
                                <?= $project['description']; ?>

                                </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <div class="projectdetails-images image-grid text-center m-md-5">
                <div id="lb-back">
                        <div id="lb-img"></div>
                        <button id="lb-close">&times;</button>
                        <button id="lb-prev">&#8249;</button>
                        <button id="lb-next">&#8250;</button>
                    </div>
                <?php
                    if (isset($project_files_data['data']) && is_array($project_files_data['data'])) {
                        foreach ($project_files_data['data'] as $file) {
                            echo '<img class="zoomD" src="' . ASSET_URL . $file['directus_files_id'] . '" alt="Project Image" />';
                        }
                    } else {
                        echo "No images available.";
                    }
                    ?>
                </div>
            </div>
        </main>
        <!-- END MAIN -->

        <!-- FOOTER -->
        <footer style="background-color: white; padding: 20px 0;" id="projectdetails_footer">
            <div class="row align-items-center justify-content-center">
                <div class="text-center mb-0 pr-4">
                    <p class="mb-0">
                        Copyright © 2024 Simha Associates. All rights reserved.
                    </p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <a href="https://www.facebook.com/profile.php?id=61563836267621" target="_blank" class="social-icon">
                        <svg id="fi_2168281" enable-background="new 0 0 49.652 49.652" height="30" viewBox="0 0 49.652 49.652" width="30" xmlns="http://www.w3.org/2000/svg"><g><g><path d="m24.826 0c-13.689 0-24.826 11.137-24.826 24.826 0 13.688 11.137 24.826 24.826 24.826 13.688 0 24.826-11.138 24.826-24.826 0-13.689-11.136-24.826-24.826-24.826zm6.174 25.7h-4.039v14.396h-5.985s0-7.866 0-14.396h-2.845v-5.088h2.845v-3.291c0-2.357 1.12-6.04 6.04-6.04l4.435.017v4.939s-2.695 0-3.219 0-1.269.262-1.269 1.386v2.99h4.56z"></path></g></g></svg>
                    </a>
                    <a href="https://youtube.com/@simhaassociates?si=cL1DO-9NwnPvq1DU" target="_blank" class="social-icon">
                        <svg height="30" viewBox="0 0 152 152" width="30" xmlns="http://www.w3.org/2000/svg" id="fi_3669688"><g id="Layer_2" data-name="Layer 2"><g id="Color"><g id="_02.YouTube" data-name="02.YouTube"><path d="m65.46 88.26 21.08-12.23-21.08-12.29z"></path><path d="m76 0a76 76 0 1 0 76 76 76 76 0 0 0 -76-76zm40 89.45a15.13 15.13 0 0 1 -15.13 15.14h-49.74a15.13 15.13 0 0 1 -15.13-15.14v-26.9a15.13 15.13 0 0 1 15.13-15.14h49.74a15.13 15.13 0 0 1 15.13 15.14z"></path></g></g></g></svg>
                    </a>
                    <a href="https://www.instagram.com/simha_associates/" target="_blank" class="social-icon">
                        <svg height="30" viewBox="0 0 512 512" width="30" xmlns="http://www.w3.org/2000/svg" id="fi_1384015"><path d="m305 256c0 27.0625-21.9375 49-49 49s-49-21.9375-49-49 21.9375-49 49-49 49 21.9375 49 49zm0 0"></path><path d="m370.59375 169.304688c-2.355469-6.382813-6.113281-12.160157-10.996094-16.902344-4.742187-4.882813-10.515625-8.640625-16.902344-10.996094-5.179687-2.011719-12.960937-4.40625-27.292968-5.058594-15.503906-.707031-20.152344-.859375-59.402344-.859375-39.253906 0-43.902344.148438-59.402344.855469-14.332031.65625-22.117187 3.050781-27.292968 5.0625-6.386719 2.355469-12.164063 6.113281-16.902344 10.996094-4.882813 4.742187-8.640625 10.515625-11 16.902344-2.011719 5.179687-4.40625 12.964843-5.058594 27.296874-.707031 15.5-.859375 20.148438-.859375 59.402344 0 39.25.152344 43.898438.859375 59.402344.652344 14.332031 3.046875 22.113281 5.058594 27.292969 2.359375 6.386719 6.113281 12.160156 10.996094 16.902343 4.742187 4.882813 10.515624 8.640626 16.902343 10.996094 5.179688 2.015625 12.964844 4.410156 27.296875 5.0625 15.5.707032 20.144532.855469 59.398438.855469 39.257812 0 43.90625-.148437 59.402344-.855469 14.332031-.652344 22.117187-3.046875 27.296874-5.0625 12.820313-4.945312 22.953126-15.078125 27.898438-27.898437 2.011719-5.179688 4.40625-12.960938 5.0625-27.292969.707031-15.503906.855469-20.152344.855469-59.402344 0-39.253906-.148438-43.902344-.855469-59.402344-.652344-14.332031-3.046875-22.117187-5.0625-27.296874zm-114.59375 162.179687c-41.691406 0-75.488281-33.792969-75.488281-75.484375s33.796875-75.484375 75.488281-75.484375c41.6875 0 75.484375 33.792969 75.484375 75.484375s-33.796875 75.484375-75.484375 75.484375zm78.46875-136.3125c-9.742188 0-17.640625-7.898437-17.640625-17.640625s7.898437-17.640625 17.640625-17.640625 17.640625 7.898437 17.640625 17.640625c-.003906 9.742188-7.898437 17.640625-17.640625 17.640625zm0 0"></path><path d="m256 0c-141.363281 0-256 114.636719-256 256s114.636719 256 256 256 256-114.636719 256-256-114.636719-256-256-256zm146.113281 316.605469c-.710937 15.648437-3.199219 26.332031-6.832031 35.683593-7.636719 19.746094-23.246094 35.355469-42.992188 42.992188-9.347656 3.632812-20.035156 6.117188-35.679687 6.832031-15.675781.714844-20.683594.886719-60.605469.886719-39.925781 0-44.929687-.171875-60.609375-.886719-15.644531-.714843-26.332031-3.199219-35.679687-6.832031-9.8125-3.691406-18.695313-9.476562-26.039063-16.957031-7.476562-7.339844-13.261719-16.226563-16.953125-26.035157-3.632812-9.347656-6.121094-20.035156-6.832031-35.679687-.722656-15.679687-.890625-20.6875-.890625-60.609375s.167969-44.929688.886719-60.605469c.710937-15.648437 3.195312-26.332031 6.828125-35.683593 3.691406-9.808594 9.480468-18.695313 16.960937-26.035157 7.339844-7.480469 16.226563-13.265625 26.035157-16.957031 9.351562-3.632812 20.035156-6.117188 35.683593-6.832031 15.675781-.714844 20.683594-.886719 60.605469-.886719s44.929688.171875 60.605469.890625c15.648437.710937 26.332031 3.195313 35.683593 6.824219 9.808594 3.691406 18.695313 9.480468 26.039063 16.960937 7.476563 7.34375 13.265625 16.226563 16.953125 26.035157 3.636719 9.351562 6.121094 20.035156 6.835938 35.683593.714843 15.675781.882812 20.683594.882812 60.605469s-.167969 44.929688-.886719 60.605469zm0 0"></path></svg>
                    </a>
                    <a href="https://www.linkedin.com/company/simhaassociate/" target="_blank" class="social-icon">
                        <svg height="30" viewBox="0 0 152 152" width="30" xmlns="http://www.w3.org/2000/svg" id="fi_3669739"><g id="Layer_2" data-name="Layer 2"><g id="Color"><path id="_10.Linkedin" d="m76 0a76 76 0 1 0 76 76 76 76 0 0 0 -76-76zm-22.1 116h-16.58v-53.41h16.58zm-8.3-60.7a9.65 9.65 0 1 1 9.61-9.7 9.68 9.68 0 0 1 -9.61 9.7zm70.4 60.7h-16.57v-26c0-6.2-.12-14.15-8.62-14.15s-10 6.74-10 13.7v26.45h-16.51v-53.41h15.91v7.28h.23c2.21-4.2 7.62-8.63 15.69-8.63 16.78 0 19.87 11.06 19.87 25.42z" data-name="10.Linkedin"></path></g></g></g></svg>
                    </a>
                    <a href="https://x.com/SimhaAssociates" target="_blank" class="social-icon">
                        <svg width="30" height="30" id="fi_5969020" enable-background="new 0 0 1227 1227" viewBox="0 0 1227 1227" xmlns="http://www.w3.org/2000/svg"><g><path d="m613.5 0c-338.815 0-613.5 274.685-613.5 613.5s274.685 613.5 613.5 613.5 613.5-274.685 613.5-613.5-274.685-613.5-613.5-613.5z"></path><path d="m680.617 557.98 262.632-305.288h-62.235l-228.044 265.078-182.137-265.078h-210.074l275.427 400.844-275.427 320.142h62.239l240.82-279.931 192.35 279.931h210.074l-285.641-415.698zm-335.194-258.435h95.595l440.024 629.411h-95.595z" fill="#fff"></path></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g><g></g></svg>
                    </a>
                </div>
            </div>
        </footer>
        <!-- END FOOTER -->
    </div>

    <!-- Jquery JS-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <!-- Vendor JS -->
    <script src="vendor/animsition/animsition.min.js"></script>
    <script src="vendor/isotope/isotope.pkgd.min.js"></script>
    <!-- Main JS -->
    <script src="js/global.js"></script>

    <script>
        $(document).ready(function () {
            let currentImageIndex = 0;
            let imagesArray = [];

            // Populate imagesArray with project images
            $(".projectdetails-images img").each(function (index) {
                imagesArray.push($(this).attr('src'));
                $(this).on('click', function () {
                    currentImageIndex = index;
                    showImage(currentImageIndex);
                });
            });

            function showImage(index) {
                $("#lb-img").html(`<img src="${imagesArray[index]}" />`);
                $("#lb-back").addClass("show");
            }

            // Lightbox controls
            $("#lb-close").click(function () {
                $("#lb-back").removeClass("show");
            });

            $("#lb-prev").click(function () {
                currentImageIndex = (currentImageIndex > 0) ? currentImageIndex - 1 : imagesArray.length - 1;
                showImage(currentImageIndex);
            });

            $("#lb-next").click(function () {
                currentImageIndex = (currentImageIndex < imagesArray.length - 1) ? currentImageIndex + 1 : 0;
                showImage(currentImageIndex);
            });

            // Close lightbox when clicking on the background
            $("#lb-back").click(function (e) {
                if (e.target.id === "lb-back" || e.target.id === "lb-close") {
                    $(this).removeClass("show");
                }
            });
        });
    </script>
</body>

</html>
