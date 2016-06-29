<h1>Campaign Builder API Goals</h1>
<hr>

<h2 id="steps">Enabled by Default</h2>
<p>The following campaign goals are enabled by default:</p>

<table class="bluetable" cellspacing=0>
	<thead>
		<tr>
			<th>Goal</th>
			<th>Integration Name</th>
			<th>Call Name</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Specific Woocommerce Purchase</td>
			<td>woopurchase</td>
			<td><i>{SKU of the Product}</i></td>
		</tr>
		<tr>
			<td>Any Woocommerce Purchase</td>
			<td>woopurchase</td>
			<td>any</td>
		</tr>
		<tr>
			<td>Woocommerce Coupon Applied</td>
			<td>woocoupon</td>
			<td><i>{Name of Coupon}</i></td>
		</tr>
		<tr>
			<td>New User Registration</td>
			<td>wooevent</td>
			<td>register</td>
		</tr>
		<tr>
			<td>User Reaches Checkout Page</td>
			<td>wooevent</td>
			<td>reachedcheckout</td>
		</tr>
	</tbody>
</table>
<br>
<h2 id="steps">Cart Tracking</h2>
<p>The following campaign goals is disabled by default and if enabled will only run if a user is logged in to wordpress.</p>
<p>Also enabling this will require considerable amount of server resources, so make sure you have a very fast server.</p>

<div class="big-row">
	
		<div class="ui-toggle<?php echo isset($iwpro->settings['advancedTracking']) && $iwpro->settings['advancedTracking'] == "yes" ? " checked" : ""; ?>" name="ia_enabled_carttracking">
						<div class="slider"></div>
						<div class="check"><i class="fa fa-check"></i></div>
						<div class="ex"><i class="fa fa-times"></i></div>
					</div>
	
	&nbsp;&nbsp;Enable Cart Tracking Campaign Goals
	
</div><br>
	<table class="bluetable carttracking<?php echo isset($iwpro->settings['advancedTracking']) && $iwpro->settings['advancedTracking'] == "yes" ? "" : " tabledisabled"; ?>" cellspacing=0>
	<thead>
		<tr>
			<th>Goal</th>
			<th>Integration Name</th>
			<th>Call Name</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Added Item to the Cart</td>
			<td>wooaddtocart</td>
			<td><i>{SKU of the Product}</i></td>
		</tr>
		<tr>
			<td>Emptied Cart</td>
			<td>wooevent</td>
			<td>emptiedcart</td>
		</tr>
	</tbody>
</table>

<br>

<br>
<h2 id="steps">Woocommerce Subscription</h2>
<p>The following campaign goals are enabled when you are using Woocommerce Subscriptions Plugin:</p>

<table class="bluetable" cellspacing=0>
	<thead>
		<tr>
			<th>Goal</th>
			<th>Integration Name</th>
			<th>Call Name</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Payment is made on a specific Subscription</td>
			<td>woosubpayment</td>
			<td><i>{SKU of the the Subscription}</i></td>
		</tr>
		<tr>
			<td>Payment is made on any Subscription</td>
			<td>woosubpayment</td>
			<td><i>{SKU of the the Subscription}</i></td>
		</tr>
		<tr>
			<td>Specific Subscription is activated</td>
			<td>woosubactivated</td>
			<td><i>{SKU of the the Subscription}</i></td>
		</tr>
		<tr>
			<td>Any Subscription is activated</td>
			<td>woosubactivated</td>
			<td>any</td>
		</tr>
		<tr>
			<td>Specific Subscription is cancelled</td>
			<td>woosubcancelled</td>
			<td><i>{SKU of the the Subscription}</i></td>
		</tr>
		<tr>
			<td>Any Subscription is cancelled</td>
			<td>woosubcancelled</td>
			<td>any</td>
		</tr>
		<tr>
			<td>Specific Subscription is suspended</td>
			<td>woosubsuspended</td>
			<td><i>{SKU of the the Subscription}</i></td>
		</tr>
		<tr>
			<td>Any Subscription is suspended</td>
			<td>woosubsuspended</td>
			<td>any</td>
		</tr>
		<tr>
			<td>Specific Subscription is expired</td>
			<td>woosubexpired</td>
			<td><i>{SKU of the the Subscription}</i></td>
		</tr>
		<tr>
			<td>Any Subscription is expired</td>
			<td>woosubexpired</td>
			<td>any</td>
		</tr>
	</tbody>
</table>
<br>
