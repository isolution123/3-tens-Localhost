<?php
//var_dump($_SESSION);
// include the pnotify css and js file
echo "<link rel='stylesheet' type='text/css' href='".base_url()."assets/users/plugins/pines-notify/jquery.pnotify.default.css'>";
echo "<script type='text/javascript' src='".base_url()."assets/users/plugins/pines-notify/jquery.pnotify.min.js'></script>";

// check values in session and show notifications as per them
if(isset($_SESSION['info'])) {
    echo "<script type='text/javascript'>
        $.pnotify({
            title: '".$_SESSION['info']['title']."',
            text: '".$_SESSION['info']['text']."',
            type: 'info',
            history: true
        });
    </script>";
    unset($_SESSION['info']);
}
elseif(isset($_SESSION['success'])) {
    echo "<script type='text/javascript'>
        $.pnotify({
            title: '".$_SESSION['success']['title']."',
            text: '".$_SESSION['success']['text']."',
            type: 'success',
            history: true
        });
    </script>";

    if(isset($_SESSION['success']['hof']) && ($_SESSION['success']['hof'] !== 0)) {
        echo "<script type='text/javascript'>
        $.pnotify({
            title: 'Head of Family updated!',
            text: 'Current client is now the Head of Family',
            type: 'success',
            history: true
        });
        </script>";
    }
    unset($_SESSION['success']);
}
elseif(isset($_SESSION['error'])) {
    echo "<script type='text/javascript'>
        $.pnotify({
            title: '".$_SESSION['error']['title']."',
            text: '".$_SESSION['error']['text']."',
            type: 'error',
            history: true
        });
    </script>";
    unset($_SESSION['error']);
}
?>