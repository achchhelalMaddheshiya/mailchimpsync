## Mailchimp → Google Sheets Contact Sync (Laravel)

This Laravel application integrates Mailchimp with Google Sheets in two scenarios:

1. **Real-time sync** – Automatically adds a new contact to Google Sheets when created in Mailchimp.
2. **Historical sync** – Imports all existing contacts from Mailchimp to Google Sheets.

---

## Setup Instructions

### 1. Download / Clone the Repository

git clone https://github.com/achchhelalMaddheshiya/mailchimpsync.git
cd mailchimpsync

### 2. Laravel Setup

composer install
cp .env.example .env
php artisan key:generate

### 3. Storage & Database Setup

php artisan storage:link
php artisan migrate

> The project includes an example Mailchimp embedded form in `welcome.blade.php`. You can replace it with your own Mailchimp embed form code.

### 4. Google Services Setup

1. Login to Google Cloud Console → https://console.cloud.google.com/
2. Create a New Project (if none exists).
3. Enable Google Sheets API:
    - APIs & Services → Enable APIs & Services → Search "Google Sheets API" → Enable.
4. Create a Service Account:
    - IAM & Admin → Service Accounts → Create Service Account.
5. Generate & download the JSON Key:
    - Service Account → Actions → Manage Keys → Add Key → JSON.
    - Save it to: `storage/app/google-service-account.json`
6. Share your Google Sheet with the Service Account email (something like `xxxx@xxxx.iam.gserviceaccount.com`) → Give Editor access.
7. Get your Google Sheet ID from the URL:
    - Example: https://docs.google.com/spreadsheets/d/<GOOGLE_SHEET_ID>/edit
8. Add the following to your `.env` file:
   GOOGLE_SHEET_ID=your_google_sheet_id
   GOOGLE_SERVICE_ACCOUNT_JSON=storage/app/google-service-account.json

### 5. Mailchimp Setup

1. Get your API Key:
    - Profile → Account & Billing → Extras → API Keys → Create Key.
    - Add to `.env`:
      MAILCHIMP_API_KEY=your_api_key
2. Get your Server Prefix (found in the Mailchimp API URL, e.g., us17):
   MAILCHIMP_SERVER_PREFIX=us17
3. Get your Audience/List ID:
    - Audience → Settings → Audience ID.
    - Add to `.env`:
      MAILCHIMP_LIST_ID=your_list_id
4. Set a Webhook Secret for validation:
   MAILCHIMP_WEBHOOK_SECRET=your_custom_secret
5. Create the Webhook in Mailchimp:
    - Audience → Settings → Webhooks → Add Webhook.
    - Webhook URL:
      https://your-domain.com/mailchimp/webhook?secret=your_custom_secret
    - Select only Subscribe events.

### 6. Running the Sync

Start the Queue Worker:
php artisan queue:work

> Handles real-time sync from Mailchimp to Google Sheets.

Run Historical Sync (Import all existing contacts):
php artisan sync:mailchimp-contacts

> Adds Email, First Name, Last Name, and Tags to your Google Sheet.

---

## Demo Google Sheet

https://docs.google.com/spreadsheets/d/14vxNf8XclW7vJNSWs9VP0ZRXErF7nArBdayh_vU_RYM/edit?usp=sharing

---

## Notes

-   Real-time sync works only for new contacts added through Mailchimp forms or API.
-   Historical sync fetches all contacts from your Mailchimp audience.
-   All sync operations run asynchronously via Laravel Queues.
