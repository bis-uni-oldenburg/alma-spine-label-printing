# alma-spine-label-printing
<strong>Printing spine labels for ExLibris Alma bib items</strong>

<strong>Purpose:</strong>
- Automatically print spine labels for currently inventoried items.
- Print custom spine labels via web frontend.

<strong>The application consists of the following components:</strong>
- A (potentially cronjob-triggered) daily request for newly inventoried items via the Analytics API (returning the item barcodes).
- A web frontend to define and print custom spine labels.
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
- Restrict access to the www directory via .htaccess.
- Grant the appropiate permissions for log and labels_zpl directory.

<strong>Usage:</strong>
- Define your own spine labels by editing classes/class.BookLabel.php. 
- Define a cronjob on your server that executes job/printjob.php daily.
- Web frontend: Open www/print_custom_label.php in browser to print custom spine labels. Spine labels can be previewed making use of the Labelary ZPL web service (http://api.labelary.com)

ZPL web service call example: 
http://api.labelary.com/v1/printers/8dpmm/labels/4.6x1.4/0/%5Exa%5ELL280%5ECI28%5EAD%5EFO70,32%5EFDTranskulturalit%C3%A4t%20und%5EFS%5EAD%5EFO70,54%5EFDMusikvermittlung%5EFS%5EAG%5EFO650,22%5EFDmus%5EFS%5EAD,36,20%5EFO650,94%5EFD104.1%5EFS%5EAD,36,20%5EFO650,130%5EFD%5EFS%5EAD,36,20%5EFO650,178%5EFDCX%208511%5EFS%5EAD,36,20%5EFO650,222%5EFDb%5EFS%5EFO70,80%5EBY2%5EB3,,116,N%5EFD2424591X%5EFS%5EAE%5EFO70,204%5EFD*2424591X*%5EFS%5EAB%5EFO282,212%5EFDUB%20Oldenburg%5EFS%5EXZ

Online ZPL viewer: http://labelary.com/viewer.html
