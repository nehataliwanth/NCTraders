<?php
if(session_status() === PHP_SESSION_NONE){
session_start();
}

include __DIR__ . '/../config/database.php';

$currentUser = $_SESSION['user_id'] ?? null;

$unreadMessages = 0;

if($currentUser){

$messageQuery = mysqli_query($conn,

"SELECT COUNT(DISTINCT sender_id) AS total

FROM messages

WHERE receiver_id='$currentUser'

AND is_read='0'");

$messageData = mysqli_fetch_assoc($messageQuery);

$unreadMessages =
$messageData['total'];

}
?>

<nav class="navbar navbar-expand-lg fixed-top custom-navbar">

<div class="container-fluid px-5">

<a class="navbar-brand d-flex align-items-center"
href="index.php">

<img src="assets/images/logo.png"
class="logo-img me-3">

<div>

<div class="logo-title">
NC Traders
</div>

<div class="logo-slogan">
Trade Smart. Grow Local.
</div>

</div>

</a>

<form class="search-form"
action="search.php"
method="GET"
id="searchForm">

<div class="search-wrapper">

<input class="search-input"
type="search"
name="search"
id="searchInput"
placeholder="Search products..."
autocomplete="off">

<button class="search-btn"
type="submit">

<i class="fas fa-search"></i>

</button>

<div id="searchHistory"
class="search-history-box d-none">

</div>

</div>

</form>

<ul class="navbar-nav ms-auto align-items-center nav-center">

<li class="nav-item">
<a class="nav-link"
href="index.php">

Home

</a>
</li>

<li class="nav-item">
<a class="nav-link"
href="products.php">

Products

</a>
</li>

<li class="nav-item">
<a class="nav-link position-relative"
href="messages.php">

Messages

<?php if($unreadMessages > 0): ?>

<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">

<?php echo $unreadMessages; ?>

</span>

<?php endif; ?>

</a>
</li>

<li class="nav-item dropdown">

<a class="nav-link dropdown-toggle"
href="#"
role="button"
data-bs-toggle="dropdown"
aria-expanded="false">

<i class="fas fa-cog"></i>
Settings

</a>

<ul class="dropdown-menu dropdown-menu-end">



<li>

<a class="dropdown-item"
href="manage_account.php">

<i class="fas fa-user me-2"></i>

Manage Account

</a>

</li>

<li>

<a class="dropdown-item"
href="logout.php">

<i class="fas fa-sign-out-alt me-2"></i>

Sign Out

</a>

</li>

</ul>

</li>

</ul>

</div>

</nav>
<script>

const searchInput =
document.getElementById('searchInput');

const searchHistory =
document.getElementById('searchHistory');

let searches =
JSON.parse(localStorage.getItem('nc_searches')) || [];

/* SHOW HISTORY */

function renderSearchHistory(){

searchHistory.innerHTML = '';

if(searches.length === 0){

searchHistory.classList.add('d-none');
return;
}

searches.forEach((item,index)=>{

searchHistory.innerHTML += `

<div class="search-history-item"
onclick="searchAgain('${item}')">

<span>
${item}
</span>

<span class="remove-search"
onclick="event.stopPropagation(); removeSearch(${index})">

✕
</span>

</div>

`;

});

searchHistory.classList.remove('d-none');
}

/* SEARCH AGAIN */

function searchAgain(value){

window.location.href =
'search.php?search=' +
encodeURIComponent(value);

}

/* REMOVE */

function removeSearch(index){

searches.splice(index,1);

localStorage.setItem(
'nc_searches',
JSON.stringify(searches)
);

renderSearchHistory();
}

/* INPUT CLICK */

searchInput.addEventListener('focus',()=>{

renderSearchHistory();

});

/* SAVE SEARCH */

document.getElementById('searchForm')
.addEventListener('submit',(e)=>{

const value =
searchInput.value.trim();

if(value !== ''){

searches = searches.filter(
item => item !== value
);

searches.unshift(value);

searches = searches.slice(0,6);

localStorage.setItem(
'nc_searches',
JSON.stringify(searches)
);

}
});

/* HIDE */

document.addEventListener('click',(e)=>{

if(!e.target.closest('.search-form')){

searchHistory.classList.add('d-none');

}

});

</script>