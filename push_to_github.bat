@echo off
echo Setting up Git configuration...
git config user.name "ThePhysBoy"
git config user.email "thephysboy@example.com"

echo Adding remote repository...
git remote remove origin 2>nul
git remote add origin https://github.com/ThePhysBoy/satitup.git

echo Pushing to GitHub...
git push -u origin main

echo.
echo If you see any errors, make sure:
echo 1. You are logged in to GitHub as ThePhysBoy
echo 2. The repository satitup.git exists and is public
echo 3. You have write access to the repository
echo.
pause
