<?php
// Include config.php to load BASE_URL if not already included
if (!defined('BASE_URL')) {
    include_once __DIR__ . '/../../utility/config.php';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Hi:D</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/utility/style2.css?v=<?php echo time(); ?>">
</head>