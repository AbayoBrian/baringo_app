# Troubleshooting 404 Error on Vercel

## Error: 404 NOT_FOUND Code: NOT_FOUND

This error typically occurs when Vercel can't find or execute the PHP function properly.

## Quick Fixes

### 1. Check Your Vercel Configuration

Make sure your `vercel.json` looks like this:

```json
{
  "version": 2,
  "framework": null,
  "functions": {
    "api/index.php": {
      "runtime": "vercel-php@0.7.1"
    },
    "api/test.php": {
      "runtime": "vercel-php@0.7.1"
    }
  },
  "routes": [
    {
      "src": "/assets/(.*)",
      "dest": "/assets/$1"
    },
    {
      "src": "/api/test",
      "dest": "/api/test.php"
    },
    {
      "src": "/(.*)",
      "dest": "/api/index.php"
    }
  ]
}
```

### 2. Test the API Endpoint

First, test if the basic API is working:
- Visit: `https://your-app.vercel.app/api/test`
- You should see a JSON response with status information

### 3. Check Function Logs

1. Go to your Vercel dashboard
2. Click on your project
3. Go to "Functions" tab
4. Click on "View Function Logs"
5. Look for any error messages

### 4. Common Issues and Solutions

#### Issue: PHP Runtime Not Found
**Solution**: Make sure you're using `vercel-php@0.7.1` in your vercel.json

#### Issue: File Not Found
**Solution**: 
- Ensure all files are committed to your repository
- Check that the file paths in vercel.json match your actual file structure
- Make sure there are no syntax errors in your PHP files

#### Issue: Environment Variables Missing
**Solution**: 
- Go to Vercel Dashboard → Settings → Environment Variables
- Add all required environment variables
- Redeploy your application

#### Issue: Database Connection Error
**Solution**:
- Verify your database credentials
- Make sure your database allows connections from Vercel
- Check if you're using the correct database type (PostgreSQL vs MySQL)

### 5. Debug Steps

#### Step 1: Test Basic Functionality
```bash
# Test the API endpoint
curl https://your-app.vercel.app/api/test
```

#### Step 2: Check File Structure
Make sure your repository has:
```
├── api/
│   ├── index.php
│   └── test.php
├── vercel.json
├── .vercelignore
└── package.json
```

#### Step 3: Verify PHP Syntax
```bash
# Check for PHP syntax errors
php -l api/index.php
php -l api/test.php
```

#### Step 4: Check Vercel Logs
1. Go to Vercel Dashboard
2. Select your project
3. Go to "Functions" → "View Function Logs"
4. Look for error messages

### 6. Environment Variables Checklist

Make sure these are set in Vercel:
```
APP_KEY=your-generated-key
APP_URL=https://your-app.vercel.app
DB_HOST=your-database-host
DB_NAME=your-database-name
DB_USER=your-database-username
DB_PASS=your-database-password
DB_PORT=5432
DB_TYPE=postgresql
```

### 7. Redeploy Steps

1. **Clear Build Cache**:
   - Go to Vercel Dashboard
   - Settings → General
   - Clear Build Cache
   - Redeploy

2. **Force Redeploy**:
   ```bash
   vercel --force
   ```

3. **Check Deployment Status**:
   - Look for any build errors
   - Check function deployment status

### 8. Alternative Debugging

If the issue persists, try this minimal test:

1. Create a simple `api/hello.php`:
```php
<?php
echo "Hello from Vercel!";
?>
```

2. Add to vercel.json:
```json
{
  "src": "/hello",
  "dest": "/api/hello.php"
}
```

3. Test: `https://your-app.vercel.app/hello`

### 9. Contact Support

If none of the above works:
1. Check Vercel's status page
2. Review Vercel's PHP documentation
3. Contact Vercel support with your error ID

## Prevention

To avoid 404 errors in the future:
1. Always test locally first
2. Use the test endpoint to verify deployment
3. Check logs after each deployment
4. Keep your vercel.json configuration simple
5. Test with minimal code first, then add complexity

---

**Remember**: The 404 error usually means Vercel can't find or execute your function. Start with the test endpoint to verify basic functionality.
