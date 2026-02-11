<?php require 'base_checkout.php';

// Check if the user is logged in and retrieve user ID
if (isset($_SESSION['user'])) {
    $userID = $_SESSION['user']['id'];
} else {
    $_err['general'] = 'You must log in to edit your information.';
    header('Location: login.php');
    exit();
}


if (is_post()) {
    $name           = req('name');
    $contact_number = req('contact_number');
    $address        = req('address');
    $state          = req('state');
    $city           = req('city');
    $postal_code    = req('postal_code');

    // Validation for Billing Info Input
    if (!$name) {
        $_err['name'] = 'Name is required.';
    }

    if (!$contact_number) {
        $_err['contact_number'] = 'Contact number is required.';
    } elseif (!ctype_digit($contact_number)) {
        $_err['contact_number'] = 'Contact number should consists only number.';
    } elseif (strlen($contact_number) < 9 || strlen($contact_number) > 11) {
        $_err['contact_number'] = 'Contact number must be between 9 and 11 digits.';
    }

    if (!$address) {
        $_err['address'] = 'Address is required.';
    }

    if (!$state) {
        $_err['state'] = 'State is required.';
    }

    if (!$city) {
        $_err['city'] = 'City is required.';
    }

    if (!$postal_code) {
        $_err['postal_code'] = 'Postal code is required.';
    } elseif (!ctype_digit($postal_code)) {
        $_err['postal_code'] = 'Postal code should consists only number.';
    }

    if (isset($_POST['edit'])) {
        if ($_POST['edit'] == 'SAVE' && empty($_err)) {
            try {
                $_db->beginTransaction();

                // Update or insert the edited info
                $stmEdit = $_db->prepare('INSERT INTO editinfo (name, contact_number, address, state, city, postal_code, user_id)
                                         VALUES (?, ?, ?, ?, ?, ?, ?)
                                        ON DUPLICATE KEY UPDATE 
                                        name = VALUES(name),
                                        contact_number = VALUES(contact_number),
                                        address = VALUES(address),
                                        state = VALUES(state),
                                        city = VALUES(city),
                                        postal_code = VALUES(postal_code)');
                $stmEdit->execute([$name, $contact_number, $address, $state, $city, $postal_code, $userID]);

                $_db->commit();
                header('Location: checkout.php');
                exit();
            } catch (Exception $e) {
                $_db->rollBack();
                error_log("Error: " . $e->getMessage());
                $_err['general'] = 'An error occurred while saving. Please try again.';
            }
        } elseif ($_POST['edit'] == 'CANCEL') {
            header('Location: checkout.php');
            exit();
        }
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/checkout.css">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Alcohol Details</title>
    <link rel="shortcut icon" href="images/logo_HIPZ.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<!-- Edit Billing Info -->
<div class="edit-container">
    <!-- Display general error -->
    <?php if (!empty($_err['general'])): ?>
        <div class="error"><?= htmlspecialchars($_err['general']) ?></div>
    <?php endif; ?>

    <form method="post" action="edit_payment.php">
    <div class="row">
    <div class="col">
        <h2 class="title">Edit Billing Info</h2>
        <table class="billing-info-table">
            <tr>
                <td><label>Name:</label></td>
                <td>
                    <input type="text" name="name" placeholder="Enter your name" value="<?= htmlspecialchars($name ?? '') ?>">
                    <?php if (!empty($_err['name'])): ?>
                        <div class="error"><?= htmlspecialchars($_err['name']) ?></div>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><label>Phone Number:</label></td>
                <td>
                    <input type="text" name="contact_number" placeholder="01234567890" value="<?= htmlspecialchars($contact_number ?? '') ?>">
                    <?php if (!empty($_err['contact_number'])): ?>
                        <div class="error"><?= htmlspecialchars($_err['contact_number']) ?></div>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><label>Address:</label></td>
                <td>
                    <input type="text" name="address" placeholder="Enter your address" value="<?= htmlspecialchars($address ?? '') ?>">
                    <?php if (!empty($_err['address'])): ?>
                        <div class="error"><?= htmlspecialchars($_err['address']) ?></div>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><label>State:</label></td>
                <td>
                    <input type="text" name="state" placeholder="Enter your state" value="<?= htmlspecialchars($state ?? '') ?>">
                    <?php if (!empty($_err['state'])): ?>
                        <div class="error"><?= htmlspecialchars($_err['state']) ?></div>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><label>City:</label></td>
                <td>
                    <input type="text" name="city" placeholder="Enter your city" value="<?= htmlspecialchars($city ?? '') ?>">
                    <?php if (!empty($_err['city'])): ?>
                        <div class="error"><?= htmlspecialchars($_err['city']) ?></div>
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><label>Postal Code:</label></td>
                <td>
                    <input type="text" name="postal_code" placeholder="Enter your postal code" value="<?= htmlspecialchars($postal_code ?? '') ?>">
                    <?php if (!empty($_err['postal_code'])): ?>
                        <div class="error"><?= htmlspecialchars($_err['postal_code']) ?></div>
                    <?php endif; ?>
                </td>
            </tr>
        </table>

        <div class="button">
            <input class="edit save" type="submit" name="edit" value="SAVE">
            <input class="edit cancel" type="submit" name="edit" value="CANCEL">
        </div>
    </div>
</div>
    </form>
    </div>
</div>
</html>

