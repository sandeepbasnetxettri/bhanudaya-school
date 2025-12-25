# Supabase Integration Guide

This guide explains how to set up Supabase for the Excellence School Management System.

## Prerequisites

1. A Supabase account (free tier available at https://supabase.io)
2. Basic knowledge of SQL

## Setting Up Supabase

### 1. Create a New Supabase Project

1. Go to https://app.supabase.io/
2. Click "New Project"
3. Enter project details:
   - Name: Excellence School Management
   - Database Password: Set a strong password
   - Region: Select the region closest to your users
4. Click "Create new project"

### 2. Wait for Project Initialization

Supabase will take 1-2 minutes to set up your project. Once ready, you'll see the project dashboard.

### 3. Access the SQL Editor

1. In the left sidebar, click on the "SQL" icon (looks like a database)
2. You'll see the SQL editor where you can run queries

### 4. Create Database Tables

Copy the contents of `SUPABASE_SETUP.sql` and paste it into the SQL editor, then click "RUN" to create all the necessary tables.

### 5. Set Up Authentication

1. In the left sidebar, click on the "Authentication" icon
2. Go to "Settings" tab
3. Under "Enable Email Signup", make sure it's enabled
4. You can customize email templates under the "Templates" tab

### 6. Configure Row Level Security (RLS)

For production use, you should enable RLS on your tables to control data access:

```sql
-- Example: Enable RLS on users table
ALTER TABLE users ENABLE ROW LEVEL SECURITY;

-- Example: Create policy for users to only read their own data
CREATE POLICY "Users can view own profile" ON users
  FOR SELECT USING (auth.uid() = id);
```

## Connecting Your Application

### 1. Get Your Supabase Credentials

1. Go to the project settings (gear icon in the sidebar)
2. Click on "API" tab
3. Copy:
   - Project URL
   - anon key (public key)
   - service_role key (keep secret!)

### 2. Update Your JavaScript Code

In `js/supabase.js`, uncomment and update the Supabase client initialization:

```javascript
// Uncomment and update with your credentials
this.supabase = createClient(
  'YOUR_SUPABASE_URL', 
  'YOUR_SUPABASE_ANON_KEY'
);
```

### 3. Replace Mock Functions

Replace the mock functions in `supabase.js` with actual Supabase calls:

```javascript
// Actual Supabase signup
async signUp(userData) {
  const { data, error } = await this.supabase.auth.signUp({
    email: userData.email,
    password: userData.password,
    options: {
      data: {
        full_name: userData.fullName
      }
    }
  });
  
  if (!error) {
    // Store additional profile data
    await this.supabase.from('user_profiles').insert({
      user_id: data.user.id,
      date_of_birth: userData.dob,
      gender: userData.gender,
      reading_habits: userData.readingHabits || [],
      exercise_habits: userData.exerciseHabits || [],
      sleep_habits: userData.sleepHabits,
      occupation: userData.occupation,
      location: userData.location,
      address: userData.address
    });
  }
  
  return { data, error };
}

// Actual Supabase login
async signIn(email, password) {
  const { data, error } = await this.supabase.auth.signInWithPassword({
    email,
    password
  });
  return { data, error };
}

// Actual Supabase logout
async signOut() {
  const { error } = await this.supabase.auth.signOut();
  return { error };
}
```

## Testing the Integration

1. Open `pages/signup.html` in your browser
2. Fill out the multi-step form
3. Submit the form
4. Check the Supabase dashboard to verify data was stored:
   - Go to "Table Editor" to see your tables
   - Check the "users" and "user_profiles" tables for your new user

## Troubleshooting

### Common Issues

1. **CORS Errors**: Make sure your Supabase URL is correct
2. **Authentication Failures**: Verify your keys are correct
3. **Database Connection Issues**: Check your internet connection

### Getting Help

1. Supabase Documentation: https://supabase.io/docs
2. Supabase Community: https://github.com/supabase/supabase/discussions
3. Project Repository Issues

## Security Considerations

1. Never expose your service_role key in client-side code
2. Always use the anon key for client-side operations
3. Implement proper Row Level Security for production
4. Use environment variables for storing secrets
5. Regularly rotate your database passwords

## Next Steps

1. Implement real-time features using Supabase Realtime
2. Add file storage with Supabase Storage
3. Set up proper authentication flows
4. Implement Row Level Security policies
5. Add database functions for complex operations

This integration provides a solid foundation for a scalable, secure school management system with real database capabilities.