## About Mailchimp sync contact in Google SpreadSheet

Build an integration using Laravel that syncs contact data from Mailchimp to a Google Sheet in two
scenarios:
#1. Real-time sync – when a new contact is created in Mailchimp.
#2. Historical sync – import all existing contacts from Mailchimp.

##Setup Instructions

#1 Download/Clone this repo
git clone https://github.com/achchhelalMaddheshiya/mailchimpsync.git
cd mailchimpsync

#2. Laravel Setup
composer install
cp .env.example .env
php artisan key:generate

#3. Storage & Database
Run composer for oackege installation
_composer install_
#Link with storage save cache and logs
_php artisan storage:link_
#Database migration like jobs, failed job etc
_php artisan migrate_

You can see the some mailchimp emaded form (You can replace it with your emaded form from welcome.blade.php file)

##Setup Google Services
Login->https://console.cloud.google.com/
You can create(No projects) => + New Project
Dashboard => https://console.cloud.google.com/home/dashboard
Goto => IAM & Admin > Service Accounts > Create Service Accounts
Api & Servive => https://console.cloud.google.com/apis/dashboard
Enable API & Services and Add Service => Google Sheets API
Assign your Google sheet to your service account => google**\***.gserviceaccount.com
#Service Accounts download json key > google-auth\*.json
Goto > Service Accounts > Action > Manage key> Add key <-- It autodown json key save in
#Add GOOGLE_SHEET_ID get id from the spreadsheet
url: https://docs.google.com/spreadsheets/d/**Sheet_ID**/edit
GOOGLE_SHEET_ID=Sheet_ID
#Add GOOGLE_SERVICE_ACCOUNT_JSON
GOOGLE_SERVICE_ACCOUNT_JSON= storage/app/google-service-account.json

##Mailchimp Setuo API Key
#Create account > Profile > Account & billing > Extra > API Key > Create API key
Placce API key in .env MAILCHIMP_API_KEY

# Add server prefix it will shown on url or End in API like 'us21' seprate from -

MAILCHIMP_SERVER_PREFIX=us17

# Add MAILCHIMP_LIST_ID

Goto > Audience > More options > Audience Setting >Audience ID
MAILCHIMP_LIST_ID=Audience ID
#Addd MAILCHIMP_WEBHOOK_SECRET for validation or You can add for specific IP
Make your custom key and then append webhooke_url?secretkey=Custom_key_for_validate

## Run command for add all contacts list in Google Sheet

_php artisan queue:work_

# It will add automaticaly your google sheet, email, first name, last name, tags

_php artisan sync:mailchimp-contacts_

#Demo Google Shee
https://docs.google.com/spreadsheets/d/14vxNf8XclW7vJNSWs9VP0ZRXErF7nArBdayh_vU_RYM/edit?usp=sharing
