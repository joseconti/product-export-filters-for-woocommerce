# Getting started — Export Filters for WooCommerce

Shortest path from installed to your first filtered export.

1. Go to **Products** → **Export** in your WordPress admin.
2. Find the new **Filter by date** field (it sits above the "Generate CSV"
   button, alongside WooCommerce's own column/type/category options).
3. Choose **Date created** (when the product was added) or **Date last
   modified** (when it was last edited).
4. Two new date fields appear: **From** and **To**.
   - Want a single day? Put the same date in both.
   - Want "everything since March 1st"? Fill only **From**.
   - Want "everything up to March 31st"? Fill only **To**.
5. Click **Generate CSV**, same as always. The download only contains
   products matching your date range — everything else about the export
   (columns, category filter, product type) still works exactly like stock
   WooCommerce.

Leave the field on **All dates** and nothing changes — the export behaves
exactly as if this plugin were not installed.
