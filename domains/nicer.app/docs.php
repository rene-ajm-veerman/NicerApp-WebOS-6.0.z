<!DOCTYPE html>
<html lang="">
  <head>
    <meta charset="utf-8">
    <title>NicerApp WebOS - Documentation</title>
  </head>
  <body>
    <header>
      <h1>NicerApp WebOS - Documentation</h1>
      <?php
        require_once (dirname(__FILE__).'/NicerAppWebOS/boot.php');
        global $date;
        echo 'Date time : '.$date;
      ?>
    </header>
    <main>
      <h2>Index</h2>
      <ol>
        <li>
            <p><a href="#nawos-">Languages used, place in the website's ecosystem.</a></p>
            <p>A <a href="https://nicer.app">NicerApp installation</a> (MIT <a href="https://nicer.app/license">licensed</a>) fits perfectly alongside other content management systems, like Drupal, Wordpress, or Godaddy.com Aero.</p>
            <p>It uses PHP as it's primary serverside language, which outputs HTML+CSS+JS and other browser languages like SVG, Canvas and WebGL on demand.</p>
        </li>
        <li>
            <p><a href="#nawos-">Components Used</a></p>
            <ol>
            <li>
                <?php echo file_get_contents(dirname(__FILE__).'/NicerAppWebOS/logic.messaging/README.html'); ?>
            </li>
            <li>
                <?php echo file_get_contents(dirname(__FILE__).'/NicerAppWebOS/logic.tasksManager/README.html'); ?>
            </li>
            <li>
                <?php echo file_get_contents(dirname(__FILE__).'/NicerAppWebOS/logic.databases/generalizedDatabasesAPI-1.0.0/README.html'); ?>
            </li>
            </ol>
        </li>
      </ol>
    </main>
    <footer></footer>
  </body>
</html>

