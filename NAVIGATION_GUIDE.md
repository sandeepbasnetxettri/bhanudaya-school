# Navigation Guide for Excellence School Website

This guide explains how to navigate through all menu items on the Excellence School website using CSS and JavaScript enhancements.

## Menu Structure

The website navigation consists of the following main menu items:

1. **Home** - Main homepage
2. **About** (Dropdown)
   - History
   - Vision & Mission
   - Principal's Message
   - Faculty
   - Management
3. **Academics** (Dropdown)
   - Courses Offered
   - +2 Computer Science
   - +2 Hotel Management
   - Class Timetable
   - Academic Calendar
4. **Admissions**
5. **Notice Board**
6. **Gallery**
7. **Contact**

## CSS Enhancements

The following CSS files enhance the navigation experience:

1. **navigation.css** - Contains enhanced styles for:
   - Keyboard focus indicators
   - Active state highlighting
   - Improved dropdown menus
   - Mobile navigation improvements
   - Smooth transitions and animations
   - Accessibility features

2. **Existing styles** - The original style.css file maintains the base navigation styles.

## JavaScript Enhancements

The following JavaScript files enhance the navigation experience:

1. **navigation.js** - Contains enhanced functionality for:
   - Keyboard navigation (arrow keys, escape)
   - Focus management for accessibility
   - Dropdown behavior enhancement
   - Active state tracking
   - Smooth scrolling for anchor links

2. **main.js** - Contains the original navigation functionality including:
   - Mobile menu toggle
   - Dropdown menu for mobile
   - Form validation helpers

## Keyboard Navigation

Users can navigate through menu items using keyboard shortcuts:

- **Arrow Right** - Move to next menu item
- **Arrow Left** - Move to previous menu item
- **Arrow Down** - Open dropdown menu (on dropdown items)
- **Escape** - Close open dropdown menus
- **Enter/Space** - Activate menu item or dropdown

## Mobile Navigation

On mobile devices:
- The hamburger menu icon toggles the main navigation
- Dropdown menus expand/collapse when tapped
- Touch-friendly targets for easy navigation

## Accessibility Features

The enhanced navigation includes:
- Visual focus indicators for keyboard users
- Proper ARIA attributes for screen readers
- Semantic HTML structure
- Keyboard operable menus
- Skip to content links

## Implementation Files

1. **CSS Files**:
   - `css/navigation.css` - New enhanced navigation styles
   - `css/style.css` - Original styles (unchanged)

2. **JavaScript Files**:
   - `js/navigation.js` - New enhanced navigation functionality
   - `js/main.js` - Original navigation functionality (unchanged)

3. **HTML Updates**:
   - `index.html` - Updated to include new CSS and JS files
   - `pages/*.html` - Updated to include new CSS and JS files

## How to Navigate

### Using Mouse:
1. Click on any main menu item to navigate to that page
2. Hover over dropdown menu items (About, Academics) to reveal sub-menu
3. Click on sub-menu items to navigate to specific pages

### Using Keyboard:
1. Tab to focus on navigation items
2. Use arrow keys to move between menu items
3. Press Enter or Space to activate a menu item
4. Use Escape to close dropdown menus

### On Mobile:
1. Tap the hamburger menu icon to open/close main navigation
2. Tap on dropdown menu items to expand/collapse sub-menus
3. Tap on any menu item to navigate to that page

## Customization

To customize the navigation:
1. Modify `css/navigation.css` to change visual styles
2. Modify `js/navigation.js` to change functionality
3. Update HTML files to add/remove menu items

## Troubleshooting

If navigation isn't working properly:
1. Ensure all CSS and JS files are properly linked
2. Check browser console for JavaScript errors
3. Verify that all page files exist in the correct locations
4. Clear browser cache and reload the page