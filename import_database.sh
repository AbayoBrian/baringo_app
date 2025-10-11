#!/bin/bash

# IMS Baringo CIDU Database Import Script
# This script will import the database schema and sample data

echo "============================================="
echo "IMS Baringo CIDU Database Import Script"
echo "============================================="
echo ""

# Check if MySQL is available
if ! command -v mysql &> /dev/null; then
    echo "Error: MySQL client is not installed or not in PATH"
    echo "Please install MySQL client and try again"
    exit 1
fi

# Get database credentials
echo "Enter MySQL root password:"
read -s MYSQL_PASSWORD

# Test connection
echo "Testing MySQL connection..."
if ! mysql -u root -p$MYSQL_PASSWORD -e "SELECT 1;" &> /dev/null; then
    echo "Error: Cannot connect to MySQL server"
    echo "Please check your credentials and try again"
    exit 1
fi

echo "✓ MySQL connection successful"
echo ""

# Import the database
echo "Importing database schema and data..."
if mysql -u root -p$MYSQL_PASSWORD < database_import.sql; then
    echo "✓ Database imported successfully!"
    echo ""
    echo "Database: ims_baringo"
    echo "Tables created:"
    mysql -u root -p$MYSQL_PASSWORD -e "USE ims_baringo; SHOW TABLES;"
    echo ""
    echo "Default users created:"
    mysql -u root -p$MYSQL_PASSWORD -e "USE ims_baringo; SELECT username, role FROM users;"
    echo ""
    echo "Sample data inserted:"
    mysql -u root -p$MYSQL_PASSWORD -e "USE ims_baringo; SELECT 'Schemes:' as type, COUNT(*) as count FROM irrigation_schemes UNION SELECT 'Assessments:', COUNT(*) FROM assessments UNION SELECT 'Subcounties:', COUNT(*) FROM subcounties;"
    echo ""
    echo "============================================="
    echo "Database setup completed successfully!"
    echo "You can now run your PHP application"
    echo "============================================="
else
    echo "Error: Database import failed"
    echo "Please check the error messages above and try again"
    exit 1
fi
