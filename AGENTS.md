# ZaminTaWeb - Project Context

## Project Overview
A Persian (Farsi) dual-domain web application combining:
1. **Educational Course Platform** - Web development academy offering structured courses with installment payments, episode-based video content, and student enrollment workflow
2. **Medical Equipment E-commerce** - Online store ("کالای پزشکی پارس طب ۱۱۵") with product catalog, cart, checkout, and order management

## Technology Stack

### Backend
- **Framework**: Laravel 12 (PHP 8.2+)
- **Admin Panel**: Filament v4 with shadcn theme
- **Auth**: Laravel Fortify + Custom OTP (IPPanel SMS)
- **SSR**: Inertia.js v2 with server-side rendering
- **Database**: MySQL (primary), SQLite (testing)
- **Queue/Cache**: sync (dev), database driver available

### Frontend
- **Framework**: Svelte 5 with runes ($state, $derived, $props)
- **Inertia**: @inertiajs/svelte v2
- **CSS**: Tailwind CSS v4 with CSS custom properties
- **UI**: shadcn-svelte (49 components)
- **Icons**: Lucide Svelte
- **Forms**: sveltekit-superforms + zod v4
- **Build**: Vite 7 + TypeScript 5.9
- **Carousel**: Embla Carousel Svelte
- **Maps**: Leaflet

### DevOps
- **Dev**: Laravel Sail (Docker) or `composer dev`
- **Testing**: Pest PHP v4
- **Linting**: ESLint + Prettier + Laravel Pint
- **Node**: ^22

## Architecture

### Directory Structure
```
app/                          # Laravel backend
├── Actions/Fortify/          # Fortify auth actions
├── Filament/Resources/       # Admin panel resources
├── Helpers/                  # PersianNumberHelper
├── Http/Controllers/         # Web controllers
│   └── Auth/                 # OTP, Fortify auth controllers
│   └── Settings/             # Profile, password, 2FA
├── Models/                   # Eloquent models (24 existing)
├── Notifications/            # ForgotPassword
├── Policies/                 # TicketPolicy
├── Providers/                # App, Fortify, Filament providers
├── Services/                 # CartService, IPPanelService
config/                       # 14 config files
database/                     # 60 migrations, 5 factories, 3 seeders
resources/                    # Frontend assets
├── js/                       # Svelte + TypeScript
│   ├── components/           # Shared Svelte components + shadcn/ui
│   ├── layouts/              # Public, App, Auth, Settings layouts
│   ├── pages/                # Inertia page components
│   ├── hooks/                # Stores/hooks (useAppearance, etc.)
│   ├── lib/                  # utils (cn, t, formatPrice, reveal)
│   └── types/                # TypeScript definitions
├── css/                      # Tailwind v4 + theme variables
└── views/                    # Blade templates (root Inertia + Filament)
routes/
├── web.php                   # Main web routes
├── settings.php              # Settings routes
└── console.php               # Artisan commands
tests/                        # Pest PHP tests
```

### Key Models

**Course Platform:**
- `User` - Student accounts with OTP auth
- `Course` - Course with price, prepayment, installment options
- `CourseCategory` - Course categorization
- `CourseEpisode` - Video episodes with preview flag
- `CourseEnrollment` - Student enrollment with receipt upload (status: pending/approved/rejected)

**E-commerce:**
- `Category` - Multi-level product categories (tree structure)
- `Brand` - Product brands
- `Product` - Products with SKU, images, technical_specs (JSON)
- `Cart` / `CartItem` - Shopping cart (session/user based)
- `Order` / `OrderItem` - Orders with shipping, payment status
- `Discount` - Percentage/fixed amount (applies to categories/brands/products)
- `Coupon` - Discount codes
- `Review` - Product reviews (rating 1-5, admin approval)
- `Address` - User addresses with province/city
- `Ticket` / `TicketMessage` - Support tickets

**Content:**
- `Post` / `PostCategory` - Blog/articles
- `Menu` - Navigation menus (tree structure)
- `Slider` - Homepage sliders
- `Newsletter` - Email subscribers

**Note**: Some e-commerce models (Product, Brand, Order, OrderItem, Cart, CartItem, CartActivity, Post, PostCategory, PurchaseInvoice, PurchaseInvoiceItem, Color) have migrations but missing model classes in `app/Models/`.

### Routes (web.php)

| Method | URI | Controller |
|--------|-----|------------|
| GET | `/` | HomeController@index |
| GET | `/courses` | CourseController@index |
| GET | `/courses/{course:slug}` | CourseController@show |
| GET | `/dashboard` | DashboardController (auth) |
| POST | `/courses/{course:slug}/enrollments` | CourseEnrollmentController (auth) |
| GET | `/login` | OtpController@showLogin (guest) |
| POST | `/login/send-otp` | OtpController@sendOtp (guest) |
| POST | `/login/verify-otp` | OtpController@verifyOtp (guest) |
| GET/PATCH/DELETE | `/settings/profile` | ProfileController (auth) |
| GET/PUT | `/settings/password` | PasswordController (auth, throttled) |
| GET | `/settings/appearance` | Inertia page (auth) |
| GET | `/settings/two-factor` | TwoFactorAuthController (auth) |

Note: E-commerce routes exist in controllers but are NOT yet registered in web.php. See `app/Http/Controllers/` for ProductController, CartController, CheckoutController, FavoriteController, ReviewController, BrandController, SearchController, OfferController, ContactController, NewsletterController, BlogController, TicketController, AddressController.

## Coding Conventions

### PHP/Laravel
- **Namespace**: `App\Models`, `App\Http\Controllers`, `App\Services`, etc.
- **Routing**: Explicit route definitions (not route model binding in closures)
- **Validation**: Inline in controllers with `$request->validate()`, closures for rules
- **Filament**: Resources use v4 schema API (`Filament\Schemas\Schema`)
- **Translations**: Persian labels via `->label('عنوان فارسی')`
- **Auth**: Fortify for email/password + OTP for phone login
- **Date/Time**: `Asia/Tehran` timezone, Persian calendar via `ariaieboy/filament-jalali`

### Svelte 5
- **Runes**: Use `$props()`, `$state()`, `$derived()` (not legacy `export let`)
- **Types**: TypeScript with strict mode, `interface Props { ... }` pattern
- **Styling**: Tailwind v4 with `cn()` utility for conditional classes
- **Translations**: `t('key')` function from `@/lib/utils.ts`
- **Formatting**: `formatPrice(n)` for Persian number formatting
- **Animations**: `reveal` action for scroll-triggered animations
- **Routing**: Inertia `<Link>` component, `router.get/post/put/delete`
- **Layout**: Svelte snippets (`{@render children?.()}`) for slot content
- **RTL**: All public pages use `dir="rtl"` on root element
- **Theme**: Dark/light mode via `mode-watcher`, `.dark` class

### Persian/Farsi Conventions
- **Fonts**: Dana (body), Kalameh (headings)
- **Numbers**: Persian digits (۰-۹) via `toPersianDigits()` or `formatPrice()`
- **Currency**: Price in تومان
- **Locale**: `fa`, timezone `Asia/Tehran`
- **Faker**: Persian Faker for seed data (medical products in Persian)
- **RTL Animations**: All bits-ui floating components (dropdown, menubar, select, context-menu, hover-card, popover, tooltip) need `rtl:` variants for `data-[side=left]` and `data-[side=right]` slide-in/translate classes — see `resources/js/components/ui/*/` for the pattern

### Testing (Pest)
- **Framework**: Pest PHP v4
- **Database**: RefreshDatabase trait in Feature tests
- **Config**: SQLite in-memory, array cache/session/mail, sync queue
- **Pattern**: `test()->extend(Tests\TestCase::class)->use(RefreshDatabase::class)->in('Feature');`
- **Location**: `tests/Feature/` (auth, settings, dashboard)

## Available Commands

```bash
# Development (all services)
composer dev                    # Laravel serve + queue + logs + Vite
composer dev:ssr                # Same + Inertia SSR

# Build
npm run build                   # Vite build
npm run build:ssr               # Vite build + SSR
npm run dev                     # Vite dev server only

# Quality
npm run lint                    # ESLint
npm run format                  # Prettier
composer pint                   # Laravel Pint
npm run svelte:check            # Svelte type check

# Testing
composer test                   # Pest tests
php artisan test                # PHPUnit/Pest

# Shadcn
npm run shadcn:update           # Update shadcn components
```

## Skills (Loaded via opencode)

| Skill | Purpose |
|-------|---------|
| `laravel-filament-admin` | Creating Filament resources, widgets, forms, tables, relation managers |
| `persian-ecommerce` | Persian e-commerce backend patterns (models, services, API routes) |
| `otp-sms-auth` | OTP authentication, SMS messaging, Telegram bot integration |
| `shetabit-payment` | Payment gateway integration with purchase/verify/callback flow |
| `image-processing` | Intervention Image v3 - resize, convert to WebP, optimize |
| `media-reader` | Read images and PDFs for OCR/text extraction |
| `task-management` | Feature subtask tracking with dependency management |
| `graphify` | Codebase knowledge graph generation and querying |
| `context7` | Live documentation lookup for libraries/frameworks |

## Sub-Agents

Located in `.opencode/agents/` - project-specific specialized agents:

| Agent | Purpose |
|-------|---------|
| `laravel-backend` | PHP/Laravel backend development (models, controllers, services) |
| `filament-admin` | Filament admin panel resources, pages, widgets |
| `svelte-frontend` | Svelte 5 + Inertia frontend components and pages |
| `ecommerce` | E-commerce features (products, cart, checkout, orders) |
| `course-platform` | Educational course platform features |
| `database` | Migrations, factories, seeders, schema design |
| `testing` | Pest/PHPUnit test writing |
| `localization` | Persian/RTL localization, translations |
| `payment` | Payment gateway integration via shetabit |
| `image-processor` | Image optimization and processing |
