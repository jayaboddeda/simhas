// services/api.js

const API_BASE_URL = "https://admin.simhas.klads.co.in"; // Update this with your API base URL
const token = "eVWpHfuEu9TzlRyxFSzNXQRQM-Io--ul";

 const ASSET_URL = `${API_BASE_URL}/assets/`;

async function fetchAPI(endpoint, method = "GET", body = null, headers = {}) {
  const requestOptions = {
    method: method,
    headers: {
      ...headers,
      "Content-Type": "application/json",
      Authorization: "Bearer " + token, // Update this with your authorization token
    },
    body: body ? JSON.stringify(body) : undefined,
  };

  try {
    const response = await fetch(`${API_BASE_URL}${endpoint}`, requestOptions);
    // if (!response.ok) {
    //   throw new Error(response.json());
    // }
    return await response.json();
  } catch (error) {
    console.error("Error:", error);
    return error;
  }
}

 async function createEnquiry(data) {
  return await fetchAPI("/items/enquiries", "POST", data);
}


 async function getblogbyid(id) {
  return await fetchAPI("/items/blogs/" + id);
}








 async function getblogs(filter = "", limit = "") {
  let queryString = "?sort=-date_created";

  if (filter) {
    queryString += "&filter[slug_name][_eq]=" + filter;
  }

  if (limit) {
    queryString += "&limit=" + limit;
  }

  return await fetchAPI("/items/blogs" + queryString);
}


async function getprojects(id){
    return await fetchAPI(`/items/projects?filter={ "category": { "_in":[${id}] }}`)
}

async function getprojectbyid(id){
    return await fetchAPI(`/items/projects/${id}`)
}

async function getprojectfiles(id){
    return await fetchAPI(`/items/projects_files?filter={ "id": { "_in":[${id}] }}`)
}


function initializeIsotope() {
    return new Promise((resolve) => {
        // Use requestAnimationFrame to ensure DOM updates are complete
        requestAnimationFrame(() => {
            try {
                var $isotopeWrapper = $('.js-isotope-wrapper');

                $isotopeWrapper.each(function () {
                    var that = $(this);
                    var isotopeContent = that.find('.isotope-content');

                    // Check if Isotope is already initialized
                    if (isotopeContent.data('isotope')) {
                        // If Isotope is already initialized, just trigger layout
                        isotopeContent.isotope('layout');
                    } else {
                        var isotopeFilter = that.find('.iostope-filter');

                        var $dataHori = false;
                        if (that.data('isotope-hori'))
                            $dataHori = that.data('isotope-hori');

                        // Initialize Isotope
                        var $iso = isotopeContent.isotope({
                            itemSelector: '.isotope-item',
                            percentPosition: true,
                            animationEngine: 'best-available',
                            masonry: {
                                columnWidth: '.isotope-item-sizer',
                                horizontalOrder: $dataHori
                            }
                        });

                        $iso.imagesLoaded().progress(function () {
                            $iso.isotope('layout');
                        });

                        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                            $iso.isotope('layout');
                        });

                        isotopeFilter.on('click', 'li span', function () {
                            isotopeContent.isotope({ filter: $(this).attr('data-filter') });
                        });

                        isotopeFilter.on('click', 'li', function () {
                            isotopeFilter.find('.active').removeClass('active');
                            $(this).addClass('active');
                        });
                    }
                });

                resolve();  // Resolve the promise once Isotope initialization is done or layout is triggered

            } catch (err) {
                console.log(err);
                resolve();  // Still resolve the promise in case of an error
            }
        });
    });
}

async function loadProjectsByCategory(categoryId) {
    const projectData = await getprojects(categoryId);

    if (projectData && projectData.data && projectData.data.length > 0) {
        const projectContainer = document.querySelector(".masonry-grid");

        projectData.data.forEach(project => {
            const projectHTML = `
                <div class="col-lg-6 col-md-6 masonry-item">
                 <a href="projectdetails.html?id=${project.id}">
                    <div class="img-container">
                        <img src="${ASSET_URL}/${project.main_image}" alt="${project.name}" class="img-fluid">
                        <div class="img-overlay">
                            <h3>${project.name}</h3>
                            <p>${project.location} / ${project.status}</p>
                        </div>
                    </div>
                    </a>
                </div>
            `;
            projectContainer.insertAdjacentHTML('beforeend', projectHTML);    
        });

        // Use imagesLoaded to ensure all images are loaded before initializing Masonry
        imagesLoaded(projectContainer, function() {
            const masonryGrid = document.querySelector('.masonry-grid');
            new Masonry(masonryGrid, {
                itemSelector: '.masonry-item',
                percentPosition: true,
            });
        });

    } else {
        console.error(`No project data found for category ID: ${categoryId}.`);
    }
}




    // async function loadProjectsByCategory(categoryId) {
    //     const projectData = await getprojects(categoryId);

    //     if (projectData && projectData.data && projectData.data.length > 0) {
    //         const projectContainer = document.querySelector(".masonry-grid");

    //         projectData.data.forEach(project => {
    //             const projectHTML = `
    //                 <div class="col-lg-4 col-md-6 col-sm-12 masonry-item">
    //                     <a href="projectdetails.html?id=${project.id}">
    //                         <div class="img-container">
    //                             <img src="${ASSET_URL}/${project.main_image}" alt="${project.name}" class="img-fluid">
    //                             <div class="img-overlay">
    //                                 <h3>${project.name}</h3>
    //                                 <p>${project.location} / ${project.status}</p>
    //                             </div>
    //                         </div>
    //                     </a>
    //                 </div>
    //             `;
    //             projectContainer.insertAdjacentHTML('beforeend', projectHTML);    
    //         });

    //         // imagesLoaded(projectContainer, function() {
    //         //     const masonryGrid = document.querySelector('.masonry-grid');
    //         //     new Masonry(masonryGrid, {
    //         //         itemSelector: '.masonry-item',
    //         //         percentPosition: true,
    //         //     });
    //         // });

    //     } else {
    //         console.error(`No project data found for category ID: ${categoryId}.`);
    //     }
    // }
