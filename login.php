<?php
ob_start();

define("INCLUDED", true);

if(!session_start()){
    header($_SERVER['SERVER_PROTOCOL']." 500 Internal Server Error");
    ob_flush();
    die();
}

$strong = false;
$t = openssl_random_pseudo_bytes(16,$strong);
if($t == false) {
    header($_SERVER['SERVER_PROTOCOL']." 500 Internal Server Error");
    ob_flush();
    die();
}

define("XSRF_TOKEN", bin2hex($t));
$OLD_TOKEN = $_SESSION['XSRF_TOKEN'];
$_SESSION['XSRF_TOKEN'] = XSRF_TOKEN;


$error = false;
$errors = array();

require_once dirname(__FILE__) . "/include/audit/inc_audit_log_function.php";

if(isset($_POST['submit']) && $_POST['submit'] == "login") {
    $in_token = filter_input(INPUT_GET, "xsrf", FILTER_SANITIZE_STRING);
    if($in_token != $OLD_TOKEN) {
        require_once dirname(__FILE__). "/xsrf_error.php";
        ob_flush();
        exit(0);
    }

    require_once dirname(__FILE__)."/config.php";

    $inputs = filter_input_array(INPUT_POST, array(
        'username' => FILTER_SANITIZE_STRING,
        'password' => FILTER_SANITIZE_STRING
    ));

    /** @noinspection PhpUndefinedVariableInspection */
    $mysqli = new mysqli(DB_SERVER, DB_USER, DB_PASS, DB_NAME);

    if(!($s = $mysqli->prepare("SELECT * FROM users WHERE username = ?"))){
        $error = true;
        array_push($errors, "Error logging in ".$mysqli->errno);
    }
    if(!($s->bind_param("s", $inputs['username']))) {
        $error = true;
        array_push($errors, "Error logging in ".$mysqli->errno);
    }
    if(!($s->execute())){
        $error = true;
        array_push($errors, "Error logging in ".$mysqli->errno);
    }

    if(!$error) {
        $r = $s->get_result();
        $data = $r->fetch_assoc();
        if($data == null) {
            $error = true;
            audit(null, "login_fail", json_encode(["from" => $_SERVER['REMOTE_ADDR']]));
        } else {
            if(!password_verify($inputs['password'], $data['password'])){
                $error = true;
                audit(null, "login_fail", json_encode(["from" => $_SERVER['REMOTE_ADDR']]));
            } else {
                $_SESSION['userdata'] = array(
                        'id' => $data['id'],
                        'username' => $data['username'],
                        'given_name' => $data['given_name'],
                        'surname' => $data['surname'],
                        'common_name' => $data['surname'].", ". $data['given_name']
                );
                $return = filter_input(INPUT_GET, "return", FILTER_SANITIZE_URL);
                audit($data['id'], "login", json_encode(["from" => $_SERVER['REMOTE_ADDR']]));
                /** @noinspection PhpUndefinedVariableInspection */
                if(in_array(parse_url($return, PHP_URL_HOST), $TRUSTED_HOSTS)) {
                    header("Location: ".$return, true, 302);
                } else {
                    header("Location: verify.php", true, 302);
                }
                ob_flush();
            }
        }
    }
}
if(!isset($_POST['submit']) || $_POST['submit'] != "login" || $error){
?>
<html lang="en" data-lt-installed="true">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.84.0">
    <title>LOGIN</title>

    <!-- Bootstrap core CSS -->
    <link href="<?= rtrim(dirname($_SERVER['PHP_SELF']),"/"); ?>/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <meta name="theme-color" content="#7952b3">


    <style>
        .bd-placeholder-img {
            font-size: 1.125rem;
            text-anchor: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            user-select: none;
        }

        @media (min-width: 768px) {
            .bd-placeholder-img-lg {
                font-size: 3.5rem;
            }
        }

        html,
        body {
            height: 100%;
        }

        body {
            display: flex;
            align-items: center;
            padding-top: 40px;
            padding-bottom: 40px;
            background-color: #f5f5f5;
        }

        .form-signin {
            width: 100%;
            max-width: 330px;
            padding: 15px;
            margin: auto;
        }

        .form-signin .checkbox {
            font-weight: 400;
        }

        .form-signin .form-floating:focus-within {
            z-index: 2;
        }

        .form-signin input[type="text"] {
            margin-bottom: -1px;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 0;
        }

        .form-signin input[type="password"] {
            margin-bottom: 10px;
            border-top-left-radius: 0;
            border-top-right-radius: 0;
        }
    </style>
</head>
<body class="text-center container">

<main class="form-signin py-5 row">
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>?xsrf=<?php echo XSRF_TOKEN; ?>">
        <img class="mb-4" src="<?= LOGO_WEB_PATH; ?>" alt="" height="57">
        <h1 class="h3 mb-3 fw-normal">Please sign in</h1>
        <?php if($error){ ?>
        <div class="alert alert-error" role="alert">
            Login Failed!
        </div>
        <?php } ?>
        <div class="form-floating">
            <input type="text" class="form-control" id="floatingInput" placeholder="username" name="username">
            <label for="floatingInput">Username</label>
        </div>
        <div class="form-floating">
            <input type="password" class="form-control" id="floatingPassword" placeholder="Password" name="password">
            <label for="floatingPassword">Password</label>
        </div>

        <div class="checkbox mb-3">
            <label>
                <input type="checkbox" value="remember-me"> Remember me
            </label>
        </div>
        <button class="w-100 btn btn-lg btn-primary" type="submit" name="submit" value="login">Sign in</button>
    </form>
    <footer class="my-5 pt-5 text-muted text-center text-small">
        <p class="mb-1">© 2020-2020 Bennet Becker, Dresden</p>
        <ul class="list-inline">
            <li class="list-inline-item"><a href="#">Privacy</a></li>
            <li class="list-inline-item"><a href="#">Terms</a></li>
            <li class="list-inline-item"><a href="#">Support</a></li>
        </ul>
    </footer>
</main>


</body>
</html>
<?php }
ob_flush();
?>