# Agent Guidelines

- Avoid refactoring existing plugin code unless specifically requested.
- Keep URL fields as plain text inputs sanitized with `sanitize_text_field`. Do not convert them to `<input type="url">` or use `esc_url_raw` without explicit instructions.
- Focus changes solely on fulfilling user requests.
