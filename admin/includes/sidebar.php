<!-- superadmin_sidebar.php -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="sidebar">
  <h2>BiniMarket</h2>
  <ul>
    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
    <li><a href="users.php"><i class="fas fa-users"></i> Utilisateurs</a></li>
    <li><a href="annonces.php"><i class="fas fa-bullhorn"></i> Annonces</a></li>
  </ul>

  <ul style="margin-top: 50px; border-top: 1px solid rgba(255,255,255,0.3); padding-top: 20px;">
    <li><a href="../index.php"><i class="fas fa-home"></i> Accueil</a></li>
    <li><a href="includes/logout.php"><i class="fas fa-sign-out-alt"></i> Se déconnecter</a></li>
  </ul>
</div>

<style>
.sidebar {
  position: fixed;
  left: 0;
  top: 0;
  width: 230px;
  height: 100%;
  background-color: #3498db;
  padding-top: 20px;
  color: white;
  font-family: 'Segoe UI', sans-serif;
  box-shadow: 2px 0 5px #3498db;
}

.sidebar h2 {
  text-align: center;
  margin-bottom: 30px;
  font-size: 22px;
  color: #64ffda;
}

.sidebar ul {
  list-style-type: none;
  padding: 0;
}

.sidebar ul li {
  margin: 15px 0;
}

.sidebar ul li a {
  text-decoration: none;
  color: #ffffff;
  display: block;
  padding: 10px 20px;
  transition: background 0.3s;
}

.sidebar ul li a i {
  margin-right: 10px;
}

.sidebar ul li a:hover {
  background-color: #112240;
  border-left: 4px solid #64ffda;
  color: #64ffda;
}
</style>
