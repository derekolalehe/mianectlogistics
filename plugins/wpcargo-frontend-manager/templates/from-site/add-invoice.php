<?php 
session_start();
global $wpdb;

$user_id = get_current_user_id();
$current_user = wp_get_current_user();
$username = $current_user->user_login;

$tk="";

if( isset ( $_POST[ "wpcfe_new_tracking_number" ] ) ):
    $tk = $_POST['wpcfe_new_tracking_number'];
endif;

if( isset ($_POST[ "upload-tk-inv" ] ) && $_POST['tk-num'] != "" ):

    $tk = $_POST['tk-num'];
    
    if( $_FILES["upl-inv"]["error"] != 4 ):

        if( !is_dir( $_SERVER['DOCUMENT_ROOT'] . "/wp-content/themes/veriscope/invoices/" . $username ) ):

            mkdir( $_SERVER['DOCUMENT_ROOT'] . "/wp-content/themes/veriscope/invoices/" . $username );

        endif;

        $invoice_files = scandir( $_SERVER['DOCUMENT_ROOT'] . "/wp-content/themes/veriscope/invoices/" . $username . "/" );

        for( $j=0;$j<sizeof( $invoice_files );$j++ ):

            if( explode( '.', $invoice_files[$j] )[0] == $tk ):

                unlink( $_SERVER['DOCUMENT_ROOT'] . "/wp-content/themes/veriscope/invoices/" . $username . "/" . $invoice_files[$j] );

            endif;

        endfor;

        $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/wp-content/themes/veriscope/invoices/" . $username . "/";        
        $FileExtension = end((explode(".", $_FILES["upl-inv"]["name"])));
        $target_file = $target_dir . $tk . "." . $FileExtension;
        $uploadOk = 1;
        $FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        if($FileType != "pdf" && $FileType != "png" && $FileType != "jpg" && $FileType != "jpeg" ) {
        $msg = "Sorry, only .PDF, .PNG, .JPG & .JPEG files are allowed.";
        $uploadOk = 0;
        } 

        if ($_FILES["upl-inv"]["size"] > 1000000) {
            $msg = "Sorry, your file is too large.";
            $uploadOk = 0;
        }


        if (move_uploaded_file($_FILES["upl-inv"]["tmp_name"], $target_file)) {
            $msg =  "The file ". basename( $_FILES["upl-inv"]["name"]). " has been uploaded.";
        } else {
            $msg =  "Sorry, there was an error uploading your file.";
        }

    else:

        echo "file error";

    endif;

    $_SESSION['msg'] = $msg;

endif;

?>

<form method="post" action="" enctype="multipart/form-data" class="add-invoice">
    <div id="tracking-number" class="row">
    <div class="col-lg-4"></div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <label class="sr-only" for="wpcfe_shipment_title">Add Invoice</label>
                    <div class="mt-3"></div>
                    <div class="form-group">
                        <label for="upl-inv">Upload Invoice</label>
                        <input type="file" class="form-control-file" id="upl-inv" name="upl-inv">
                    </div>
                </div>
            </div>
            <div class="mt-5"></div>
            <input type="hidden" name="tk-num" value="<?php echo $tk;?>"/>
            <button type="submit" name="upload-tk-inv" class="btn btn-info btn-fill btn-wd btn-block">
                Upload
            </button>
        </div>
        <div class="col-lg-4"></div>        
    </div>
    
    <div class="mt-5"></div>
</form>