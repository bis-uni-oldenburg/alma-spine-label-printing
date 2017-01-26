# alma-spine-label-printing
<strong>Printing spine labels for ExLibris Alma bib items</strong>

<strong>Purpose:</strong>
- Automatically print spine labels for currently inventoried items
- Print custom spine labels via web frontend.

The application consists of the following components:
- A (potentially cronjob-triggered) daily request for newly inventoried items via the Analytics API (returning the item barcodes).
- A Bibs API request for each barcode returning the item details needed for the label (which are: call number, alternative call number, author, title).
- Parsing and formatting call number, title, and author information to fit in the spine labels.
- Generating ZPL (Zebra Programming Language) print commands for each label.
- Sending the commands to Zebra label printers via CUPS.

The application runs on a Linux server with PHP installed. It is still work in progress and therefore has not yet been tested properly.

<strong>Preparations:</strong>
- Go to https://developers.exlibrisgroup.com, log in to your institution's developer account and create applications both for the Analytics and for the Bibs API. You will need the applications' API keys when customizing the alma-spine-label-printing/config.ini.
- Log in to Alma and open Alma Analytics.
- Create an analysis and select "Physical items" as subject area.
- Under "Criteria", select at least the "Barcode" and the "InventoryDate" column (both from "Physical Item Details").
- Choose a name for the analysis and save it in "Shared Folders/YOUR_INSTITUTION/reports".
- Copy the "path" parameter from the analysis' url (for example "%2Fshared%2FYOUR_INSTITUTION%2FReports%2FInventoryPerDay"). You'll need it when configuring the alma-spine-label-printing application.

<strong>Installation:</strong>
- Install CUPS on your Linux server.
- Copy or clone the alma-spine-label-printing directory to your server's /var directory.
- Edit your httpd.conf to make the alma-spine-label-printing/www directory accessible via www.
- Customize the config.ini file.

<strong>Usage:</strong>
- Define your own spine labels by editing classes/class.BookLabel.php. 
- Define a cronjob on your server that executes printjob.php daily.
- Open www/print_custom_label.php in browser to create custom spine labels.
- Restrict access to the www directory via .htaccess.
