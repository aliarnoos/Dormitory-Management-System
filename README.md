# **Dormitory Management System**

The **Dormitory Management System (DMS)** is a web-based application designed to streamline the management of university dormitories. The system enables administrators to efficiently handle room reservations, manage inspections, and track student deposits, all while maintaining user-specific functionalities tailored to roles such as students, finance officers, and facility management.

## **Features**
- **Role-Based Access**: Tailored functionalities for students, finance officers, and FMD staff.
- **Room Reservations**: Students can reserve rooms based on gender, apartment type, and availability.
- **Apartment Management**: Admins can add apartments and automatically generate rooms based on apartment type.
- **Inspection Tracking**: Facility managers can schedule and monitor room inspections.
- **Deposit Management**: Tracks deposits for students, including amounts, statuses, and timestamps.
- **Reports and Notifications**: Notifications for successful reservations, deposits, and system actions.

## **Tech Stack**
- **Backend**: Laravel 11  
- **Frontend**: Filament Admin Panel  
- **Database**: MySQL  
- **Authentication**: Laravel Sanctum
- **Deployment**: Cleavr.io & DigitalOcean 

## **Contributors**
- **Ali Muhsin**: Software Engineer
- **Mohammed Salih**: Software Engineer 

## **Installation and Setup**
```bash

# Install Dependencies
composer install
npm install

# Configure the Environment
cp .env.example .env
# Update .env with database credentials and Google OAuth keys
# Example:
# DB_DATABASE=dms
# DB_USERNAME=root
# DB_PASSWORD=yourpassword
# GOOGLE_CLIENT_ID=your_google_client_id
# GOOGLE_CLIENT_SECRET=your_google_client_secret
# GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# Generate App Key
php artisan key:generate

# Run Migrations and Seed the Database
php artisan migrate --seed

# Start the Development Server
php artisan serve

# Build Frontend Assets
npm run dev

# Navigate To
http://localhost:8000