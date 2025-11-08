# Deploying Laravel Inventory System to Render

## Overview
Since Render doesn't show PHP directly in the language dropdown, we're using Docker to deploy your Laravel application.

## Prerequisites
1. A Render account (free tier is fine)
2. Your code pushed to a Git repository (GitHub, GitLab, or Bitbucket)

## Deployment Steps

### Option 1: Using Render Dashboard (Recommended)

1. **Connect Your Repository**
   - Go to your Render dashboard
   - Click "New +" → "Web Service"
   - Connect your Git repository
   - Select your `Laravel-Inventory_system` repository

2. **Configure the Service**
   - **Environment**: Select **Docker** (not PHP - this is why you couldn't find it!)
   - **Name**: `laravel-inventory-system` (or any name you prefer)
   - **Region**: Choose your preferred region (Singapore is available)
   - **Branch**: `main` or `master` (your default branch)
   - **Root Directory**: Leave empty (or set to `Laravel-Inventory_system` if your repo has this folder)
   - **Dockerfile Path**: `Dockerfile` (or `Laravel-Inventory_system/Dockerfile` if needed)
   - **Docker Context**: `.` (or `Laravel-Inventory_system` if needed)

3. **Create Database**
   - In Render dashboard, click "New +" → "PostgreSQL" (or MySQL if available)
   - Name it: `laravel-inventory-db`
   - Note the connection details (host, database, username, password)

4. **Configure Environment Variables**
   In your web service settings, add these environment variables:
   
   **Before First Deployment (you can add APP_URL later):**
   
   **For MySQL Database:**
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:YOUR_APP_KEY_HERE
   (APP_URL - add this after deployment, see step 7)
   
   DB_CONNECTION=mysql
   DB_HOST=your-db-host
   DB_PORT=3306
   DB_DATABASE=your-db-name
   DB_USERNAME=your-db-username
   DB_PASSWORD=your-db-password
   
   LOG_CHANNEL=stack
   ```
   
   **For PostgreSQL Database (Render's default free tier):**
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:YOUR_APP_KEY_HERE
   (APP_URL - add this after deployment, see step 7)
   
   DB_CONNECTION=pgsql
   DB_HOST=your-db-host
   DB_PORT=5432
   DB_DATABASE=your-db-name
   DB_USERNAME=your-db-username
   DB_PASSWORD=your-db-password
   
   LOG_CHANNEL=stack
   ```
   
   **📍 Where to Find Database Connection Details:**
   - Go to your database service in Render dashboard
   - Click on your database (e.g., "laravel-inventory-db")
   - Look for "Connection Information" or "Internal Database URL" section
   - You'll find: Host, Port, Database Name, Username, Password
   - **Important:** Use the **Internal Database URL** or connection details shown in Render
   - **Note:** Render provides the port in the connection details - use that port number
   
   **💡 About DB_PORT:**
   - **Yes, DB_PORT should be included** in your environment variables
   - **MySQL default port:** `3306`
   - **PostgreSQL default port:** `5432`
   - Render will show you the exact port in the database connection details
   - While Laravel has defaults, it's **recommended to include DB_PORT explicitly** for clarity
   - If you omit DB_PORT, Laravel will use defaults (3306 for MySQL, 5432 for PostgreSQL)
   
   **Note:** You'll add `APP_URL` after the first deployment once you know your app's URL.

5. **Generate APP_KEY**
   - Run this locally: `php artisan key:generate --show`
   - Copy the generated key and paste it as the `APP_KEY` value
   - Or connect to your Render service via SSH and run: `php artisan key:generate`

6. **Deploy**
   - Click "Create Web Service"
   - Render will build and deploy your application
   - This may take 5-10 minutes on first deployment

7. **Find Your App URL and Set APP_URL**
   
   **📍 Where to Find Your URL:**
   - After deployment completes, go to your service page in Render dashboard
   - The URL is displayed at the **top of the page** in a banner/header
   - It looks like: `https://laravel-inventory-system-xxxx.onrender.com`
   - **📖 Detailed guide:** See `HOW_TO_FIND_APP_URL.md` for step-by-step instructions with screenshots locations
   
   **🔧 How to Set APP_URL:**
   - Copy the URL from the top of your service page
   - Go to your service → **Environment** tab (left sidebar)
   - Click **"Add Environment Variable"**
   - **Key**: `APP_URL`
   - **Value**: Paste your URL (e.g., `https://laravel-inventory-system-xxxx.onrender.com`)
   - Click **"Save Changes"**
   - Render will automatically redeploy (this is normal and takes a few minutes)
   
   **💡 Tip:** You can deploy without APP_URL first, then add it after you see your URL. Your app will work, but some Laravel features (like URL generation) work better with APP_URL set.
   
   **Alternative:** If using `render.yaml`, `APP_URL` has `sync: true` which may auto-set it, but you should still verify it's correct in the Environment tab.

### Option 2: Using render.yaml (Infrastructure as Code)

1. **Update render.yaml**
   - If you created a database manually, update the database connection details
   - Or let Render create the database from the render.yaml file

2. **Deploy**
   - Connect your repository to Render
   - Render will automatically detect the `render.yaml` file
   - It will create all services defined in the file

## Post-Deployment Steps

### Automatic Migrations ✅
**Good news!** Migrations now run automatically when your container starts. The startup script will:
- Wait for the database to be ready
- Automatically run `php artisan migrate --force`
- Create all database tables
- Cache configuration for better performance

You can verify migrations ran successfully by checking the logs in Render dashboard.

### Optional: Seed Database
If you want to seed your database with initial data:
- Go to your service in Render dashboard
- Open the "Shell" tab
- Run: `php artisan db:seed`

### Manual Migration (if needed)
If you need to run migrations manually for any reason:
- Go to your service in Render dashboard
- Open the "Shell" tab
- Run: `php artisan migrate --force`

## Important Notes

- **Storage**: Render's file system is ephemeral. For file uploads, use S3 or similar cloud storage.
- **Queue Workers**: If you use queues, create a separate Background Worker service.
- **Scheduled Tasks**: Use Render's Cron Jobs feature for Laravel's task scheduler.
- **Database Migrations**: Migrations run automatically on every container start. The script waits for the database to be ready before running migrations.
- **Migration Safety**: Migrations are run with `--force` flag in production mode. Make sure your migrations are safe to run multiple times (idempotent).

## Troubleshooting

### Build Fails
- Check the build logs in Render dashboard
- Ensure all dependencies are in `composer.json` and `package.json`
- Verify the Dockerfile path is correct

### Application Doesn't Start
- Check the logs tab in Render dashboard
- Verify all environment variables are set correctly
- Ensure APP_KEY is generated

### Database Connection Issues
- Verify database credentials in environment variables
- Check that the database service is running
- Ensure the database host allows connections from your web service

## Environment Variables Reference

### Required Variables:
- `APP_KEY`: Application encryption key (generate with `php artisan key:generate`)
- `APP_URL`: Your application URL (found after deployment in Render dashboard)
- `DB_CONNECTION`: Database type (`mysql` or `pgsql`)
- `DB_HOST`: Database host (from Render database connection details)
- `DB_PORT`: Database port (`3306` for MySQL, `5432` for PostgreSQL)
- `DB_DATABASE`: Database name (from Render database connection details)
- `DB_USERNAME`: Database username (from Render database connection details)
- `DB_PASSWORD`: Database password (from Render database connection details)

### Optional but Recommended:
- `APP_ENV`: Set to `production`
- `APP_DEBUG`: Set to `false` in production
- `LOG_CHANNEL`: Set to `stack` or `stderr`

### Database Port Details:
- **DB_PORT is included and recommended** for explicit configuration
- **MySQL:** Default port is `3306`
- **PostgreSQL:** Default port is `5432`
- **Render:** Check your database connection details for the exact port
- **Note:** If DB_PORT is omitted, Laravel will use default ports, but it's better to specify it explicitly

## Support

For Render-specific issues, check Render's documentation: https://render.com/docs
For Laravel deployment issues, check Laravel's deployment guide: https://laravel.com/docs/deployment

