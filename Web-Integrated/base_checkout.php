<?php

// ============================================================================
//                                      PHP Setups
// ============================================================================
date_default_timezone_set('Asia/Kuala_Lumpur');
session_start();


// ============================================================================
//                              General Page Functions
// ============================================================================

// Is GET request?
function is_get() {
    return $_SERVER['REQUEST_METHOD'] == 'GET';
}

// Is POST request?
function is_post(){
    return $_SERVER['REQUEST_METHOD'] == 'POST';
}

// Obtain GET parameter
function get($key, $value = null) {
    $value = $_GET[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain POST parameter
function post($key, $value = null) {
    $value = $_POST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Obtain REQUEST (GET and POST) parameter
function req($key, $value = null) {
    $value = $_REQUEST[$key] ?? $value;
    return is_array($value) ? array_map('trim', $value) : trim($value);
}

// Redirect to URL
function redirect($url = null) {
    header("Location: $url");
    exit();
}

// Set or get temporary session variable
function temp($key, $value = null) {
    if ($value !== null) {
        $_SESSION["temp_$key"] = $value;
    }
    else {
        $value = $_SESSION["temp_$key"] ?? null;
        unset($_SESSION["temp_$key"]);
        return $value;
    }
}


// ============================================================================
//                                  HTML Helper
// ============================================================================

// Encode HTML special characters
function encode($value) {
    return htmlentities($value);
}

// Generate radio list
function html_radios($key, $items, $br = false) {
    $value = encode($GLOBALS[$key] ?? '');
    echo '<div>';
    foreach ($items as $id => $text) {
        $_methods = $id == $value ? 'checked' : '';
        echo "<label><input type = 'radio' id = '{$key}_$id' name = '$key' value = '$id' $_methods>$text</label>";

        if ($br) {
            echo '<br>';
        }
    } echo '</div>';
}

// Generate bank selection list
function bank_options($key, $items, $default = '- Select a bank -', $attr = '' ) {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<select id='$key' name = '$key' $attr>";
    if ($default !== null) {
        echo "<option value = ''>$default</option>";
    }
    foreach ($items as $id => $text) {
        $_bankings = $id == $value ? 'selected' : '';
        echo "<option value = '$id' $_bankings>$text</option>";
    }
    echo '</select>';
}

function html_text($key, $attr = '') {
    $value = encode($GLOBALS[$key] ?? '');
    echo "<input type = 'text' id = '$key' name = '$key' value = '$value' $attr>";
}

function html_hidden($key, $attr = '') {
    $value ??= encode($GLOBALS[$key] ?? '');
    echo "<input type='hidden' id='$key' name='$key' value='$value' $attr>";
}


// ============================================================================
//                              Error Handlings
// ============================================================================

// Global error array
$_err = [];

// Generate <span class = 'err'>
function err($key) {
    global $_err;
    if ($_err[$key] ?? false) {
        echo "<span class = 'err error_red' > $_err[$key]</span>";
    }
    else {
        echo '<span></span>';
    }
}


// ============================================================================
//                                 Shopping Cart
// ============================================================================

// Get shopping cart
function get_cart() {
    return $_SESSION['cart'] ?? [];
}

// Set shopping cart
function set_cart($cart = []) {
    $_SESSION['cart'] = $cart;
}

// Update shopping cart
function update_cart($id, $quantity) {
    $cart = get_cart();

    if ($quantity >= 1 && $quantity <= 10 && is_exists($id, 'product', 'id')) {
        $cart[$id] = $quantity;
        ksort($cart);
    }
    else {
        unset($cart[$id]); // Remove product
    }

    set_cart($cart);
}


// ============================================================================
//                        Database Setups & Functions
// ============================================================================
$_db = new PDO('mysql:dbname=alcohol_store', 'root', '', [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
]);

// Is unique?
function is_unique($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() == 0;
}

// Is exists?
function is_exists($value, $table, $field) {
    global $_db;
    $stm = $_db->prepare("SELECT COUNT(*) FROM $table WHERE $field = ?");
    $stm->execute([$value]);
    return $stm->fetchColumn() > 0;
}


// ============================================================================
//                        Constants and Variables
// ============================================================================

// Global Constants and Variables
$_methods = [
    'C' => 'Card Payment',
    'B' => 'Online Banking',
];

$_bankings = [
    'ambank' => 'AmBank',
    'bankIslam' => 'Bank Islam',
    'bankrakyat' => 'Bank Rakyat',
    'cimb' => 'CIMB',
    'hsbc' => 'HSBC',
    'maybank' => 'Maybank',
    'publicbank' => 'Public Bank',
    'rhb' => 'RHB Bank',
    'uob' => 'UOB',
];

$bank_img = [
    'ambank' => 'images/ambank.png',
    'bankIslam' =>'images/bankIslam.png',
    'bankrakyat' => 'images/bankrakyat.png',
    'cimb' => 'images/cimb.png',
    'hsbc' => 'images/hsbc.png',
    'maybank' => 'images/maybank.png',
    'publicbank' => 'images/publicbank.png',
    'rhb' => 'images/rhb.png',
    'uob' => 'images/uob.jpg',
];

$month = [
    'JAN', 'JANUARY', 'FEB', 'FEBRUARY', 'MAR', 'MARCH',
    'APR', 'APRIL', 'MAY', 'JUN', 'JUNE', 'JUL', 'JULY',
    'AUG', 'AUGUST', 'SEP', 'SEPTEMBER', 'OCT', 'OCTOBER', 
    'NOV', 'NOVEMBER', 'DEC', 'DECEMBER',
];