# Images Folder Structure

## 📁 Required Images

This file lists all images referenced in the website. You can replace these with your own images.

### Logo
- `logo.png` - School logo (recommended size: 100x100px)

### Banner/Slider Images
- `banner1.jpg` - Main banner image (1200x500px)
- `banner2.jpg` - Second banner image (1200x500px)
- `banner3.jpg` - Third banner image (1200x500px)

### News/Notice Images
- `news1.jpg` - News item image (400x200px)
- `news2.jpg` - News item image (400x200px)
- `news3.jpg` - News item image (400x200px)
- `news4.jpg` - News item image (400x200px)
- `news5.jpg` - News item image (400x200px)

### Gallery Images
Create a subfolder: `gallery/`

#### Sports
- `gallery/sports1.jpg`
- `gallery/sports2.jpg`
- `gallery/sports3.jpg`

#### Labs & Facilities
- `gallery/lab1.jpg`
- `gallery/lab2.jpg`
- `gallery/kitchen1.jpg`
- `gallery/library1.jpg`

#### Events
- `gallery/science1.jpg`
- `gallery/cultural1.jpg`
- `gallery/annual1.jpg`
- `gallery/trip1.jpg`

#### Activities
- `gallery/art1.jpg`
- `gallery/dance1.jpg`
- `gallery/robotics1.jpg`
- `gallery/music1.jpg`

#### Achievements
- `gallery/award1.jpg`
- `gallery/award2.jpg`
- `gallery/trophy1.jpg`

### Faculty Images (if creating faculty page)
- `faculty/teacher1.jpg`
- `faculty/teacher2.jpg`
- `faculty/teacher3.jpg`
- etc.

## 📝 Image Guidelines

### Recommended Sizes
- **Logo**: 100x100px (square)
- **Banner**: 1200x500px (wide)
- **News**: 400x200px (2:1 ratio)
- **Gallery**: 400x300px (4:3 ratio)
- **Faculty**: 300x400px (portrait)

### Formats
- **Preferred**: JPG for photos, PNG for logos
- **Quality**: Medium to high (avoid too large files)
- **Max size**: 2MB per image

### Naming Convention
- Use lowercase
- No spaces (use hyphens)
- Descriptive names
- Example: `sports-day-2024.jpg`

## 🎨 Placeholder Images

**Current Status**: The website uses SVG placeholders (colored boxes with text) if images are not found.

**To add your images:**
1. Create `images` folder in project root
2. Create `images/gallery` subfolder
3. Add your images with matching names
4. Refresh the website

## ⚙️ Technical Notes

### Fallback System
All `<img>` tags include `onerror` attribute:
```html
onerror="this.src='data:image/svg+xml,...'"
```
This displays a colored placeholder if image is missing.

### No External Dependencies
- No image libraries required
- Works without images
- SVG placeholders ensure nothing breaks

## 🚀 Quick Setup

### Option 1: Use Placeholders (Current)
- No action needed
- Website works with SVG placeholders
- Good for testing and development

### Option 2: Add Real Images
1. Create folder structure:
```
school 2/
└── images/
    ├── logo.png
    ├── banner1.jpg
    ├── banner2.jpg
    ├── banner3.jpg
    ├── news1.jpg
    ├── news2.jpg
    ├── news3.jpg
    ├── news4.jpg
    ├── news5.jpg
    └── gallery/
        ├── sports1.jpg
        ├── lab1.jpg
        ├── science1.jpg
        └── ... (other images)
```

2. Add your images
3. Refresh browser

## 📸 Free Image Resources

If you need placeholder images:
- **Unsplash**: unsplash.com
- **Pexels**: pexels.com
- **Pixabay**: pixabay.com

Search for:
- "school students"
- "classroom"
- "sports field"
- "science lab"
- "school building"

## ✅ Checklist

Before going live:
- [ ] Add school logo
- [ ] Replace all banner images
- [ ] Add news/event images
- [ ] Upload gallery photos
- [ ] Add faculty photos (if applicable)
- [ ] Optimize image sizes
- [ ] Test on slow connections

---

**Note**: The website is fully functional without images. All placeholders are automatically generated using SVG.
