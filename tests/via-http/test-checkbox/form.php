<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>DataTable - test with checkbox</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="../_css-reset.css">
        <link rel="stylesheet" href="../_basic-css.css">
    </head>
    <body>
        <?php if ((isset($_POST['action']) && $_POST['action'] == 'view_selected') || (isset($_POST['action2']) && $_POST['action2'] == 'view_selected')) { ?> 
        <h1>View selected</h1>
        <pre><?php 
            if (isset($_POST['id'])) {
                print_r($_POST['id']);
            }
        ?></pre>
        <?php } else { ?> 
        <h1>Error</h1>
        <p>Please select action.</p>
        <?php }// endif; ?> 

        <hr>

        <h3>Debug</h3>
        <pre><?php
            print_r($_POST);
        ?></pre>
    </body>
</html>