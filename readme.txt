=== JoeBooking - Appointment Scheduling For Providers and Salons ===

Contributors: HitCode
Tags: appointment scheduling, appointment booking, client scheduling, customer calendar, calendar booking, service scheduling, salon scheduling software
License: GPLv2 or later

Stable tag: trunk
Requires at least: 3.3
Tested up to: 4.0

Switch to accept appointments for your services right from your WordPress powered website. 

== Description ==

JoeBooking is a WordPress appointment scheduling plugin that allows to accept service bookings online.
It is designed specifically for service professionals like massage therapists, consultants, tutors, instructors, photographers, stylists, dog groomers and others who need to schedule their time with clients online. 
JoeBooking appointment scheduling allows the owner to manage the plugin through a powerful admin panel and offers a self-service customer appointment booking form embedded into a page or a post with a shortcode.

Please visit [our website](http://www.joebooking.com "WordPress Appointment Scheduling") for more info and [get the Pro version now!](http://www.joebooking.com/order/).

###Pro/Salon Pro Version Features###

* Manage payments
* Appointment packages
* Promotions and coupons
* Custom fields
* Attachments
* Multiple providers
* Multiple locations

Make sure you don't overbook yourself while keeping it easy for your potential clients to book time with you.
Switch to manage your service offerings and client appointments right from your WordPress powered website. 

== Support ==
Please contact us at http://www.joebooking.com/contact/

Author: HitCode
Author URI: http://www.joebooking.com

== Installation ==

1. After unzipping, upload everything in the `joebooking` folder to your `/wp-content/plugins/` directory (preserving directory structure).

2. Activate the plugin through the 'Plugins' menu in WordPress.

== Upgrade Notice ==
The upgrade is simply - upload everything up again to your `/wp-content/plugins/` directory, then go to the JoeBooking menu item in the admin panel. It will automatically start the upgrade process if any needed.

== Changelog ==

= 6.2.3 =
* Now the coupon codes can be used in the admin area for existing appointments too.
* BUG: when viewing the appointments of a customer in Customers > Appointments, appointments of all of customers were displayed
* BUG: month names were not translated if a foreign language was enabled.

= 6.2.2 =
* Pay at our office button at the final confirmation form if this option is enabled.
* The shortcode output was appearing before the page text even if it was placed after.
* A few minor optimizations.

= 6.2.1 =
* Made it compatible with Developer Mode plugin so that "Developers" are also admins in JoeBooking
* Now when downloading the appointments list in CSV (Excel) format, it's possible to select which columns are included
* BUG: the availability week wizard assigned a wrong location if you first filtered the availability by location.

= 6.2.0 =
* BUG: error in payments form for payment gateways other than Paypal
* BUG: when upgrading from older versions (4 and 5) there might be some availability timeblocks hidden in the admin area
* BUG: quite a specific bug, if you have the "filter customers" plugin enabled, and a customer purchases a package, then the notification email was sent to all admins rather than those who can see this customer.
* Calendar view preference (month, week, or next days) now is saved in the admin panel
* Multilanguage interface, as well as online edit for the text used within the applicatation
* Day view extended to display several days that have appointments or availability defined
* Admin now can generate an invoice for an appointment and send it to the customer if needed
* Appointment cart is cleared upon logout to prevent issues on shared computers
* Extended the Event Actions module, now there is a new option to add automatically add an item to a first invoice of a customer, for example a registration fee.

= 6.1.2 =
* Now it is possible to make use of full HTML including <html> and <head> tags in the message templates, previously it was allowed only in the header and the footer, for the body if the head part was used it got corrupted.
* BUG: locations, resources and services sometimes were not sorted according to settings defined in the admin area.
* BUG: a customer could not make use of balance if a package was configured to allow only a selection of services rather than all.
* BUG: if more than one appointment were booked at a time by a customer, then the payment amounts for all appointments were set as the price of the last appointment.
* Staff members now can not delete customer accounts, only administrators can do that.
* Appointment notes if any are now also displayed in the calendar and list views in the admin area.
* BUG: session handling might be conflicting with other plugins or themes

= 6.1.1 =
* Now you can add parameters for shortcode: fix_location, fix_service, fix_resource to filter these options in the front-end.
* BUG: in the customer iCal export file cancelled and no-show appointments were included. Now only approved and not completed appointments are listed.
* Admin or staff members can again give the reason for rejecting appointment, in addition now it is also stored in the appointment change history.
* A setting if to count the min advance booking period from now or from tomorrow's earliest available time.
* In the admin area now there is a filter for customers with restrictions, i.e. with Email Not Confirmed, Not Approved, Suspended to easily locate them.
* Staff members now can not completely delete appointments, only administrators can do that.
* Now invoices can be deleted in the admin area.

= 6.1.0 =
* BUG: Appointments in customer panel were not properly sorted thus appointments from one month might have appeared under another month title.
* Hide SMS configuration and logs from staff members
* BUG: SMS text message were sent even if "No Notification" checkbox was on
* BUG: "Filter customers for admin" plugin was not working
* Added appointment status legend in the customer area
* BUG: iCal and Excel (CSV) export links were not working in the customer area
* BUG: Synchronization link was not displayed to staff members
* BUG: calendar popup was shifted in position in JoeBooking admin area

= 6.0.6 =
* Added a link to customer info in the appointment dropdown in admin area
* Fixed wrong time shown for appointments in admin area when lead-out was enabled
* Minor code updates and fixes

= 6.0.5 =
* Added an option to archive a location
* Added a link to pay for existing appointments in the customer area
* Minor code updates and fixes

= 6.0.4 =
* Switched back to built-in WordPress update check functions

= 6.0.3 =
* Added the appointment creation date in the admin appointment calendar and list views as well as in CSV/Excel export file
* Added a label for internal providers in the admin appointment calendar and list views.
* Added a configuration option to pick a location or a resource randomly from available ones in the front end. So if you have multiple locations or resources, but it is not required to let customers know which one is booked, you can hide this information from them.

= 6.0.2 =
* Added an option to specify if an availability time slot is valid on odd or even weeks (or all, as before)
* Event actions or hooks feature (Premium versions).
* Minor code optimization and fixes 

= 6.0.1 =
* A slight CSS fix to escape conflicts with some WP admin themes
* Location capacity booking check fix

= 6.0.0 =
* Initial public release


Thank You.