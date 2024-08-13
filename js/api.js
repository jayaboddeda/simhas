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


async function loadProjectsByCategory(categoryId) {
    const projectData = await getprojects(categoryId);

    if (projectData && projectData.data && projectData.data.length > 0) {
        const projectContainer = document.querySelector(".isotope-content");

        projectData.data.forEach(project => {
            const projectHTML = `
                <div class="col-md-6 col-lg-6 isotope-item">
                    <article class="media-project-2 m-b-30">
                        <figure class="media__img">
                            <img src="${ASSET_URL}/${project.main_image}" alt="${project.name}" />
                        </figure>
                        <div class="media__body" style="position: absolute; bottom: 0px; padding-bottom: 0px;">
                            <h3 class="media__title">
                                <a href="projectdetails.html?id=${project.id}">${project.name}</a>
                            </h3>
                            <span class="address">${project.location} / ${project.status}</span>
                        </div>
                    </article>
                </div>
            `;
            projectContainer.innerHTML += projectHTML;
            try {

                var $isotopeWrapper = $('.js-isotope-wrapper');
        
                $isotopeWrapper.each(function () {
                    var that = $(this);
                    var isotopeFilter = that.find('.iostope-filter');
                    var isotopeContent = that.find('.isotope-content');
        
                    var $dataHori = false;
                    if(that.data('isotope-hori'))
                        $dataHori = that.data('isotope-hori');
                        setTimeout(function() {
                    // init Isotope
                        var $iso = isotopeContent.isotope({
                            itemSelector: '.isotope-item',
                            percentPosition: true,
                            animationEngine : 'best-available',
                            masonry: {
                                columnWidth: '.isotope-item-sizer',
                                horizontalOrder: $dataHori
                            }
                        });
        
                        $iso.imagesLoaded().progress( function() {
                            $iso.isotope('layout');
                        });
        
                        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                            $iso.isotope('layout');
                        })
        
                    }, 1000); 
                    isotopeFilter.on('click', 'li span', function () {
                        isotopeContent.isotope({filter: $(this).attr('data-filter')});
                    });
        
                    isotopeFilter.on('click', 'li', function () {
                        isotopeFilter.find('.active').removeClass('active');
                        $(this).addClass('active');
                    });
        
        
                });
        
            } catch(err) {
                console.log(err)
            }
        });
    } else {
        console.error(`No project data found for category ID: ${categoryId}.`);
    }
}
