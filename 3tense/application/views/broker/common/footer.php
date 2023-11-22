    <footer role="contentinfo">
        <div class="clearfix">
            <ul class="list-unstyled list-inline pull-left">
                <li>3Tense &copy; 2015</li>
            </ul>
            <button class="pull-right btn btn-inverse-alt btn-xs hidden-print" id="back-to-top"><i class="fa fa-arrow-up"></i></button>
        </div>
    </footer>

</div> <!-- page-container -->

<!--
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

<script>!window.jQuery && document.write(unescape('%3Cscript src="assets/js/jquery-1.10.2.min.js"%3E%3C/script%3E'))</script>
<script type="text/javascript">!window.jQuery.ui && document.write(unescape('%3Cscript src="assets/js/jqueryui-1.10.3.min.js'))</script>
-->

<?php
function scripttag($address) {echo "<script type='text/javascript' src='".base_url().$address."'></script> \n";}

    //The following plugins are used for the functionality of the theme
    scripttag('assets/users/js/enquire.js');
    scripttag('assets/users/js/jquery.cookie.js');
    scripttag('assets/users/js/jquery.nicescroll.min.js');
    scripttag('assets/users/js/placeholdr.js');  //IE8 Placeholders
    scripttag('assets/users/js/application.js');
?>
    </body>
</html>