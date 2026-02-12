<?php
session_start();

if(!isset($_SESSION['email'])){
  header('Location: login_page.html');
  exit;
}

require './database/db.php';

$email = $_SESSION['email'];

$stmt = $conn->prepare("SELECT * FROM users where email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){
  $user = $result->fetch_assoc();

  $fullName = $user['full_name'];
  $firstName = explode(' ', $fullName)[0];

  $_SESSION['user_id'] = $user['id'];
  $_SESSION['full_name'] = $fullName;
  $_SESSION['first_name'] = $firstName;
  $_SESSION['phone'] = $user['phone'];
} else{
  session_destroy();
  header('Location: login_page.html');
  exit;
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <!-- Merienda Font-->
  <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@300..900&family=Spline+Sans:wght@300..700&display=swap" rel="stylesheet">
  <!-- Lato Font -->
  <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Merienda:wght@300..900&family=Spline+Sans:wght@300..700&display=swap" rel="stylesheet">
  <!-- Open Sans Font -->
  <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Merienda:wght@300..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Spline+Sans:wght@300..700&display=swap" rel="stylesheet">

  <!-- Google Icons -->
   <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

  <!-- Boxicons CSS -->
   <!-- Basic Icons -->
<link href="https://cdn.boxicons.com/3.0.8/fonts/basic/boxicons.min.css" rel="stylesheet">
<!-- Filled Icons -->
<link href="https://cdn.boxicons.com/3.0.8/fonts/filled/boxicons-filled.min.css" rel="stylesheet">
<!-- Brand Icons -->
<link href="https://cdn.boxicons.com/3.0.8/fonts/brands/boxicons-brands.min.css" rel="stylesheet">

  <!-- Font Awesome CSS -->
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <!-- CSS Stylesheet -->
  <link rel="stylesheet" href="./css/style.css?v=<?php echo time(); ?>">
  <link rel="stylesheet" href="./css/dashboard.css?v=<?php echo time(); ?>">
  <title>Julie's Bakery | User Dashboard</title>
</head>

<body>
  <aside class="sidebar">
    <nav>
      <a href="#home" class="sidebar-header"><i class='bx bx-cupcake'></i><h1>Julie's Bakery</h1></a>

      <ul>
        <h2><span>Main Menu</span><div class="menu-divider"></div></h2>

        <li><a href="" class="active"><span class="material-symbols-outlined">
dashboard
</span>Dashboard</a></li>
        <li><a href=""><span class="material-symbols-outlined">
shopping_basket
</span>Shop</a></li>
        <li><a href=""><span class="material-symbols-outlined">
favorite
</span>Favorites</a></li>

        <h2><span>Account</span><div class="menu-divider"></div></h2>
        <li><a href=""><span class="material-symbols-outlined">
account_circle
</span>Profile</a></li>
        <li><a href=""><span class="material-symbols-outlined">
settings
</span>Settings</a></li>
        <li><form action="./formHandlers/logout_handler.php" method="post"><button id="logout_button"><a href="./formHandlers/logout_handler.php"><span class="material-symbols-outlined">
logout
</span>Logout</a></button></form></li>
      </ul>

      <div class="user-account">
        <div class="user-profile">
          <img src="./images/blank-pfp1.jpg" alt="Profile Image">
          <div class="user-details">
            <h3><?php echo htmlspecialchars($firstName); ?></h3>
            <h4><?php echo htmlspecialchars($user['email']); ?></h4>
          </div>
        </div>
      </div>
    </nav>
  </aside>

  <div class="grid-column-2">
  <header>
    <form>
      <div class="searchbar-wrapper">
      <i class='bx  bx-search'></i> 
      <input type="search" id="search-bar" placeholder="Search">
      </div>
    </form>

    <nav>
      <ul>
        <li><a href=""><span class="material-symbols-outlined">
notifications
</span></a></li>
        <li><a href=""><span class="material-symbols-outlined">
chat
</span></a></li>
<li><a href=""><span class="material-symbols-outlined">
shopping_cart
</span></a></li>
        <li class="header-user-info">
           <div class="user-account">
        <div class="user-profile">
          <img src="./images/blank-pfp1.jpg" alt="Profile Image">
          <div class="user-details">
            <h3><?php echo htmlspecialchars($firstName); ?></h3>
            <h4><?php echo htmlspecialchars($user['email']); ?></h4>
          </div>
        </div>
      </div>
        </li>
      </ul>
    </nav>
  </header>

  <main>
    <section class="dashboard-hero-section">
      <div class="dashboard-hero-content">
        <div class="hero-content-col-1">
        <p>Deal of the weekend</p>
        <h1>Hello, <?php echo htmlspecialchars($firstName); ?>!</h1>
        <p>Get FREE delivery on every weekend</p>

        <a href="#"><button type="button" class="cta-button">Check Menu</button></a>
        </div>

        <div class="hero-content-col-2">
          <figure>
            <img src="./images/blueberry-cheesecake.jpg" alt="blueberry-cheesecake">
          </figure>
        </div>
      </div>
    </section>

    <section class="dashboard-product-section">
      <div class="product-subsection">
        <!--category-->
        <div class="dashboard-products-container">
          <div class="heading-nav">
          <h2>Category</h2>
          <a href="">View All</a>
          </div>

          <div class="category-container">
            <div class="category">
              <figure>
                <img src="./images/cake3.jpg" alt="category img">
                <figcaption>Cake</figcaption>
              </figure>
            </div>

            <div class="category">
              <figure>
                <img src="./images/pastry1.jpg" alt="category img">
                <figcaption>Pastry</figcaption>
              </figure>
            </div>

            <div class="category">
              <figure>
                <img src="./images/cookie.jpg" alt="category img">
                <figcaption>Cookie</figcaption>
              </figure>
            </div>

            <div class="category">
              <figure>
                <img src="./images/bread.jpg" alt="category img">
                <figcaption>Bread</figcaption>
              </figure>
            </div>
          </div>
        </div>

        <!--trending-->
        <div class="dashboard-products-container">
          <h2>Trending Products</h2>

          <div class="trending-container">
            <div class="trending wide-container">
              <figure>
                <span class="img-container">
                <img src="./images/eggpie.png" alt="trending-product">
                </span>
                <figcaption>
                  <h3>Egg Pie</h3>
                  <data><span class="php-symbol">&#8369;</span>100.00</data>
                  <div class="buttons-wrapper">
                  <button type="button" class="cta-button add-cart-btn"><span class="material-symbols-outlined">
add_shopping_cart
</span> Add to Cart</button>
<span class="material-symbols-outlined heart_plus">
heart_plus
</span>
</div>
                </figcaption>
              </figure>
            </div>

            <div class="trending wide-container">
              <figure>
                <span class="img-container">
                <img src="./images/eggpie.png" alt="trending-product">
                </span>
                <figcaption>
                  <h3>Egg Pie</h3>
                  <data><span class="php-symbol">&#8369;</span>100.00</data>
                  <div class="buttons-wrapper">
                  <button type="button" class="cta-button add-cart-btn"><span class="material-symbols-outlined">
add_shopping_cart
</span> Add to Cart</button>
<span class="material-symbols-outlined heart_plus">
heart_plus
</span>
</div>
                </figcaption>
              </figure>
            </div>

            <div class="trending wide-container">
              <figure>
                <span class="img-container">
                <img src="./images/eggpie.png" alt="trending-product">
                </span>
                <figcaption>
                  <h3>Egg Pie</h3>
                  <data><span class="php-symbol">&#8369;</span>100.00</data>
                  <div class="buttons-wrapper">
                  <button type="button" class="cta-button add-cart-btn"><span class="material-symbols-outlined">
add_shopping_cart
</span> Add to Cart</button>
<span class="material-symbols-outlined heart_plus">
heart_plus
</span>
</div>
                </figcaption>
              </figure>
            </div>

            <div class="trending wide-container">
              <figure>
                <span class="img-container">
                <img src="./images/eggpie.png" alt="trending-product">
                </span>
                <figcaption>
                  <h3>Egg Pie</h3>
                  <data><span class="php-symbol">&#8369;</span>100.00</data>
                  <div class="buttons-wrapper">
                  <button type="button" class="cta-button add-cart-btn"><span class="material-symbols-outlined">
add_shopping_cart
</span> Add to Cart</button>
<span class="material-symbols-outlined heart_plus">
heart_plus
</span>
</div>
                </figcaption>
              </figure>
            </div>
          </div>
        </div>
      </div>

      <div class="cart-subsection">
        <div class="heading-nav">
          <h2>My Cart</h2>
          <a href="">View All</a>
          </div>

          <div class="cart-product-container">
        <div class="cart-container wide-container">
          <figure>
            <span class="img-container">
            <img src="./images/eggpie.png" alt="cart-product">
            </span>  
            <figcaption>
              <h3>Name</h3>
              <data><span class="php-symbol">&#8369;</span>100.00</data>
            </figcaption>
          </figure>
        </div>

        <div class="cart-container wide-container">
          <figure>
            <span class="img-container">
            <img src="./images/eggpie.png" alt="cart-product">
            </span> 
            <figcaption>
              <h3>Name</h3>
              <data><span class="php-symbol">&#8369;</span>100.00</data>
            </figcaption>
          </figure>
        </div>

        <div class="cart-container wide-container">
          <figure>
            <span class="img-container">
            <img src="./images/eggpie.png" alt="cart-product">
            </span>  
            <figcaption>
              <h3>Name</h3>
              <data><span class="php-symbol">&#8369;</span>100.00</data>
            </figcaption>
          </figure>
          </div>
        </div>
      </div>
    </section>
  </main>
</div>

</body>
</html>