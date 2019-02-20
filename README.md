# email-validator

A Simple Symfony-based command line tool to verify email addresses from CSV file.

The CSV file needs to be put in __csv__ folder. Then you can run the validator by running command: 

**bin/console validate:email {file_name.csv}**

e.g.:

bin/console validate:email data.csv

The results will be saved in __csv/validator_result__ folder.It will contain 3 files:

correct_emails.csv

wrong_emails.csv

report.txt
