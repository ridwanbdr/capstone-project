# Technical Specification
## Clothing Manufacturing Operations Management System

---

## Table of Contents
1. [System Purpose](#system-purpose)
2. [Technical Stack](#technical-stack)
3. [System Actors](#system-actors)
4. [Core Data Structure](#core-data-structure)
5. [Entity Relationship Diagram](#entity-relationship-diagram)
6. [Core Features](#core-features)
7. [User Interface Specifications](#user-interface-specifications)
8. [Notes](#notes)

---

## System Purpose

A web-based system developed to help clothing manufacturing SMEs manage their daily operations including inventory tracking, production orders, quality control, and centralized sales transactions.

---

## Technical Stack

- **Framework**: Laravel (PHP)
- **Database**: MySQL
- **Architecture**: MVC with modular components
- **Frontend**: Blade templates with optional modern JS framework
- **Styling**: Tailwind CSS (via Play CDN) *
- **Timestamps**: Automatic tracking via `created_at` and `updated_at` fields

---

## System Actors

### 1. Admin
- Manage user accounts (registration, deletion, viewing)
- Access all features for user activity monitoring

### 2. Warehouse Manager
- Record incoming and outgoing inventory
- View complete inventory history

### 3. QC Staff
- Input quality check results (pass/fail)
- View checked items list

### 4. Production Team
- Create bulk production orders
- Input order details (fabric type, color, size, quantity)
- View incoming orders

### 5. Finance Staff
- Record sales and operational transactions
- View complete sales history

---

## Core Data Structure

### 1. User
**Attributes:**
- `user_id` (Primary Key)
- `username`
- `password`
- `role`
- `full_name`
- `email`
- `created_at`
- `updated_at`

**Description:** Stores all user data, including login credentials and assigned roles.

---

### 2. Item
**Attributes:**
- `item_id` (Primary Key)
- `item_name`
- `item_type`
- `stock`
- `unit`
- `unit_price`
- `created_at`
- `updated_at`

**Description:** Represents inventory items managed in the warehouse.

---

### 3. IncomingItem
**Attributes:**
- `incoming_id` (Primary Key)
- `item_id` (Foreign Key → Item)
- `user_id` (Foreign Key → User)
- `date_in`
- `quantity`
- `created_at`
- `updated_at`

**Description:** Records items that enter the warehouse, managed by the Warehouse Manager.

---

### 4. OutgoingItem
**Attributes:**
- `outgoing_id` (Primary Key)
- `item_id` (Foreign Key → Item)
- `user_id` (Foreign Key → User)
- `date_out`
- `quantity`
- `created_at`
- `updated_at`

**Description:** Records items leaving the warehouse.

---

### 5. QualityCheck
**Attributes:**
- `qc_id` (Primary Key)
- `item_id` (Foreign Key → Item)
- `user_id` (Foreign Key → User)
- `check_date`
- `qc_status`
- `note`
- `created_at`
- `updated_at`

**Description:** Records product quality check results performed by QC Staff.

---

### 6. BulkOrder
**Attributes:**
- `order_id` (Primary Key)
- `user_id` (Foreign Key → User)
- `customer_name`
- `order_date`
- `order_status`
- `created_at`
- `updated_at`

**Description:** Represents production orders handled by the Production Team.

---

### 7. OrderDetail
**Attributes:**
- `detail_id` (Primary Key)
- `order_id` (Foreign Key → BulkOrder)
- `product_name`
- `fabric_type`
- `color`
- `size`
- `quantity`
- `created_at`
- `updated_at`

**Description:** Contains detailed specifications for each bulk order.

---

### 8. SalesTransaction
**Attributes:**
- `transaction_id` (Primary Key)
- `user_id` (Foreign Key → User)
- `item_id` (Foreign Key → Item)
- `transaction_date`
- `quantity_sold`
- `total_price`
- `description`
- `created_at`
- `updated_at`

**Description:** Records sales or operational financial transactions handled by Finance Staff.

---

## Entity Relationship Diagram (ERD)

### Relationships Overview

#### User Relationships (One-to-Many)
- **User** → **IncomingItem**: One user can record many incoming items
- **User** → **OutgoingItem**: One user can record many outgoing items
- **User** → **QualityCheck**: One user can perform many quality checks
- **User** → **BulkOrder**: One user can create many bulk orders
- **User** → **SalesTransaction**: One user can record many sales transactions

#### Item Relationships (One-to-Many)
- **Item** → **IncomingItem**: One item can have many incoming records
- **Item** → **OutgoingItem**: One item can have many outgoing records
- **Item** → **QualityCheck**: One item can be checked multiple times
- **Item** → **SalesTransaction**: One item can appear in many sales transactions

#### BulkOrder Relationships (One-to-Many)
- **BulkOrder** → **OrderDetail**: One bulk order contains many order details

### Key Constraints
- All foreign key relationships enforce referential integrity
- `user_id` tracks which user performed each action for audit purposes
- Automatic timestamps (`created_at`, `updated_at`) on all entities for data modification tracking

### ERD Diagram Summary

```
User (1) ──────< (N) IncomingItem >── (N) Item (1)
     │
     ├─────────< (N) OutgoingItem >─── (N) Item (1)
     │
     ├─────────< (N) QualityCheck >─── (N) Item (1)
     │
     ├─────────< (N) BulkOrder (1) ───< (N) OrderDetail
     │
     └─────────< (N) SalesTransaction >── (N) Item (1)
```

---

## Core Features

### Account Management (Admin)
- User registration
- User listing
- Account deletion

### Inventory Management (Warehouse)
- Record stock movement
- View stock history
- Automatic stock updates

### Quality Control
- Quality check results input
- QC history viewing

### Production Management
- Bulk order creation
- Order status tracking

### Sales Management (Finance)
- Sales transaction recording
- Automatic price calculation
- Transaction history viewing

---

## User Interface Specifications

### Dashboard Page

#### General Design Requirements
- **Style**: Modern, dynamic, and simple interface
- **Template Engine**: Blade templating (Laravel)
- **Styling Framework**: Tailwind CSS (via CDN)
- **Responsiveness**: Fully responsive design for all screen sizes

---

#### Layout Components

##### 1. Sidebar
**Design Features:**
- Responsive sidebar with collapsible functionality
- Smooth transition animations
- Active state indication for current page
- Mobile-friendly hamburger menu

**Navigation Items:**
Each menu item includes an icon/logo with support for submenu expansion

**Menu Structure:**
- **Dashboard** (home icon)
- **Inventory Management** (box icon)
  - Incoming Items
  - Outgoing Items
  - Stock List
- **Quality Control** (check-circle icon)
  - Quality Check Input
  - QC History
- **Production Orders** (clipboard icon)
  - Create Bulk Order
  - View Orders
- **Sales Transactions** (dollar icon)
  - Record Sales
  - Transaction History
- **User Management** (users icon) - Admin only
  - User List
  - Add User

---

##### 2. Navbar (Top Bar)
**Position:** Fixed top

**Layout:**
- **Left Side**: 
  - Menu toggle button (mobile)
  - Breadcrumb navigation (desktop)
- **Right Side**: 
  - User welcome message: `Welcome, {username}`
  - User avatar/icon (optional)
  - Logout button with icon

**Styling:** 
- Clean design with subtle shadow
- Contrasting background from main content
- Sticky positioning for consistent access

---

##### 3. Main Content Area

**Quick Menu Section:**
- **Layout**: Grid layout (2x3 or 3x2 based on screen size)
- **Card Count**: 4-6 quick access cards
- **Card Components**:
  - Icon (large, colored)
  - Menu title (bold)
  - Brief description or current count
  - Hover effect with elevation
  - Click action to navigate to feature

**Example Quick Menu Cards:**
1. **Add Incoming Stock** - Add new items to inventory
2. **Add Outgoing Stock** - Record items leaving warehouse
3. **Create Quality Check** - Perform QC inspection
4. **New Bulk Order** - Create production order
5. **Record Sales** - Add sales transaction
6. **View Reports** - Access analytics and reports

---

**Recent Activity Table:**
- **Title**: "Recent Stock & Transaction Activity"
- **Rows**: 7 rows of latest data
- **Columns**:
  1. Date/Time
  2. Activity Type (Badge: Incoming/Outgoing/QC/Sales)
  3. Item Name
  4. Quantity
  5. User (who performed the action)
  6. Status (Badge: Success/Pending/Failed)

**Table Features:**
- Alternating row colors for readability
- Hover effect on rows with slight elevation
- Responsive table design (horizontal scroll on mobile)
- Empty state message if no data
- "View All" button at the bottom right
- Pagination indicator

**Sample Table Structure:**
```
┌────────────────┬─────────────┬──────────────┬──────────┬──────────┬──────────┐
│ Date/Time      │ Type        │ Item Name    │ Quantity │ User     │ Status   │
├────────────────┼─────────────┼──────────────┼──────────┼──────────┼──────────┤
│ Oct 27, 10:30  │ Incoming    │ Cotton Fabric│ 50 m     │ John Doe │ Success  │
│ Oct 27, 09:15  │ Sales       │ T-Shirt Blue │ 25 pcs   │ Jane S.  │ Success  │
│ Oct 26, 16:45  │ QC Check    │ Polo Shirt   │ 100 pcs  │ Mike R.  │ Passed   │
│ Oct 26, 14:20  │ Outgoing    │ Polyester    │ 30 m     │ Sarah M. │ Success  │
│ Oct 26, 11:00  │ Bulk Order  │ Hoodie Black │ 200 pcs  │ Tom B.   │ Pending  │
│ Oct 25, 15:30  │ Incoming    │ Buttons      │ 500 pcs  │ John Doe │ Success  │
│ Oct 25, 13:45  │ Sales       │ Jeans Denim  │ 15 pcs   │ Jane S.  │ Success  │
└────────────────┴─────────────┴──────────────┴──────────┴──────────┴──────────┘
                                                          [View All →]
```

---

#### Color Scheme
- **Primary**: Blue tones (`bg-blue-600`, `text-blue-600`) for main actions and links
- **Secondary**: Gray tones (`bg-gray-100`, `text-gray-600`) for neutral elements
- **Success**: Green (`bg-green-500`, `text-green-600`) for positive actions/status
- **Warning**: Yellow (`bg-yellow-500`, `text-yellow-600`) for alerts
- **Danger**: Red (`bg-red-500`, `text-red-600`) for critical actions/status
- **Background**: Light gray (`bg-gray-50`) or white (`bg-white`)
- **Text**: Dark gray (`text-gray-800`) for primary text, lighter for secondary

---

#### Responsive Behavior

**Desktop (≥1024px):**
- Sidebar always visible and expanded by default
- Full navigation menu visible
- Multi-column grid layouts
- Table shows all columns

**Tablet (768px-1023px):**
- Sidebar collapsible, overlay on content when expanded
- Reduced padding and margins
- 2-column grid for quick menu cards
- Table may hide less important columns

**Mobile (<768px):**
- Sidebar hidden by default with hamburger menu toggle
- Hamburger menu in top-left of navbar
- Single column layout for quick menu cards
- Table switches to horizontal scroll or card view
- Larger touch targets for buttons and links

---

#### Additional UI Elements

**Buttons:**
- Primary: `bg-blue-600 hover:bg-blue-700 text-white`
- Secondary: `bg-gray-200 hover:bg-gray-300 text-gray-800`
- Danger: `bg-red-600 hover:bg-red-700 text-white`
- Rounded corners: `rounded-lg`
- Padding: `px-4 py-2`

**Cards:**
- Background: `bg-white`
- Shadow: `shadow-md hover:shadow-lg`
- Border radius: `rounded-lg`
- Padding: `p-6`

**Badges:**
- Small rounded pills with colored backgrounds
- Text size: `text-xs`
- Padding: `px-2 py-1`
- Examples:
  - Incoming: `bg-blue-100 text-blue-800`
  - Outgoing: `bg-orange-100 text-orange-800`
  - Success: `bg-green-100 text-green-800`
  - Pending: `bg-yellow-100 text-yellow-800`

**Icons:**
- Use icon library: Heroicons, Font Awesome, or Lucide Icons
- Consistent sizing: `w-6 h-6` for menu items, `w-4 h-4` for inline icons
- Color coordination with menu/button colors

---

## Notes

### Development Guidelines
- All data modifications are tracked with timestamps for audit purposes
- Implement proper validation for all user inputs
- Use Laravel's built-in authentication and authorization
- Follow PSR coding standards for PHP
- Implement CSRF protection on all forms
- Use database transactions for critical operations
- Add proper error handling and logging
- Implement soft deletes where appropriate

### Security Considerations
- Hash all passwords using bcrypt
- Implement role-based access control (RBAC)
- Validate and sanitize all user inputs
- Use prepared statements for database queries
- Implement rate limiting for API endpoints
- Add XSS protection headers
- Regular security audits and updates

### Performance Optimization
- Index frequently queried columns
- Implement caching for frequently accessed data
- Use lazy loading for relationships
- Optimize database queries with eager loading
- Compress and minify assets
- Use CDN for static assets

### Future Enhancements
- RESTful API for mobile application
- Real-time notifications using WebSockets
- Advanced reporting and analytics dashboard
- Export functionality (PDF, Excel)
- Barcode/QR code scanning integration
- Multi-language support
- Email notifications for critical events

---

**Document Version:** 1.0  
**Last Updated:** October 27, 2025  
**Author:** Development Team