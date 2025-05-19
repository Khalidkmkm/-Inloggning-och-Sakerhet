<?php
require_once('Models/Database.php');
require_once('lib/PageTemplate.php');
# trick to execute 1st time, but not 2nd so you don't have an inf loop
if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "Login";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}

$dbContext = new Database();

$errorMessage = "";
$username = ""; 
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $_POST['username'];
    $password = $_POST['password'];

    try{  
       
        $dbContext->getUsersDatabase()->getAuth()->login($username, $password);
        header('Location: /');
        exit;
    }
    catch(Exception $e){
        $errorMessage = "Kunde inte logga in";
    }
}else{
    
}


?>
<p>
<div class="row">

<div class="row">
                <div class="col-md-12">
                    <div class="newsletter">
                        <p>User<strong>&nbsp;LOGIN</strong></p>
                        <?php if($errorMessage): ?>
      <div class="error"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>
                        <form method="POST">
                             <input class="input" placeholder="Enter your Email" type="text" name="username" value="<?= htmlspecialchars($username) ?>">
                            <br/>
                            <br/>
                            <input class="input" type="password" name="password" placeholder="Enter Your Password">
                            <br/>
                            <br/>
                            <input type="submit" value="Login">
                        </form>
                        <a href="">Forgot password?</a>
                    </div>
                </div>
            </div>


</div>
    

</p>