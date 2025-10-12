#!/bin/bash

# CRM Workflow Validation Script
# This script will help you systematically test your CRM system

echo "🚀 Starting CRM Workflow Validation..."
echo "======================================"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    local status=$1
    local message=$2
    
    case $status in
        "SUCCESS")
            echo -e "${GREEN}✅ $message${NC}"
            ;;
        "ERROR")
            echo -e "${RED}❌ $message${NC}"
            ;;
        "WARNING")
            echo -e "${YELLOW}⚠️ $message${NC}"
            ;;
        "INFO")
            echo -e "${BLUE}ℹ️ $message${NC}"
            ;;
    esac
}

# Function to run a command and check its status
run_check() {
    local command=$1
    local description=$2
    
    echo -n "Testing: $description... "
    
    if eval "$command" > /dev/null 2>&1; then
        print_status "SUCCESS" "$description"
        return 0
    else
        print_status "ERROR" "$description"
        return 1
    fi
}

# Function to check if a URL is accessible
check_url() {
    local url=$1
    local description=$2
    
    echo -n "Testing: $description... "
    
    if curl -s -o /dev/null -w "%{http_code}" "$url" | grep -q "200"; then
        print_status "SUCCESS" "$description"
        return 0
    else
        print_status "ERROR" "$description"
        return 1
    fi
}

echo ""
print_status "INFO" "Phase 1: System Health Check"
echo "----------------------------------------"

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    print_status "ERROR" "Not in Laravel project directory. Please run this script from your project root."
    exit 1
fi

# Check PHP version
run_check "php --version" "PHP is installed"

# Check Composer
run_check "composer --version" "Composer is installed"

# Check Node.js and NPM
run_check "node --version" "Node.js is installed"
run_check "npm --version" "NPM is installed"

# Check Laravel Artisan
run_check "php artisan --version" "Laravel Artisan is working"

echo ""
print_status "INFO" "Phase 2: Database & Configuration"
echo "----------------------------------------"

# Check database connection
run_check "php artisan migrate:status" "Database connection"

# Check configuration
run_check "php artisan config:cache" "Configuration caching"

# Check routes
run_check "php artisan route:list" "Route registration"

echo ""
print_status "INFO" "Phase 3: Application Components"
echo "----------------------------------------"

# Check if key models exist
run_check "php artisan tinker --execute='App\Models\User::count()'" "User model"

run_check "php artisan tinker --execute='App\Models\ProductionOrder::count()'" "ProductionOrder model"

run_check "php artisan tinker --execute='App\Models\Customer::count()'" "Customer model"

# Check notification service
run_check "php artisan tinker --execute='app(App\Services\NotificationService::class)'" "NotificationService"

echo ""
print_status "INFO" "Phase 4: Development Server Test"
echo "----------------------------------------"

# Start the development server in background
print_status "INFO" "Starting development server..."
php artisan serve --host=127.0.0.1 --port=8000 &
SERVER_PID=$!

# Wait for server to start
sleep 3

# Test if server is running
if kill -0 $SERVER_PID 2>/dev/null; then
    print_status "SUCCESS" "Development server started (PID: $SERVER_PID)"
    
    # Test basic routes
    check_url "http://127.0.0.1:8000" "Home page"
    check_url "http://127.0.0.1:8000/login" "Login page"
    
    # Stop the server
    kill $SERVER_PID
    print_status "INFO" "Development server stopped"
else
    print_status "ERROR" "Failed to start development server"
fi

echo ""
print_status "INFO" "Phase 5: Workflow Validation"
echo "----------------------------------------"

# Run the custom validation command
if php artisan workflow:validate --quick; then
    print_status "SUCCESS" "Workflow validation completed"
else
    print_status "ERROR" "Workflow validation failed"
fi

echo ""
print_status "INFO" "Phase 6: Queue System Test"
echo "----------------------------------------"

# Test queue system
run_check "php artisan queue:work --once" "Queue processing"

echo ""
print_status "INFO" "Phase 7: Asset Compilation"
echo "----------------------------------------"

# Check if assets can be compiled
run_check "npm run build" "Asset compilation"

echo ""
print_status "INFO" "Validation Summary"
echo "=================="

echo ""
print_status "INFO" "Next Steps:"
echo "1. Review any errors above"
echo "2. Run 'php artisan workflow:validate' for detailed validation"
echo "3. Test the application manually at http://localhost:8000"
echo "4. Check the workflow validation guide: workflow/WORKFLOW_VALIDATION_GUIDE.md"

echo ""
print_status "INFO" "If you encounter issues:"
echo "1. Check Laravel logs: storage/logs/laravel.log"
echo "2. Run 'php artisan config:clear' and 'php artisan cache:clear'"
echo "3. Verify your .env file configuration"
echo "4. Run 'php artisan db:seed' to ensure test data exists"

echo ""
print_status "SUCCESS" "Validation script completed!"
echo "======================================"
