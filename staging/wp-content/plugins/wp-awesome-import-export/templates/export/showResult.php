<?php
/*
 *  (View file)
 * 
 *  ALL Variables available from the called script
 *  
 *  Show Export Result
 */
global $error;
$output = array();
?>
<div id='loadingmessage'></div>
<div class="result" style="display:none" id="result<?php echo $operationCategory;?>">
<strong class='red'><?php echo $error;?></strong>
<table class='widefat' style="background: #34495E; border: none;">
<thead>
<tr><th colspan='2' style="background:#E86850;color:#FFF; border-bottom:2px solid #DDD;"><strong><?php _e( 'Result', 'wpacs' ); ?></strong></th></tr>
</thead>
<tbody>
<tr><th><?php _e( 'Records Read:', 'wpacs' ); ?>:</th>
<td class="recordsRead" id="recordsRead<?php echo $operationCategory;?>">
<strong><?php echo $output["recordsRead"];?></strong></td></tr>
<tr><th><?php _e( 'Download Link:', 'wpacs' ); ?></th>
<td class="downloadLink" id="downloadLink<?php echo $operationCategory;?>">
    <strong><?php echo $output["recordsInserted"];?></strong></td></tr>
</tbody>
</table>
</div>