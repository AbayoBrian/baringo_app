# IMS Baringo CIDU - Vercel Deployment Guide

## Prerequisites

1. **GitHub Account**: Your code should be in a GitHub repository
2. **Vercel Account**: Sign up at [vercel.com](https://vercel.com)
3. **Database**: You'll need a PostgreSQL database (recommended: Neon, Supabase, or PlanetScale)

## Step 1: Prepare Your Repository

### 1.1 Push to GitHub
```bash
git add .
git commit -m "Prepare for Vercel deployment"
git push origin main
```

### 1.2 Verify Required Files
Make sure these files exist in your repository:
- `api/index.php` ✅
- `vercel.json` ✅
- `.vercelignore` ✅
- `package.json` ✅
- `config/database_postgresql.php` ✅

## Step 2: Set Up Database

### Option A: Neon (Recommended - Free PostgreSQL)
1. Go to [neon.tech](https://neon.tech)
2. Create a new project
3. Copy the connection string
4. Note down: Host, Database, Username, Password, Port

### Option B: Supabase
1. Go to [supabase.com](https://supabase.com)
2. Create a new project
3. Go to Settings > Database
4. Copy the connection details

### Option C: PlanetScale
1. Go to [planetscale.com](https://planetscale.com)
2. Create a new database
3. Get connection details

## Step 3: Deploy to Vercel

### 3.1 Connect Repository
1. Go to [vercel.com/dashboard](https://vercel.com/dashboard)
2. Click "Add New" → "Project"
3. Import your GitHub repository
4. Choose "Other" as the framework preset

### 3.2 Configure Environment Variables
In Vercel dashboard, go to Settings → Environment Variables and add:

```
APP_KEY=your-generated-app-key
APP_URL=https://your-app-name.vercel.app
DB_HOST=your-database-host
DB_NAME=your-database-name
DB_USER=your-database-username
DB_PASS=your-database-password
DB_PORT=5432
DB_TYPE=postgresql
AGENT_USERNAME=Agent
AGENT_PASSWORD=agent@2025!
ADMIN_USERNAME=CiduAdmin
ADMIN_PASSWORD=admin@2025#
```

### 3.3 Generate APP_KEY
Run this command locally to generate an APP_KEY:
```bash
php -r "echo 'base64:' . base64_encode(random_bytes(32)) . PHP_EOL;"
```

### 3.4 Deploy
1. Click "Deploy" in Vercel
2. Wait for deployment to complete
3. Your app will be available at `https://your-app-name.vercel.app`

## Step 4: Set Up Database Schema

### 4.1 Connect to Your Database
Use your database's web interface or a tool like pgAdmin to run the SQL schema.

### 4.2 Import Schema
Run the contents of `database_postgresql.sql` in your database to create all tables.

### 4.3 Insert Sample Data
```sql
-- Insert default users
INSERT INTO users (username, password, role) VALUES 
('Agent', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agent'),
('CiduAdmin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert sample subcounties
INSERT INTO subcounties (subcounty_name) VALUES 
('Baringo Central'),
('Baringo North'),
('Baringo South'),
('Eldama Ravine'),
('Mogotio'),
('Tiaty');
```

## Step 5: Test Your Deployment

### 5.1 Basic Functionality Test
1. Visit your Vercel URL
2. You should be redirected to the login page
3. Try logging in with:
   - **Agent**: Username: `Agent`, Password: `agent@2025!`
   - **Admin**: Username: `CiduAdmin`, Password: `admin@2025#`

### 5.2 Feature Testing Checklist
- [ ] Login page loads correctly
- [ ] Agent login works
- [ ] Admin login works
- [ ] Dashboard displays after login
- [ ] File upload functionality works
- [ ] Database operations work
- [ ] All pages are accessible

### 5.3 Performance Testing
- [ ] Page load times are acceptable
- [ ] Images load correctly
- [ ] CSS/JS assets load properly
- [ ] Mobile responsiveness works

## Step 6: Custom Domain (Optional)

1. Go to Vercel Dashboard → Your Project → Settings → Domains
2. Add your custom domain
3. Update DNS records as instructed
4. Update `APP_URL` environment variable

## Troubleshooting

### Common Issues

#### 1. Database Connection Error
- Verify all database environment variables are correct
- Check if your database allows connections from Vercel's IP ranges
- Ensure the database is running and accessible

#### 2. 500 Internal Server Error
- Check Vercel function logs in the dashboard
- Verify all required environment variables are set
- Check if all PHP files are properly included

#### 3. File Upload Issues
- Vercel has limitations on file uploads in serverless functions
- Consider using external storage like AWS S3 or Cloudinary
- Update file upload paths to use external storage

#### 4. Session Issues
- Sessions might not persist properly in serverless environment
- Consider using database sessions or external session storage

### Debugging Steps

1. **Check Vercel Logs**:
   - Go to Vercel Dashboard → Functions → View Function Logs

2. **Test Database Connection**:
   - Add a simple database test endpoint
   - Check if the connection works

3. **Verify Environment Variables**:
   - Make sure all required variables are set
   - Check for typos in variable names

## Production Considerations

### Security
- Change default passwords
- Use strong, unique passwords
- Enable HTTPS (automatic with Vercel)
- Regularly update dependencies

### Performance
- Optimize images
- Use CDN for static assets
- Consider caching strategies
- Monitor function execution times

### Monitoring
- Set up error tracking (Sentry, Bugsnag)
- Monitor database performance
- Track user analytics
- Set up uptime monitoring

## Support

If you encounter issues:
1. Check Vercel's documentation
2. Review the function logs
3. Test locally first
4. Check database connectivity
5. Verify all environment variables

## Next Steps

After successful deployment:
1. Set up monitoring and analytics
2. Configure backup strategies
3. Plan for scaling
4. Set up CI/CD for automatic deployments
5. Consider implementing additional security measures

---

**Happy Deploying! 🚀**
