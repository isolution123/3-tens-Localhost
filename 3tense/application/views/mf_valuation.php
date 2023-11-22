<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
</head>
<body>
<?=form_open('MF_scripts/valuation'); ?>
    <h3>Broker List</h3>


  <input type="checkbox" id='selectAll'/> Select All<br/>

    <div id='broker_list'style='width:800px;'>
    <?php
        foreach ($brokers as $broker)
          {
            echo "<input name='check_list[]'  value='".$broker->id."' type='checkbox' id='".$broker->id."'>".$broker->id."  ".$broker->name ."<br>";
          }
    ?>
    </div>
    <input type="submit" name='submit' value='submit' id="check_all">
<?php echo  Form_close (); ?>
</body>
</html>

<script>
$(document).ready(function ()
{
  $('body').on('click', '#selectAll', function ()
  {
    if ($(this).hasClass('allChecked')) {
        $('input[type="checkbox"]', '#broker_list').prop('checked', false);
    } else {
        $('input[type="checkbox"]', '#broker_list').prop('checked', true);
    }
    $(this).toggleClass('allChecked');
  })
});

</script>
