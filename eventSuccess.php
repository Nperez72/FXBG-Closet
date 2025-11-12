<?php
    session_cache_expire(30);
    session_start();
    header("refresh:2;url=addEvent.php");
?>

    <!DOCTYPE html>
    <html>
        <head>
            <?php require_once('universal.inc') ?>
            <title>Fredericksburg SPCA | Create Event</title>
        </head>
        <body style="margin: 0; display: flex; flex-direction: column; min-height: 100vh;">
            <?php require_once('header.php') ?>
            <div style="flex: 1; display: grid; justify-items: center; align-items: start; padding-top: 0rem;">
                <h1>Event Created!</h1>
            </div>
        </body>
    </html>