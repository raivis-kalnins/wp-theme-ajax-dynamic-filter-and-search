# WP Theme AJAX Dynamic Filter and Search

A WordPress theme/solution providing:

✔ Dynamic AJAX filters for CPT + ACF + Taxonomies  
✔ AJAX live search across WordPress and WooCommerce  
✔ Filter configuration via ACF Options Page  
✔ Shortcode to embed dynamic archive pages  
✔ Price range sliders, multi‑select filters, and URL sync

---

## 🗂 Project Structure


wp-theme-ajax-dynamic-filter-and-search/
│
├─ acf-dynamic-filters.json # ACF field group for filter config
├─ README.md # This documentation
├─ assets/
│ ├─ css/
│ │ ├─ dynamic-filters.css # Style for AJAX filters
│ │ ├─ ajax-search.css # Style for live search dropdown
│ └─ js/
│ ├─ dynamic-filters.js # AJAX filter logic
│ └─ ajax-search.js # AJAX live search logic
│
├─ inc/ # (Optional) reusable PHP includes
│ └─ search-handler.php # AJAX search functions
│
├─ patterns/ # Block patterns (if any)
│
├─ src/ # JS/CSS build sources (optional)
│
├─ archive-dynamic.php # Main dynamic archive template
└─ functions.php # Enqueue assets + AJAX handlers


---

## 🧩 Installation

1. Clone into your theme directory:

/wp-content/themes/your-theme/wp-theme-ajax-dynamic-filter-and-search


2. Activate the theme in Appearance → Themes.

3. Install & activate **Advanced Custom Fields Pro** (for repeater filters).

4. Import filter settings JSON:
   - Go to **Custom Fields → Tools → Import**
   - Upload `acf-dynamic-filters.json`

---

## 🔧 ACF Filter Configuration

After import, go to:

Custom Fields → Filter Settings (under Dynamic Filters)


Add rows in repeater:

| Filter Type | Field/Taxonomy | Label | Input Type | Options |
|-------------|----------------|-------|------------|---------|
| acf_field   | color          | Color | select     | Red,Blue,Green |
| acf_field   | size           | Size  | checkbox   | S,M,L,XL       |
| acf_field   | price          | Price | range      | *(unused)*     |
| taxonomy    | product_category | Category | checkbox | *(automatic)* |

> **field_name** must match ACF field key or taxonomy slug.

---

## 📌 Usage: Dynamic Archive

Place the shortcode anywhere:

[dynamic_archive]


This will render an archive with:

✔ Filters from ACF  
✔ Dual‑handle price range  
✔ AJAX Load More  
✔ URL sync for deep linking

---

## 🔎 AJAX Search Integration

Place the search form (e.g., in `header.php`):

<form id="ajax-search-form"> <input type="text" id="ajax-search-input" placeholder="Search..."> <div id="ajax-search-results"></div> </form> ```

The live search script
(assets/js/ajax-search.js)
will display live results with images and a “See all results” link if more than 10 results match.

Search includes:

WordPress posts
Pages
Any CPT
WooCommerce products (by title, SKU, or ID)

And localizes AJAX URL:

wp_localize_script('dynamic-filters','ajaxfilters',[
    'ajaxurl' => admin_url('admin-ajax.php')
]);

Dynamic Filters

✔ Multi‑select, select, text, range
✔ Taxonomy and ACF based
✔ Price sliders using noUiSlider
✔ Filter UI fully AJAX
✔ Load More and pagination
✔ URL state syncing

AJAX Search

✔ Live dropdown
✔ Search entire site + CPTs
✔ WooCommerce product title/SKU search
✔ Results with thumbnails
✔ “See all results” link