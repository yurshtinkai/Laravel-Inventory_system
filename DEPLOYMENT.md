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
   
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:YOUR_APP_KEY_HERE
   APP_URL=https://your-service-name.onrender.com
   
   DB_CONNECTION=mysql
   DB_HOST=your-db-host
   DB_PORT=3306
   DB_DATABASE=your-db-name
   DB_USERNAME=your-db-username
   DB_PASSWORD=your-db-password
   
   LOG_CHANNEL=stack
   ```

5. **Generate APP_KEY**
   - Run this locally: `php artisan key:generate --show`
   - Copy the generated key and paste it as the `APP_KEY` value
   - Or connect to your Render service via SSH and run: `php artisan key:generate`

6. **Deploy**
   - Click "Create Web Service"
   - Render will build and deploy your application
   - This may take 5-10 minutes on first deployment

### Option 2: Using render.yaml (Infrastructure as Code)

1. **Update render.yaml**
   - If you created a database manually, update the database connection details
   - Or let Render create the database from the render.yaml file

2. **Deploy**
   - Connect your repository to Render
   - Render will automatically detect the `render.yaml` file
   - It will create all services defined in the file

## Post-Deployment Steps

1. **Run Migrations**
   - Go to your service in Render dashboard
   - Open the "Shell" tab
   - Run: `php artisan migrate --force`

2. **Seed Database (Optional)**
   - In the shell: `php artisan db:seed`

3. **Clear Cache**
   - In the shell: `php artisan config:clear`
   - In the shell: `php artisan cache:clear`
   - In the shell: `php artisan view:clear`

## Important Notes

- **Storage**: Render's file system is ephemeral. For file uploads, use S3 or similar cloud storage.
- **Queue Workers**: If you use queues, create a separate Background Worker service.
- **Scheduled Tasks**: Use Render's Cron Jobs feature for Laravel's task scheduler.
- **Database Migrations**: You can enable automatic migrations by uncommenting the line in the Dockerfile's startup script.

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

Required:
- `APP_KEY`: Application encryption key
- `APP_URL`: Your application URL
- `DB_*`: Database connection details

Optional but Recommended:
- `APP_ENV`: Set to `production`
- `APP_DEBUG`: Set to `false` in production
- `LOG_CHANNEL`: Set to `stack` or `stderr`

## Support

For Render-specific issues, check Render's documentation: https://render.com/docs
For Laravel deployment issues, check Laravel's deployment guide: https://laravel.com/docs/deployment

