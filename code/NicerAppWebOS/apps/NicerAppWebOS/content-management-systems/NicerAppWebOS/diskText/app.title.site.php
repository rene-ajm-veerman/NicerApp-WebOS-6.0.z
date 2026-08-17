<?php
    global $naWebOS;
    global $na_apps_structure;
  //  echo '<pre>'; var_dump ($naWebOS->view); die();
    foreach ($naWebOS->view as $viewFolder => $viewSettings) break; // just 1 view per URL for now.
    $filePath = $naWebOS->codePath.$viewSettings['title'];
    echo trim(require_return ($filePath));
?>
