<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="<?php 
namespace ECSPrefix202306;

echo $this->charset;
?>" />
    <meta name="robots" content="noindex,nofollow,noarchive" />
    <title>An Error Occurred: <?php 
echo $statusText;
?></title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 128 128%22><text y=%221.2em%22 font-size=%2296%22>❌</text></svg>">
    <style><?php 
echo $this->include('assets/css/error.css');
?></style>
</head>
<body>
<div class="container">
    <h1>Oops! An Error Occurred</h1>
    <h2>The server returned a "<?php 
echo $statusCode;
?> <?php 
echo $statusText;
?>".</h2>

    <p>
        Something is broken. Please let us know what you were doing when this error occurred.
        We will fix it as soon as possible. Sorry for any inconvenience caused.
    </p>
</div>
</body>
</html>
<?php 
