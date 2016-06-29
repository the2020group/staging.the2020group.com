<ul id="sidebar-dash">
  <li<?php if ( is_page('dashboard'))  { echo ' class="current_page_item"'; } ?>><a href="/dashboard/"><span>Edit Details</span></a></li>
  <li<?php if ( is_page('my-purchases'))  { echo ' class="current_page_item"'; } ?>><a href="/dashboard/my-purchases"><span>Purchases</span></a></li>
  <li<?php if ( is_page('my-cpd-record'))  { echo ' class="current_page_item"'; } ?>><a href="/dashboard/my-cpd-record"><span>CPD Record</span></a></li>
  <li<?php if ( is_page('my-upcoming-events'))  { echo ' class="current_page_item"'; } ?>><a href="/dashboard/my-upcoming-events"><span>Upcoming Events</span></a></li>
  <li<?php if ( is_page('your-recommendations'))  { echo ' class="current_page_item"'; } ?>><a href="/dashboard/your-recommendations">Recommendations</a></li>
  <li<?php if ( is_page('practice-development-tools'))  { echo ' class="current_page_item"'; } ?>><a href="/dashboard/practice-development-tools">Practice Development Tools</a></li>
  <li<?php if ( is_page('previous-newsletters'))  { echo ' class="current_page_item"'; } ?>><a href="/dashboard/previous-newsletters">Previous Newsletters</a></li>
  <li<?php if ( is_page('my-subscriptions'))  { echo ' class="current_page_item"'; } ?>><a href="/dashboard/my-subscriptions">My Subscriptions</a></li>
</ul>