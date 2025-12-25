// Configuration file to switch between Supabase and PHP backends

// Set to true to use PHP backend, false to use Supabase
window.usePhpBackend = false;

// Supabase configuration (only used when usePhpBackend is false)
window.supabaseConfig = {
    url: 'YOUR_SUPABASE_URL',
    key: 'YOUR_SUPABASE_ANON_KEY'
};