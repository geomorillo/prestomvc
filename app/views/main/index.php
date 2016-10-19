<?php use system\core\Assets; ?>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
        <a class="navbar-brand" href="/"><?= $logo1 ?></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-right">
        <li><a href="/login">Login</a></li>
        <li><a href="/register  ">Register</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

<section id="welcome" class="container">
    <div class="jumbotron">
        <h1>Welcome to <?= $logo2 ?></h1>
        
        <p>
            Is a new lightweight framework, made for people who is starting on the world of MVC patterns and wants to create new applications.
        </p>
    </div>
</section>

<section id="main">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="page-header">
                    <h2>Features</h2>
                </div>
                <ul>
                    <li>Lightweight Framework</li>
                    <li>Easy to install</li>
                    <li>NOTORM Database (SOON)</li>
                    <li>Templates for each view</li>
                    <li>Many and useful helpers</li>
                    <li>Easy to manage</li>
                    <li>Secure and fast</li>
                </ul>
            </div>
            <div class="col-md-6">
                <div class="page-header">
                    <h2>Getting Started</h2>
                </div>
                <p>Well, you did install the framework but, you need to set some parameters for the correct working.</p>
                <ol>
                    <li>Go to <code>/app/config/config.php</code> and set up your configurations</li>
                    <li>Go to <code>/app/config/database_config.php</code> and set up your connection to your database and his respective table.</li>
                    <li>Finally set up tou routes on the file <code>/app/Routes.php</code></li>
                    <li>Ready to go</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<footer id="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <p class="text-center">Copyright &copy; 2016 <a href="">Presto MVC</a></p>
            </div>
        </div>
    </div>
</footer>

