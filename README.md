# Product CMS

This is a small Laravel product and order management dashboard. The main goal was to build a clean admin experience for managing products, viewing orders, checking order details, and working with seeded demo data.

I kept the first version focused on the requested scope instead of adding extra business modules too early. The structure should still be easy to extend later if more rules are added around customers, refunds, discounts, inventory, or permissions.

## Stack Used

- PHP 8.2
- Laravel 12
- Tailwind CSS 4
- Alpine.js 3
- Chart.js 4

## Setup Instructions

1. Install PHP dependencies:

```bash
composer install
```

2. Create the environment file:

```bash
copy .env.example .env
```

3. Generate the application key:

```bash
php artisan key:generate
```

4. Configure the database in `.env`.

The default local setup can use SQLite, but MySQL can also be configured.

5. Run migrations and seeders:

```bash
php artisan migrate --seed
```

6. Install frontend dependencies:

```bash
npm install
```

7. Build frontend assets:

```bash
npm run build
```

8. Create the public storage symlink for uploaded product images:

```bash
php artisan storage:link
```

9. Start the local development environment:

```bash
php artisan serve
```

## Running Tests

Run the full test suite:

```bash
php artisan test --compact
```

Run a single test file:

```bash
php artisan test --compact tests/Feature/ProductManagementTest.php
```

## Decisions Made

### 1. Simple models first

I built the first version around `Product`, `Order`, and `OrderItem`. I did not add extra models for customers, invoices, discounts, or shipping because the task did not include enough business rules for those areas.

The current models are enough for the dashboard, product CRUD, order listing, order details, and seeded demo data. If the project continues, I would expand the model gradually instead of guessing the final business flow from the start.

### 2. Seeded orders instead of full order CRUD

I used factories and seeders for orders instead of building full order creation and editing. My reason was simple: order creation usually depends on many rules such as customer type, payment flow, refund policy, sales channel, stock reservation, and delivery logic.

Since those rules were not fully defined, I preferred to create realistic sample data that supports the dashboard and order pages without inventing behavior that might be wrong later.

### 3. Blade and Tailwind for the UI

I used Blade and Tailwind instead of adding a ready-made admin package. This gave me better control over the layout and kept the code closer to the actual task.

The UI work focused on the dashboard, product list, product form, order list, order details, filters, status labels, pagination, and basic responsive behavior.

### 4. Enums for repeated values

I used enums for repeated values like product status, order status, and order channel. This made the code cleaner because the same values are reused in filters, labels, seeders, and views.

### 5. Avoided adding unclear business modules

I did not add permissions, invoices, discounts, advanced inventory, multi-currency, or refund logic in this version. These features are important, but they need client input first.

For this submission, I focused more on making the base clean and understandable.

## Questions I Would Ask the Client Before Writing More Code

1. Are the products simple products only, or do they have variants like size, color, or material?

2. Do products need categories, brands, tags, or collections?

3. Do you sell bundles or kits made from multiple products?

4. Which sales channels should be supported?
   For example: website, Instagram, WhatsApp, retail, marketplace, or manual orders.

5. What should happen when an order is refunded?
   Do you need full refunds, partial refunds, replacements, exchanges, or all of them?

6. How should VAT or tax be handled?
   Is tax included in the product price, added later, or different by country?

7. Do you need multi-country or multi-currency support?

8. How should discounts work?
   Coupon codes, automatic discounts, campaign discounts, or customer-group pricing?

9. Do you need roles and permissions?
   For example: admin, manager, warehouse staff, support, or finance.

10. What should the shipping flow include?
    Courier integration, tracking number, pickup, local delivery, warehouse status, or shipment history.

11. Do customers need accounts, or is this mainly an internal admin dashboard?

## What I Would Build Next With Another Two Days

With two more days, I would focus on the areas that make the system closer to a real back-office tool:

1. Add soft deletes where they make sense.

2. Add a proper customer model instead of keeping customer information only on orders.

3. Improve stock handling, especially around pending and paid orders.

4. Add low-stock and out-of-stock alerts.

5. Add a proper sales channel table if channels need different behavior.

6. Add the first version of discounts.

7. Add product import/export and bulk actions.

8. Add roles and permissions.

9. Add invoice generation.

10. Improve dashboard reports, especially sales by date, channel, and product.

## Weakest Part of My Submission

The weakest part is probably the order workflow.

Right now, the system gives a good overview of products, orders, and sales, but it does not go very deep into what happens after an order is placed. Orders can be viewed and analyzed, but there is still no full flow for moving an order through its stages and updating stock based on that.

## Time Spent

I spent around 30 minutes at the beginning analyzing the task before writing code. I used a simple empty text file to break the work down into smaller parts, including migrations, models, functions, possible client questions, and future features.

After that, I worked through the implementation in stages: backend structure, seeders, UI, cleanup, and testing/verification.

Overall, the task took around 3 to 4 hours.

## AI Usage

I used AI tools to support the work, but not to replace the technical decisions.

I used ChatGPT to review my initial plan, organize my thoughts, and improve some written explanations. I also used it for UI/UX inspiration while thinking about the dashboard layout.

During development, I used Laravel Boost and Codex as coding assistants to speed up implementation and refinement.

The final structure, tradeoffs, and integration decisions were still made based on the project requirements and the actual Laravel codebase.
