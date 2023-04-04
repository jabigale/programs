<!DOCTYPE html>
<html>
<title>W3.CSS Template</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="style/mobilestyle.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<style>
html,body,h1,h2,h3,h4,h5 {font-family: "Raleway", sans-serif}
</style>
<body class="w3-dark-grey">
<!-- Top container -->
<div class="w3-bar w3-top w3-black w3-large" style="z-index:4">
  <button class="w3-bar-item w3-button w3-hover-none w3-hover-text-light-grey" onclick="w3_open();">Menu</button>
  <span class="w3-bar-item w3-right">Logo</span>
</div>
<!-- Sidebar/menu -->
<nav class="w3-sidebar w3-collapse w3-white w3-animate-left" style="z-index:3;width:300px;" id="mySidebar"><br>
  <div class="w3-container w3-row">
    <div class="w3-col s4">
    </div>
    <div class="w3-col s8 w3-bar">
      <span>Welcome, <strong>Jordan</strong></span><br>
      <a href="#" class="w3-bar-item w3-button"></a>
      <a href="#" class="w3-bar-item w3-button"></a>
      <a href="#" class="w3-bar-item w3-button"></a>
    </div>
  </div>
  <hr>
  <div class="w3-containerk">
    <h5>Dashboard</h5>
  </div>
  <div class="w3-bar-block">
    <a href="#" class="w3-bar-item w3-button w3-padding-16 w3-dark-grey w3-hover-black" onclick="w3_close()" title="close menu">Close Menu</a>
    <a href="#" class="w3-bar-item w3-button w3-padding w3-red">3 Customers Waiting</a>
    <a href="#" class="w3-bar-item w3-button w3-padding">Vehicles to get in</a>
    <a href="#" class="w3-bar-item w3-button w3-padding"></a>
    <a href="#" class="w3-bar-item w3-button w3-padding"></a>
    <a href="#" class="w3-bar-item w3-button w3-padding">Orders</a>
    <a href="#" class="w3-bar-item w3-button w3-padding"></a>
    <a href="#" class="w3-bar-item w3-button w3-padding"></a>
    <a href="#" class="w3-bar-item w3-button w3-padding"></a>
    <a href="#" class="w3-bar-item w3-button w3-padding">Settings</a><br><br>
  </div>
</nav>
<!-- Overlay effect when opening sidebar on small screens -->
<div class="w3-overlay w3-animate-opacity" onclick="w3_close()" style="cursor:pointer" title="close side menu" id="myOverlay"></div>
<!-- !PAGE CONTENT! -->
<div class="w3-main" style="margin-left:300px;margin-top:43px;">
  <!-- Header -->
  <header class="w3-container" style="padding-top:22px">
    <h5><b>My Dashboard</b></h5>
  </header>
  <div class="w3-row-padding w3-margin-bottom">
    <div class="w3-quarter"><a href="mobileworkorders.php">
      <div class="w3-container w3-red w3-padding-16">
        <div class="w3-right">
          <h3>5</h3>
        </div>
        <div class="w3-clear"></div>
        <h4>Work Orders</h4>
      </div></a>
    </div>
    <div class="w3-quarter"><a href="mobileschedule.php">
      <div class="w3-container w3-blue w3-padding-16">
        <div class="w3-right">
          <h3>99</h3>
        </div>
        <div class="w3-clear"></div>
        <h4>Schedule</h4>
      </div></a>
    </div>
    <div class="w3-quarter"><a href="mobilenewworkorder.php">
      <div class="w3-container w3-teal w3-padding-16">
        <div class="w3-right">
          <h3>23</h3>
        </div>
        <div class="w3-clear"></div>
        <h4>New Workorder</h4>
      </div></a>
    </div>
    <div class="w3-quarter"><a href="mobileorders.php">
      <div class="w3-container w3-orange w3-text-white w3-padding-16">
        <div class="w3-right">
          <h3>50</h3>
        </div>
        <div class="w3-clear"></div>
        <h4>Order Parts</h4>
      </div></a>
    </div>
  </div>
  <div class="w3-panel">
    <div class="w3-row-padding" style="margin:0 -16px">
      <div class="w3-third">
      </div>
      <div class="w3-twothird">
        <h5>Feeds</h5>
        <table class="w3-table w3-striped w3-white">
          <tr>
            <td></td>
            <td>New record, over 90 views.</td>
            <td><i>10 mins</i></td>
          </tr>
          <tr>
            <td></td>
            <td>Database error.</td>
            <td><i>15 mins</i></td>
          </tr>
          <tr>
            <td></td>
            <td>New record, over 40 users.</td>
            <td><i>17 mins</i></td>
          </tr>
          <tr>
            <td></td>
            <td>New comments.</td>
            <td><i>25 mins</i></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  <hr>
  <hr>
  <!-- Footer -->
  <footer class="w3-container w3-padding-16 w3-dark-grey">
    <p>Powered by <a href="https://www.w3schools.com/w3css/default.asp" target="_blank">w3.css</a></p>
  </footer>
  <!-- End page content -->
</div>
<script>
// Get the Sidebar
var mySidebar = document.getElementById("mySidebar");
// Get the DIV with overlay effect
var overlayBg = document.getElementById("myOverlay");
// Toggle between showing and hiding the sidebar, and add overlay effect
function w3_open() {
    if (mySidebar.style.display === 'block') {
        mySidebar.style.display = 'none';
        overlayBg.style.display = "none";
    } else {
        mySidebar.style.display = 'block';
        overlayBg.style.display = "block";
    }
}
// Close the sidebar with the close button
function w3_close() {
    mySidebar.style.display = "none";
    overlayBg.style.display = "none";
}
</script>
</body>
</html>