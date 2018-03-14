<!DOCTYPE html>
<html lang="en">
    <head> 
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="viewport" content="width=device-width, initial-scale=1"/> 
        <title>Laclasse.com GRR | 403 - Access Denied</title> 
	    <link rel="shortcut icon" href="./favicon.ico">
        <style type="text/css">body,html{width:100%;height:100%;background-color:#21232a}body{color:#fff;text-align:center;text-shadow:0 2px 4px rgba(0,0,0,.5);padding:0;min-height:100%;-webkit-box-shadow:inset 0 0 100px rgba(0,0,0,.8);box-shadow:inset 0 0 100px rgba(0,0,0,.8);display:table;font-family:"Open Sans",Arial,sans-serif}html{font-family:sans-serif;line-height:1.15;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}body{margin:0}.cover{display:table-cell;vertical-align:middle;padding:0 20px}footer{position:fixed;width:100%;height:40px;left:0;bottom:0;color:#a0a0a0;font-size:14px}a{text-decoration:none;color:#fff;font-size:inherit;border-bottom:dotted 1px #707070}a{background-color:transparent;-webkit-text-decoration-skip:objects}h1{font-family:inherit;font-weight:500;line-height:1.1;color:inherit;font-size:36px}h1{font-size:2em;margin:.67em 0}.lead{color:silver;font-size:21px;line-height:1.4}</style>
    </head>
    <body> 
        <div class="cover">
            <h1><?php echo Settings::get('company'); ?></h1>
            <h2>Accès non autorisé <small>Erreur 403</small></h2>
            <p class="lead">
                <?php echo Settings::get('message_home_page'); ?>
            </p>
        </div>
        <footer><p>Retour sur <a href="<?php echo $redirect_url ?>">Laclasse.com</a></p></footer>
        </body>
</html>