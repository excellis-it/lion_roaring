# Admin Portal Page Titles

This document lists all the page titles to be added to the `resources/views/user/admin` folder files.

## Page Title Structure

Each page should have a visible page title (not the HTML `<title>` tag) displayed on the page using:

```blade
<div class="page-header">
    <h3 class="page-title">{{ $pageTitle }}</h3>
</div>
```

## Page Titles by Module

### 1. Donations

-   **List**: "Donations List"

### 2. Contact Us Messages

-   **List**: "Contact Us Messages"

### 3. Newsletters

-   **List**: "Newsletters List"

### 4. Testimonials

-   **List**: "Testimonials List"
-   **Create**: "Create Testimonial"
-   **Edit**: "Edit Testimonial"

### 5. Our Governance

-   **List**: "Our Governance List"
-   **Create**: "Create Our Governance"
-   **Edit**: "Edit Our Governance"

### 6. Our Organizations

-   **List**: "Our Organizations List"
-   **Create**: "Create Our Organization"
-   **Edit**: "Edit Our Organization"

### 7. Organization Centers

-   **List**: "Organization Centers List"
-   **Create**: "Create Organization Center"
-   **Edit**: "Edit Organization Center"

### 8. Services

-   **Update**: "Manage Services"

### 9. Pages Module

#### Home CMS

-   **Update**: "Home Page CMS"

#### Details

-   **Update**: "Details Page"

#### Organizations

-   **Update**: "Organizations Page"

#### About Us

-   **Update**: "About Us Page"

#### FAQ

-   **List**: "FAQs List"
-   **Create**: "Create FAQ"
-   **Edit**: "Edit FAQ"

#### Gallery

-   **List**: "Gallery List"
-   **Create**: "Add Gallery Item"
-   **Edit**: "Edit Gallery Item"

#### Ecclesia Association

-   **Update**: "Ecclesia Association Page"

#### Principle and Business

-   **Update**: "Principle and Business Page"

#### Contact Us CMS

-   **Update**: "Contact Us Page CMS"

#### Articles of Association

-   **Update**: "Articles of Association Page"

#### Footer

-   **Update**: "Footer Settings"

#### Register Agreements

-   **Update**: "Register Page Agreements"

#### PMA Terms

-   **Update**: "PMA Terms Page"

#### Privacy Policy

-   **Update**: "Privacy Policy Page"

#### Terms and Conditions

-   **Update**: "Terms and Conditions Page"

### 10. Countries

-   **List**: "Countries List"
-   **Create**: "Add Country"
-   **Edit**: "Edit Country"

### 11. Site Settings

-   **Settings**: "Site Settings"
-   **Menu Names**: "Menu Names Management"

### 12. Admin Management

-   **List**: "Admin List"
-   **Add**: "Add Admin"
-   **Edit**: "Edit Admin"

### 13. Ecclesias

-   **List**: "Ecclesias List"
-   **Edit**: "Edit Ecclesia"

### 14. Members

-   **List**: "Members List"
-   **Rejected**: "Rejected Members"

### 15. Customers

-   **List**: "Customers List"
-   **Create**: "Add Customer"
-   **Edit**: "Edit Customer"

### 16. Plans (Membership Plans)

-   **List**: "Membership Plans List"
-   **Create**: "Create Membership Plan"
-   **Edit**: "Edit Membership Plan"

## Implementation Notes

1. Add page titles using `Helper::getMenuName()` for dynamic naming where applicable
2. Use consistent styling across all pages
4. Email templates don't need page titles
