<!DOCTYPE html>
<?php
  session_start();
  if(@$_SESSION['logged'] == true){
    header("Location: index");
  }
?> 
<?php 
  if(@$_POST['login']){
    if(isset($_POST['username']) && isset($_POST['password'])){
        if($_POST['username'] == "" && $_POST['password'] ==""){
          $_SESSION['logged'] = true;
          header("Location: index");
        }else{
         ?>
          <div class="alert alert-warning fade in">
                <a href="#" class="close" data-dismiss="alert">&times;</a>
                <strong>Warning !</strong> Invalid Login Credentials. Please Try again.
            </div>
         <?php
        }
      }
  }
?>
<html lang="en">
	<head>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="js/dataTables/dataTables.bootstrap.min.css">
	</head>
	<body style="background-color:#1C1D1E">
<br><br><br><br>
<div class="container marketing">
    <div class="container">
      <div class="row">
    <div class="col-md-4 col-md-offset-4 text-left" style="background-color:#27292A ;">
      <center>
        <br>
        <div class='panel panel-success'>
       <div class='panel-heading text-left'>
        <h5><strong><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> Login to DataSchef</strong><span class="glyphicon glyphicon-lock pull-right" aria-hidden="true"></span><h5>
       </div>
      </div>
      <form method="POST">
        <input type="text" class="form-control input-sm" name="username" placeholder="Username" required><br>
        <input type="password" class="form-control input-sm" name="password" placeholder="Password" required><br>
        <input type="submit" class="form-control btn btn-primary" name="login" value="Login"><br><br>
        <p align="left" style="color:gray">Forgot your password? <a id="reset_pass" href="#"> click here </a></p>
      </form> 
  <br>
</div>
    </div>
</div>
    </div> <!-- /container -->

	</body>

</html>
<script type="text/javascript" src="js/jquery.min.js"></script>
<script src="js/dataTables/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="js/bootstrap.min.js"></script>
<script src="js/dataTables/dataTables.bootstrap.min.js"></script>
<script type="text/javascript" src="js/angular.min.js"></script>