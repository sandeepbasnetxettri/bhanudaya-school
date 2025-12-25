# Backend Options

This project supports two backend implementations:

1. **JavaScript/Supabase** (Default) - Modern, serverless approach
2. **PHP/MySQL** - Traditional server-based approach

## Option 1: JavaScript/Supabase (Default)

This is the current implementation that uses:
- Supabase for authentication and database
- Client-side JavaScript for all functionality
- No server required

### Setup
1. The system is ready to use out of the box
2. To connect to a real Supabase instance:
   - Edit `js/config.js` and set `window.usePhpBackend = false`
   - Update `window.supabaseConfig` with your Supabase credentials
   - Deploy the database schema from `DATABASE_SCHEMA.md` to your Supabase project

### Advantages
- No server setup required
- Fast deployment to static hosting (GitHub Pages, Netlify, Vercel)
- Real-time capabilities
- Built-in authentication
- Automatic scaling

## Option 2: PHP/MySQL

This implementation uses:
- PHP for server-side logic
- MySQL for database storage
- REST API endpoints for frontend communication

### Setup
1. Set up a PHP-enabled web server (Apache/Nginx with PHP)
2. Create a MySQL database
3. Import the schema from `php/database/schema.sql`
4. Update database credentials in `php/config/database.php`
5. Edit `js/config.js` and set `window.usePhpBackend = true`

### File Structure
```
php/
├── api/
│   ├── register.php     # User registration endpoint
│   └── login.php        # User login endpoint
├── config/
│   └── database.php     # Database configuration
└── database/
    └── schema.sql       # Database schema
```

### API Endpoints
- **POST** `/php/api/register.php` - User registration
- **POST** `/php/api/login.php` - User login

Both endpoints expect JSON data and return JSON responses.

### Advantages
- Works with traditional hosting providers
- More control over data and business logic
- Familiar technology stack for many developers
- Can be extended with additional server-side features

## Switching Between Implementations

To switch between implementations:

1. Open `js/config.js`
2. Set `window.usePhpBackend` to:
   - `false` for Supabase (default)
   - `true` for PHP/MySQL

## Database Schema

Both implementations use the same database structure:
- Users table for authentication
- User Profiles table for signup information
- All required tables for school management (Students, Teachers, Classes, etc.)

The only difference is the database technology (Supabase PostgreSQL vs MySQL) and the way data is accessed (Supabase client vs PHP PDO).

## Deployment

### For JavaScript/Supabase
- Deploy to any static hosting service
- Configure Supabase project
- Update configuration with your credentials

### For PHP/MySQL
- Upload files to PHP-enabled web server
- Create MySQL database
- Import schema
- Update database configuration
- Configure web server if needed

Both implementations provide the same user experience and functionality.