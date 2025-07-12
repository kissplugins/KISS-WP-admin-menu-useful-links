# Agent & System Instructions Overview

This document provides an overview of the system instructions for our collaboration and the final code architecture of the "AVH Sticker Sizes" plugin.

## 1. System Instructions Summary

The development process followed these core user-defined instructions:

- **Verification First:** Always confirm and verify the task at hand before beginning any coding.
- **Incremental Development:** Proceed with one file at a time upon confirmation.
- **Complete File Output:** When providing code, output the entirety of only the changed file(s), line-by-line and un-abbreviated.
- **Scoped Changes:** Do not refactor code beyond the scope of the immediate task.
- **Label Consistency:** Do not change any pre-defined labels.
- **Code Maintenance:** Maintain the Table of Contents, increment the version number for each change, and add to the existing changelog within the code.
- **DRY Principles:** Adhere to "Don't Repeat Yourself" principles where possible to reuse functions.
- **Best Practices:** Follow WordPress best practices for UX/UI and security.
- **File Structure:** Initially specified as a 4-file structure, this was later modified to keep all files in the plugin's root directory.

## 2. Project Overview

The "AVH Sticker Sizes" plugin is a custom WordPress solution for creating, managing, and searching a database of automotive sticker/emblem dimensions.

- **Core Technology:** The plugin is built on the WordPress Plugin API and PHP.
- **Key Dependency:** Its data structure and admin interface are critically dependent on **Advanced Custom Fields (ACF) Pro**. The plugin programmatically registers all necessary fields and field groups.
- **Primary Features:**
    - `sticker_size` Custom Post Type (CPT).
    - Custom Taxonomies: Manufacturer, Model Year, Trim Level.
    - Custom Fields: Pre-defined placements (Front, Rear, etc.) and a repeater for unlimited custom placements.
    - Frontend Search: A `[search-stickers]` shortcode that provides a search interface and displays results in a table.

## 3. Code Architecture

The plugin's functionality is separated into four distinct files, all located in the plugin root folder.

* **`avh-sticker-sizes.php`**
    * **Role:** The main plugin file and entry point.
    * **Responsibilities:** Handles plugin initialization, header information, constants, and the crucial dependency check for ACF Pro. It registers the Custom Post Type (`sticker_size`) and the three custom taxonomies. It programmatically registers the entire ACF field group and all its fields. It also enqueues the JS/CSS assets and includes the shortcode file.

* **`shortcode-search.php`**
    * **Role:** Manages all frontend shortcode functionality.
    * **Responsibilities:** Renders the HTML for the `[search-stickers]` form. When a search is submitted, it sanitizes and parses the query string, identifying terms for the model name, manufacturer, year, and trim. It builds a complex `WP_Query` to find matching posts and formats the results into a clean, copy-paste-friendly HTML table. It also handles logic for sorting results by year when a year is not specified in the search.

* **`main.js`**
    * **Role:** Client-side user experience enhancements.
    * **Responsibilities:** Its primary function is to improve data entry in the WordPress admin. Using jQuery, it listens for input on the dimension fields and automatically converts common typed fractions (e.g., `1 3/4`) into their decimal equivalent (`1.75`) in real-time. This functionality is attached via event delegation to ensure it works on fields added dynamically by the ACF repeater.

* **`style.css`**
    * **Role:** Frontend presentation layer.
    * **Responsibilities:** Contains all CSS rules for styling the shortcode output. It styles the search form to be large and prominent, per user requirements. It also styles the results container, individual result items, and the main data table to ensure the information is readable, professional, and easy to interact with.

## 4. Key Architectural Decisions

- **Metabox Implementation:** The initial plan considered building metaboxes from scratch or using a library like CMB2. The final, user-directed decision was to leverage the already-installed **ACF Pro** plugin. This simplified development significantly and provides a more robust and familiar admin UI.
- **File Structure:** The initial best-practice suggestion of using `assets` and `includes` sub-folders was overridden by the user's request to keep all four files flat in the main plugin directory.