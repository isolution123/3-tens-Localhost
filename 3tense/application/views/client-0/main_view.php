<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utd-8" />
    <meta http-equiv="X-UA-Compatible" content="IE-9" />
  </head>
  <body>

      <?php
      echo $error;
       echo form_open_multipart('client/main/upload');?>
      <input type="file" name="userfile" />
      <input type="submit" name="submit" value="Upload Files">
    </form>

  </body>
</html>
