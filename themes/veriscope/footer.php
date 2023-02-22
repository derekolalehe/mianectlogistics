<div id="site-footer">

    <div id="footer-contents">

        <div class="footer-widget-area"><?php dynamic_sidebar( 'footer_area_1' ); ?></div>

        <div class="footer-widget-area"><?php dynamic_sidebar( 'footer_area_2' ); ?></div>

        <div class="footer-widget-area"><?php dynamic_sidebar( 'footer_area_3' ); ?></div>

    </div>

</div>

<div id="footer-copyright">
        <p>&copy;&nbsp;Copyright <?php echo date("Y");?> MIANECT</p>
        <a href="https://www.facebook.com/mianect"><img alt="mianect facebbok" src="<?php echo get_template_directory_uri() . 
        '/assets/icons/facebook-logo.png';?>"/></a>
</div>

<?php wp_footer();?>

</body>

</html>