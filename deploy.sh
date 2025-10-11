#!/bin/bash

echo "🚀 Deploying IMS Baringo CIDU to Vercel..."
echo "=========================================="

# Check if git is initialized
if [ ! -d ".git" ]; then
    echo "❌ Git not initialized. Please run: git init"
    exit 1
fi

# Add all files
echo "📁 Adding files to git..."
git add .

# Commit changes
echo "💾 Committing changes..."
git commit -m "Fix Vercel deployment: add public directory and update configuration"

# Push to GitHub
echo "⬆️  Pushing to GitHub..."
git push origin main

echo "✅ Deployment initiated!"
echo "🌐 Check your Vercel dashboard for deployment status"
echo "🔗 Your app will be available at: https://your-app-name.vercel.app"
echo ""
echo "📋 Next steps:"
echo "1. Wait for deployment to complete"
echo "2. Test: https://your-app-name.vercel.app/api/test"
echo "3. Test: https://your-app-name.vercel.app/"
