# alma-spine-label-printing
Printing spine labels from ExLibris Alma

The application consists of the following components:
1. A (potentially cronjob-triggered) daily request for newly inventoried items via the Analytics API (returning the item barcodes).

2. A Bibs API request for each barcode returning the item details needed for the label (which are: call number, alternative call number, author, title).

3. Parsing and formatting call number, title, and author information to fit in our spine labels.

4. Generating ZPL (Zebra Programming Language) print commands for each label.

5. Sending the commands to our Zebra label printers via CUPS.

The application runs on a Linux web server using PHP. It is still work in progress and therefore has not yet been tested properly.

