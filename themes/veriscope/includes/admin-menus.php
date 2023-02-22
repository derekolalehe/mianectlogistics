<?php

if( is_admin() ) {

    add_action( 'admin_menu', 'add_custom_menu_pages' );

}

function add_custom_menu_pages(){ 

    add_submenu_page( 'options-general.php', 'Verify Customers', 'Verify Customers', 'manage_options', 'verify-existing-customers',
    'verify_existing_customers' );

}

function verify_existing_customers() {

    ?>

    <h3 style="margin-top: 60px;">Verify Existing customers</h3>
    <hr/>

    <table id="pending-customers">
        <thead>
            <tr>
                <th>FIRST NAME</th>
                <th>LAST NAME</th>
                <th>MIDDLE INITIAL</th>
                <th>USER EMAIL</th>
                <th>DATE OF BIRTH</th>
                <th>USER I.D.</th>
                <th>REGISTER DATE</th>
                <th>ACTIONS</th>
            </tr>
        <thead>
    </table>

    <div style="height: 80px;"></div>

    <?php

}

?>