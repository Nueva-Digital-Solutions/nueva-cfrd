# Nueva Custom Field Repeater Display (CFRD)

**Nueva CFRD** is a powerful Elementor extension that allows you to display Advanced Custom Fields (ACF) Repeater data with complete design freedom. Unlike standard widgets that lock you into specific layouts, CFRD gives you a "Code-First" approach: you write the HTML, CSS, and JS to loop through your data exactly how you want.

---

## 🚀 Features

*   **ACF Repeater Support**: Seamlessly pull data from ACF Repeater fields on Pages, Options Pages, or Taxonomies.
*   **Custom Loop Widget**: A single, flexible widget to render your data.
*   **Code-First Control**: Full control over the HTML structure, CSS styling, and JavaScript behavior.
*   **Dynamic Placeholders**: Use simple handlebars-style placeholders (e.g., `{{title}}`, `{{image}}`) to inject field data.
*   **External Libraries**: Easily load external CSS or JS libraries (like Swiper.js or customized libraries) directly from the widget.
*   **Frontend Performance**: Optimized rendering with no unnecessary bloat.

---

## 🛠️ Installation

1.  Upload the `nueva-cfrd` folder to your `/wp-content/plugins/` directory.
2.  Activate the plugin through the 'Plugins' menu in WordPress.
3.  Ensure you have **Elementor** and **Advanced Custom Fields** installed and active.

---

## 📖 How to Use

1.  **Open Elementor**: Edit any page with Elementor.
2.  **Add Widget**: Search for **"ACF Repeater Custom Loop"** and drag it to your page.
3.  **Select Data**:
    *   In the **Content** tab, select your **Repeater Field Name**.
    *   Choose the **Source** (Current Post, Options Page, etc.).
4.  **Write HTML**:
    *   Open the **"Custom Loop Template"** section.
    *   Enter your HTML structure for **ONE item**. The widget will automatically repeat this for every row in your ACF Repeater.
    *   Use placeholders matching your **Sub-field Names**:
        *   `{{title}}` -> Outputs the text from sub-field 'title'.
        *   `{{image}}` -> Outputs the URL of the image sub-field 'image'.
        *   `{{link}}` -> Outputs the URL from sub-field 'link'.
5.  **Style & Script**:
    *   Open the **"Custom Assets"** section.
    *   Add your **Custom CSS** to style the elements.
    *   Add **Custom JS** for interactivity (sliders, accordions, etc.).
    *   (Optional) Add URLs for external CSS/JS libraries if needed.

---

## 📋 Copy-Paste Templates

Here are some starter templates you can copy directly into the widget to get started quickly.

### 1. Card Grid
**HTML Template**:
```html
<div class="nueva-card">
    <div class="nueva-card-image">
        <img src="{{image}}" alt="{{title}}">
    </div>
    <div class="nueva-card-content">
        <h3>{{title}}</h3>
        <p>{{description}}</p>
        <a href="{{link}}" class="nueva-btn">Read More</a>
    </div>
</div>
```

**Wrapper Settings**:
*   Wrapper Tag: `div`
*   Wrapper Class: `nueva-grid-wrapper`

**Custom CSS**:
```css
.nueva-grid-wrapper {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
}
.nueva-card {
    background: #fff;
    border: 1px solid #eee;
    border-radius: 8px;
    overflow: hidden;
    transition: transform 0.2s;
}
.nueva-card:hover { transform: translateY(-5px); }
.nueva-card-image img { width: 100%; height: 200px; object-fit: cover; }
.nueva-card-content { padding: 15px; }
.nueva-btn { 
    display: inline-block; margin-top: 10px; padding: 8px 15px; 
    background: #0073e6; color: #fff; text-decoration: none; border-radius: 4px; 
}
```

---

### 2. Simple List
**HTML Template**:
```html
<div class="nueva-list-item">
    <img src="{{image}}" alt="{{title}}">
    <div class="nueva-list-text">
        <h4>{{title}}</h4>
        <div class="meta">{{date}}</div>
    </div>
</div>
```

**Custom CSS**:
```css
.nueva-list-item { 
    display: flex; align-items: center; gap: 15px; 
    padding: 10px; border-bottom: 1px solid #eee; 
}
.nueva-list-item img { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; }
.nueva-list-text h4 { margin: 0; font-size: 1.1rem; }
.meta { font-size: 0.9rem; color: #666; }
```

---

### 3. Accordion
**HTML Template**:
```html
<div class="nueva-accordion">
    <div class="accordion-header" onclick="toggleAccordion(this)">
        {{title}}
        <span class="icon">+</span>
    </div>
    <div class="accordion-body">
        <p>{{description}}</p>
    </div>
</div>
```

**Custom CSS**:
```css
.nueva-accordion { border: 1px solid #ddd; margin-bottom: 5px; border-radius: 4px; }
.accordion-header { 
    padding: 15px; background: #f9f9f9; cursor: pointer; 
    display: flex; justify-content: space-between; font-weight: bold; 
}
.accordion-body { display: none; padding: 15px; background: #fff; border-top: 1px solid #ddd; }
.accordion-header.active .icon { transform: rotate(45deg); }
```

**Custom JS**:
```javascript
function toggleAccordion(element) {
    // Close others (Optional)
    let allHeaders = document.querySelectorAll('.accordion-header');
    allHeaders.forEach(header => {
        if (header !== element) {
            header.classList.remove('active');
            header.nextElementSibling.style.display = 'none';
        }
    });

    // Toggle current
    element.classList.toggle('active');
    let body = element.nextElementSibling;
    if (body.style.display === 'block') {
        body.style.display = 'none';
    } else {
        body.style.display = 'block';
    }
}
```

---

### 4. Slider (Swiper.js)
**Prerequisite**: Add `https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css` to **External CSS URLs** and `https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js` to **External JS URLs**.

**HTML Template**:
```html
<div class="swiper-slide">
    <div class="slide-content" style="background-image: url('{{image}}');">
        <h3>{{title}}</h3>
    </div>
</div>
```

**Wrapper Settings**:
*   Wrapper Class: `swiper mySwiper`
*   Wrapper Tag: `div`
*   **Important**: You also need the `swiper-wrapper` inside. Since the widget repeats items, use this trick:

*Actually, for Swiper, the structure is specific (`swiper > swiper-wrapper > swiper-slide`).*
*With this widget:*
1. Set **Wrapper Class** to `swiper-wrapper`.
2. Wrap the widget itself in a Container/Section with class `swiper mySwiper`.
*OR, simpler Custom JS approach:*

**Custom JS**:
```javascript
// Initialize Swiper on the container
new Swiper('.mySwiper', {
    slidesPerView: 1,
    spaceBetween: 20,
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
     breakpoints: {
        768: { slidesPerView: 2 },
        1024: { slidesPerView: 3 },
    }
});
```
*(Note: Ensure your wrapper has class `swiper-wrapper` and the items match `swiper-slide`)*

---

### 5. Data Table
**HTML Template**:
```html
<tr>
    <td>{{title}}</td>
    <td>{{date}}</td>
    <td>{{status}}</td>
</tr>
```

**Wrapper Settings**:
*   Wrapper Tag: `tbody` (or `table` if you don't need a thead)
*   Wrapper Class: `nueva-table-body`

**Custom CSS**:
```css
.elementor-widget-nueva_cfrd_custom table { width: 100%; border-collapse: collapse; }
.elementor-widget-nueva_cfrd_custom td { border: 1px solid #ddd; padding: 8px; }
.elementor-widget-nueva_cfrd_custom tr:nth-child(even) { background-color: #f2f2f2; }
```

---

## Support

For issues or feature requests, please contact [Nueva Digital Solutions](https://nuevadigital.co.in).
