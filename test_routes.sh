#!/bin/bash

# Test Routes for Inventory System
BASE_URL="http://127.0.0.1:8000"

echo "=== Testing Routes ==="
echo ""

# 1. Test Home Page
echo "1. Testing Home Page (GET /):"
curl -s -o /dev/null -w "Status: %{http_code}\n" "$BASE_URL/"
echo ""

# 2. Test Dashboard (requires auth)
echo "2. Testing Dashboard (GET /dashboard):"
curl -s -o /dev/null -w "Status: %{http_code}\n" "$BASE_URL/dashboard"
echo ""

# 3. Test Inventory List (requires auth)
echo "3. Testing Inventory Index (GET /inventory):"
curl -s -o /dev/null -w "Status: %{http_code}\n" "$BASE_URL/inventory"
echo ""

# 4. Test Login Page
echo "4. Testing Login Page (GET /login):"
curl -s -o /dev/null -w "Status: %{http_code}\n" "$BASE_URL/login"
echo ""

# 5. Test Register Page
echo "5. Testing Register Page (GET /register):"
curl -s -o /dev/null -w "Status: %{http_code}\n" "$BASE_URL/register"
echo ""

echo "=== All protected routes should return 302 (redirect to login) ==="
echo "=== Public routes should return 200 ==="
