chromecast
==========

Chromecast Sandbox

Make sure to whitelist your chromecast. https://developers.google.com/cast/whitelisting

Install the chromecast extension and be sure to add your domain to it:

* On the Cast extension icon in the browser's upper right corner (next to the address field), right-click and select Options.
* The Google Cast extension options page opens in a new tab.
* On the blue Cast icon, in the page's upper left corner, click four (4) times.
* The Developer Settings appear.
* In the Cast SDK additional domains field, enter your application's domain, for example, "www.mydomain.com" and click Add.

Drop these files onto the root directory of your subdomain that your chromecast api keys point to, then visit http://www.mydomain.com/sender.php or http://www.mydomain.com/youtube.php to send stuff to your chromecast.
