<h1>Cart Abandon Campaign</h1>
<hr>

This is a free campaign blueprint that comes with InfusedWoo Plugin. 
<br><br>

According to a <a href="http://baymard.com/lists/cart-abandonment-rate" target="_blank">research</a> from Baymard Institute, an average of 67.9% of your site visitor abandon their shopping cart. 
As an example on how to effectively use 
the <?php echo infusedwoo_sub_menu_link('campaign_goals', 'available campaign API goals'); ?> that is built-in inside InfusedWoo, we will be building a Cart Abandon Campaign in Infusionsoft.
<br><br>

<h2>Campaign Blueprint</h2>

<img src="https://mjtokyo.s3-ap-northeast-1.amazonaws.com/Screenshot-2014-09-23-20.01.20/Screenshot-2014-09-23-20.01.20.png" style="width:100%;" />
<br><br>
This campaign blueprint is based from very good blog post:
<a href="http://blog.marketo.com/2013/12/how-to-send-perfectly-time-abandoned-cart-emails.html" target="_blank">
<i>How to Send Perfectly Timed “Abandoned Cart” Emails</i>
</a><br><br>
We will be building a campaign that sends three emails when they abandon their cart.
First email will be sent 1 hour after purchase, Second Email 24 hours thereafter. And third email after 48 hours.
<br><br>
<img src="https://mjtokyo.s3-ap-northeast-1.amazonaws.com/Screen-Shot-2014-09-23-20-22-45/Screen-Shot-2014-09-23-20-22-45.png" style="float:left; margin-right: 5px; margin-bottom: 5px; margin-right: 10px;" />
<br><br>
<b>1. Add Traffic Source: </b> You can pick any traffic source you desire. Here, the one with wordpress logo is used as our shop is
sitting on a wordpress platform.
<br><br><br><br><br>
<b>2. Reached Checkout Campaign Goal</b>: Next we utilize one of API goals InfusedWoo provides which is the "Reached Checkout" Goal.
<br><br>
To set this up, we add an API goal and set the integration name to "wooevent" and call name "reachedcheckout". 
<br><br>
<center>
<img src="https://mjtokyo.s3-ap-northeast-1.amazonaws.com/Screen-Shot-2014-09-23-20-29-52/Screen-Shot-2014-09-23-20-29-52.png"  />
</center>

<br><br>
<b>3. Cart Abandon Sequence</b>
<br><br>
<center>
<img src="https://mjtokyo.s3-ap-northeast-1.amazonaws.com/Screen-Shot-2014-09-23-20-37-42/Screen-Shot-2014-09-23-20-37-42.png" style="width: 100%;" /><br>
</center>
<br>Next we add a new sequence and connect the "reached checkout" goal to this sequence. In this example, we sent out three emails.<br>
<br>
First email is more focused on helping the customer, e.g. asking them if they have some technical difficulties or issues with payments.
This is sent 1 hour after they have reached the checkout page.<br><br>

Second email is sent 24 hours after the first email and this is a more urgent email, e.g. telling them that their cart will expire or 
some of their cart items will soon get out of stock.<br><br>

Last email is sent 48 hours after the last email. In this email you may give a discount or a coupon code.

<img src="https://mjtokyo.s3-ap-northeast-1.amazonaws.com/Screen-Shot-2014-09-23-20-53-18/Screen-Shot-2014-09-23-20-53-18.png" style="float: right; margin-left: 10px; margin-bottom: 5px;"/>
<br><br>
<b>4. Purchase Goal: </b> Last but not the least, we add an Purchase Goal via Campaign API goal. We set the API Goal's integration name
to "woopurchase" and call name to "any".<br><br>
This is a very important step. Without this goal, all customers going to your checkout page will receive all cart abandon emails. 
<br>
<hr>

