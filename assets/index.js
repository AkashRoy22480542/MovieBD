let name; 
let email;
let password;
let confirmPassword;
let error;

function email_validate() {
        email = document.getElementById("email").value;
        error = document.getElementById("error");

        if (email === "") {
                error.innerHTML = "Email cannot be empty!";
                return false;
        }

        if (email.indexOf("@") <= 0 || email.indexOf("@") === email.length - 1) {
                error.innerHTML = "Invalid email! '@' must be in the middle.";
                error.style.color = "red"; 
                return false;
        }

        let at = email.indexOf("@");
        let dot = email.lastIndexOf(".");
        if (dot <= at + 1 || dot === email.length - 1) {
                error.innerHTML = "Invalid email! '.' must come after '@' and not at the end.";
                error.style.color = "red"; 
                return false;
        }

        return true;
}

function password_vaildate(){
        password = document.getElementById("password").value;
        if (password.length < 8) {
                return false;
        }
        return true;
        
}

function validateSignInForm() {  
        email = document.getElementById("email").value;
        password = document.getElementById("password").value;
        error = document.getElementById("error"); 

        error.textContent = "";
        if (!email || !password) {
          error.textContent = "Please fill in all fields.";
          error.style.color = "red"; 
          return false;
        }
  
        if(!email_validate()){
          error.textContent = "Invalid email address.";
          error.style.color = "red"; 
          return false;
        }
  
        if (!password_vaildate()) {
          error.textContent = "Password must be at least 8 characters long.";
          error.style.color = "red"; 
          return false;
        }
        error.textContent="";
  
        return true;
}

function validateSignUpForm() {
        const name = document.getElementById("name").value;
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;
        const confirmPassword = document.getElementById("repassword").value;
        const error = document.getElementById("error");
  
        error.textContent = "";
  
        if (!name || !email || !password || !confirmPassword) {
          error.textContent = "Please fill in all fields.";
          error.style.color = "red"; 
          return false;
        }

        else if(!email_validate()){
                error.textContent = "Invalid email address.";
                error.style.color = "red"; 
                return false;
        }
  
        else if (!password_vaildate()) {
          error.textContent = "Password must be at least 8 characters long.";
          error.style.color = "red"; 
          return false;
        }
  
        else if (password !== confirmPassword) {
          error.textContent = "Passwords do not match.";
          error.style.color = "red"; 
          return false;
        }
        error.textContent="";
        return true;
}

function validateForgetPassForm(){
        email = document.getElementById("email").value;
        error = document.getElementById("error");
        if(!email){
                error.textContent = "Please fill in all fields.";
                error.style.color = "red"; 
                return false;
        }
        if(!email_validate()){
                error.textContent = "Invalid email address.";
                error.style.color = "red"; 
                return false;
        }
        error.textContent = "";
        window.location.href = "reset_password.html";
        return true;
}

function vaildateResetPass(){
        password = document.getElementById("password").value;
        confirmPassword = document.getElementById("renewpassword").value;
        error =document.getElementById("error");
        if(!password || !confirmPassword){
                error.textContent = "Please fill in all fields.";
                error.style.color = "red"; 
                return false;

        }
        if(!password_vaildate()){
                error.textContent = "Password must be at least 8 characters long.";
                error.style.color=red;
                return false;
        }
        else if(password!==confirmPassword){
                error.innerHTML="Password not Matched";
                error.style.color=red;
                return false;

        }
        error.innerHTML="";
        return true;
}

function toggleEdit() {
  const nameField = document.getElementById("name");
  const phoneField = document.getElementById("phone");
  const emailField = document.getElementById("email"); 
  const fileInput = document.getElementById("uploadPic");
  const button = document.getElementById("toggleBtn");
  const form = document.getElementById("profileForm");

  if (button.textContent === "Edit") {
    nameField.readOnly = false;
    phoneField.readOnly = false;
    fileInput.style.display = "block";
    button.textContent = "Save";
    return true;
  } else {
    nameField.readOnly = false;
    phoneField.readOnly = false;
    form.submit();
    return false;
  }
}




function previewProfilePic() {
  const fileInput = document.getElementById('uploadPic');
  const profilePic = document.getElementById('profilePic');

  const file = fileInput.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      profilePic.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }
}

function searchBoxVaild(){
        value = document.getElementById("search-box").value;
        error = document.getElementById("searcherror");
        if(!value){
                error.innerHTML="Fill the search box!"
                error.style.color="red";
                return false;
        }
        error.innerHTML="";
        return true;
}


    
      let movies =[];
      function loadMovies(list) {
        console.log(list);
        movies=list;
        const grid = document.getElementById('moviesGrid');
        grid.innerHTML = '';
      
        list.forEach(movie => {
          grid.innerHTML += `
            <a href="../controller/movieDetailController.php?title=${encodeURIComponent(movie.title)}" style="text-decoration:none; color:inherit;">
              <div class="movie-card">
                <img src="${movie.poster}" alt="${movie.title}">
                <p>${movie.title}</p>
              </div>
            </a>
          `;
        });
      }

      function applyFilters(list) {
        const searchText = document.getElementById('searchInput').value.toLowerCase();
        const selectedGenres = Array.from(document.querySelectorAll('input[type="checkbox"]:checked')).map(checkbox => checkbox.value);
        const selectedStatus = document.querySelector('input[name="status"]:checked').value;
        const fromDate = document.getElementById('fromDate').value;
        const toDate = document.getElementById('toDate').value;
      
        let filteredMovies = list.filter(movie => {
          if (searchText && !movie.title.toLowerCase().includes(searchText)) {
            return false;
          }
          if (selectedGenres.length > 0 && !selectedGenres.some(genre => movie.genre.includes(genre))) {
            return false;
          }
          if (selectedStatus !== "All" && movie.status !== selectedStatus) {
            return false;
          }
          if (fromDate && movie.date < fromDate) {
            return false;
          }
          if (toDate && movie.date > toDate) {
            return false;
          }
          return true;
        });
      
        loadMovies(filteredMovies);
      }

      let tv =[];

      function loadTvShows(list) {
        console.log(list);
        const grid = document.getElementById('moviesGrid');
        grid.innerHTML = '';
        tv=list;
        list.forEach(tv => {
          grid.innerHTML += `
            <a href="../controller/tvShowDetailsController.php?title=${encodeURIComponent(tv.title)}" style="text-decoration:none; color:inherit;">
              <div class="movie-card">
                <img src="${tv.poster_url}" alt="${tv.title}">
                <p>${tv.title}</p>
              </div>
            </a>
          `;
        });
      }
      


      function applyFiltersTv(list) {
        const searchText = document.getElementById('searchInput').value.toLowerCase();
        const selectedGenres = Array.from(document.querySelectorAll('input[type="checkbox"]:checked')).map(checkbox => checkbox.value);
        const selectedStatus = document.querySelector('input[name="status"]:checked').value;
        const fromDate = document.getElementById('fromDate').value;
        const toDate = document.getElementById('toDate').value;


        let filteredMovies = list.filter(movie => {

          if (searchText && !movie.title.toLowerCase().includes(searchText)) {
            return false;
          }
          if (selectedGenres.length > 0 && !selectedGenres.some(genre => movie.genre.includes(genre))) {
            return false;
          }
          if (selectedStatus !== "All" && movie.status !== selectedStatus) {
            return false;
          }
          if (fromDate && movie.date < fromDate) {
            return false;
          }
          if (toDate && movie.date > toDate) {
            return false;
          }
          return true;
        });
      
        loadTvShows(filteredMovies);
      }



      function displayTrendingMovies(list) {
        const slider = document.getElementById('movie-slider');
        slider.innerHTML = '';
      
        list.forEach(movie => {
          slider.innerHTML += `
            <a href="controller/movieDetailController.php?title=${encodeURIComponent(movie.title)}" style="text-decoration: none; color: inherit;">
              <div class="movie-card">
                <div class="movie-image-wrapper">
                  <img src="${movie.poster.slice(3)}" alt="${movie.title}">
                </div>
                <p>${movie.title}</p>
              </div>
            </a>
          `;
        });
      }


      function displayTrendingTVShows(list) {
        const slider = document.getElementById('tv-slider');
        slider.innerHTML = '';
      
        list.forEach(show => {
          const posterPath = show.poster.startsWith('../') ? show.poster.slice(3) : show.poster;      
          slider.innerHTML += `
            <a href="controller/tvShowDetailsController.php?title=${encodeURIComponent(show.title)}" style="text-decoration: none; color: inherit;">
              <div class="movie-card">
                <div class="movie-image-wrapper">
                  <img src="${posterPath}" alt="${show.title}">
                </div>
                <p>${show.title}</p>
              </div>
            </a>
          `;
        });
      }
      
      
      // document.addEventListener('DOMContentLoaded', () => {
      //   loadMovies();
      // });


      function updateCountdown(releaseDateString) {
        const releaseDate = new Date(releaseDateString).getTime();
        const now = new Date().getTime();
        const distance = releaseDate - now;
      
        const countdownElem = document.getElementById("countdown");
      
        if (!countdownElem) return;
      
        if (distance < 0) {
          countdownElem.innerHTML = "Released!";
          return;
        }
      
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
      
        countdownElem.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
      }
      


    function liveSearch() {
      const input = document.getElementById("search-box").value.trim();
    
      if (input.length < 1) {
        document.getElementById("live-suggestions").innerHTML = "";
        return;
      }
    
      const xhttp = new XMLHttpRequest();
      xhttp.open("POST", "controller/searchHandler.php", true);
      xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      xhttp.send("query=" + encodeURIComponent(input));
    
      xhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
          const result = JSON.parse(this.responseText);
          renderSuggestions(result);
        }
      };
    }
    
    function renderSuggestions(data) {
      const box = document.getElementById("live-suggestions");
      box.innerHTML = "";
    
      if (data.movies.length === 0 && data.tv_shows.length === 0) {
        box.innerHTML = "<p class='no-match'>No results found</p>";
        return;
      }
    
      const combined = [...data.movies, ...data.tv_shows];
    
      combined.forEach(item => {
        const div = document.createElement("div");
        div.className = "suggestion-item";
        div.textContent = item.title;
    
        div.onclick = () => {
          const url = item.is_tv
          ? `controller/tvShowDetailsController.php?title=${encodeURIComponent(item.title)}`
          : `controller/movieDetailController.php?title=${encodeURIComponent(item.title)}`;
          window.location.href = url;
        };
    
        box.appendChild(div);
      });
    }
    
          


  function goBack() {
    window.location.href = "../index.php";
  }

  function goMovie() {
    window.location.href = "movie.php";
  }

  function goTvShow() {
    window.location.href = "tv_show.php";
  }
