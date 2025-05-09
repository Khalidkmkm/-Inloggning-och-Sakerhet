<?php
require_once('lib/PageTemplate.php');
require_once('Models/Database.php');
require_once('Utils/validator.php');
# trick to execute 1st time, but not 2nd so you don't have an inf loop
if (!isset($TPL)) {
    $TPL = new PageTemplate();
    $TPL->PageTitle = "Regsier";
    $TPL->ContentBody = __FILE__;
    include "layout.php";
    exit;
}

$dbContext = new Database();


$data = $_POST ?? [];
$errorMessages = [];
$valid = new Validator($data);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $data['email'];
    $password = $data['password'];
    $passwordRepeat = $data['passwordRepeat'];
    $name = $data['name'];
    $streetAddress = $data['streetAddress'];
    $postalCode = $data['postalCode'];
    $city = $data['city'];

    $valid->field('email')->required()->email();
    $valid->field('password')->required()->min_len(7)->max_len(20);
    $valid->field('passwordRepeat')->equals($password);
    $valid->field('name')->required()->min_len(3)->max_len(50);
    $valid->field('streetAddress')->required()->min_len(3)->max_len(50);
    $valid->field('postalCode')->required()->max_len(5);
    $valid->field('city')->required()->max_len(50);

    if (!$valid->is_valid()) {
        $errorMessages = $valid->error_messages;
    } else {
        try {
            $userId = $dbContext
                ->getUsersDatabase()
                ->getAuth()
                ->register($email, $password, $name);


        $dbContext->addUserDetails($userId, $name, $streetAddress, $postalCode, $city);
            header('Location: /');
            exit;

            

        } catch (\Delight\Auth\InvalidEmailException $e) {
            $errorMessages['email'] = 'Ogiltig e-postadress';
        } catch (\Delight\Auth\InvalidPasswordException $e) {
            $errorMessages['password'] = 'Ogiltigt lösenord';
        } catch (\Delight\Auth\UserAlreadyExistsException $e) {
            $errorMessages['email'] = 'Användaren finns redan';
        } catch (Exception $e) {
            error_log($e->getMessage());
            $errorMessages['general'] = 'Något gick fel, var god försök igen';
        }
    }
}
?>
<p>
<div class="row">

<div class="row">
                <div class="col-md-12">
                    <div class="newsletter">
                        <p>User<strong>&nbsp;REGISTER</strong></p>
                        <?php if (isset($errorMessages['general'])): ?>
                        <div class="alert"><?= $errorMessages['general'] ?></div>
                         <?php endif; ?>
                        <form method="POST" >
                            <input class="input" type="email"  name="email" placeholder="Enter Your Email" value="<?= htmlspecialchars($data['email'] ?? '') ?>">
                            <div class="error"><?= $errorMessages['email'] ?? '' ?></div>
                            <br/>
                            <br/>
                            <input class="input" type="password" name="password" placeholder="Enter Your Password">
                            <div class="error"><?= $errorMessages['password'] ?? '' ?></div>
                            <br/>
                            <br/>
                            <input class="input" type="password" name="passwordRepeat" placeholder="Repeat Password">
                            <div class="error"><?= $errorMessages['passwordRepeat'] ?? '' ?></div>
                            <br/>
                            <br/>
                            <input class="input" type="text" name="name" placeholder="Name" value="<?= htmlspecialchars($data['name'] ?? '') ?>">
                            <div class="error"><?= $errorMessages['name'] ?? '' ?></div>
                            <br/>
                            <br/>
                            <input class="input" type="text" name="streetAddress" placeholder="Street address" value="<?= htmlspecialchars($data['streetAddress'] ?? '') ?>">
                            <div class="error"><?= $errorMessages['streetAdress'] ?? '' ?></div>
                            <br/>
                            <br/>
                            <input class="input" type="text" name="postalCode" placeholder="Postal code" value="<?= htmlspecialchars($data['postalCode'] ?? '') ?>">
                            <div class="error"><?= $errorMessages['postalCode'] ?? '' ?></div>
                            <br/>
                            <br/>
                            <input class="input" type="text" name="city" placeholder="City" value="<?= htmlspecialchars($data['city'] ?? '') ?>">
                            <div class="error"><?= $errorMessages['city'] ?? '' ?></div>
                            <br/>
                            <br/>
                            <button type="submit">Register Now!</button>
                        </form>
                    </div>
                </div>
            </div>


</div>
    

</p>