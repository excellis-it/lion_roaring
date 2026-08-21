---
title: Create Member
updated: 2026-08-21
status: ready
sidebar_key: create_member
---

# Create Member

## What this is

**Create Member** (screen title: **Create Partners**) is how staff add a new person to Lion Roaring from the User PMA — login, role, country, permissions, and (when needed) a membership plan — in one form.

This is the normal path for onboarding someone who should already have access, without waiting for public self-registration.

**Where to find it**

1. Open **All Members** in the sidebar (needs **Manage Partners**).
2. On the members list, click **+ Add Members** (needs **Create Partners**).
3. Complete the form and save.

If you do not see **All Members** or **+ Add Members**, you do not have the right permissions. Ask a Super Admin or someone who manages Role Permission.

---

## Who can create members

| Who | Can open Create Member? | Important limits |
|-----|-------------------------|------------------|
| User with **Create Partners** | Yes | Still limited by their own User Type and country (see below) |
| User with only **Manage Partners** | Can open the list, **not** the create form | Need Create Partners separately |
| Super Admin | Yes | User Type options follow the **current site** (same as other staff): on **Global (`.org`)** only **Global**; on a **regional site** only **Regional** and **G_R**. Country still unrestricted except the special Global country code `GL`. |
| Global staff | Yes (if permitted) | On **Global site**: form offers **Global** only. `G_R` is assigned from a regional country context, not here. |
| Regional staff | Yes (if permitted) | Form offers **Regional** and **G_R**; Regional members must be in **their own country**. |
| G_R staff | Yes (if permitted) | Same instance rules as above (Global site → Global only; regional site → Regional & G_R). |

**Important:** The User Type dropdown is limited to types you may create for this site. Picking a type outside that list (or forcing it via API) fails with: *You are not authorized to create partners of this type.*

Regional creators who pick another country get: *You are not authorized to create partners in this country.*

---

## What Create Member is not

People often confuse these screens:

| Screen | What it actually creates |
|--------|---------------------------|
| **All Members → Add Members** (this page) | A **person / partner account** |
| **Membership Management → Create Plan** | A **membership plan/tier**, not a person |
| **Membership Management → Members** | List of **subscriptions** (who paid / which plan) — does not add new people |
| **Signup Rules** | Rules for **public website registration** only — **not** applied when you create a member here |
| **Super Admin** menu | Creates **Super Admin** accounts only |
| **Membership** (self-service, for members themselves) | Buy/renew your own plan — not admin create |
| Team Chat “Add Member” | Adds an **existing** user to a chat group |

Public signup (website / app register) uses Signup Rules and a different process. Admin Create Member does **not** run Signup Rules and does **not** require the new person to sign the register agreement before the account is active.

---

## Before you start — decide these four things

1. **User Type** — How this person connects to Global vs Regional domains:
   - **Global** — works on the Global site; not for a single regional country host.
   - **Regional** — tied to one country instance.
   - **G_R** — can use Global **or** their assigned regional country (not other regionals).
2. **Role** — The role template (for example MEMBER_SOVEREIGN, staff roles, ecclesia roles). This drives permissions and membership fields.
3. **Country** — The country record on the member’s profile (not the special `GL` Global country row).
4. **Membership vs exclude** — For MEMBER_SOVEREIGN: either pick a plan, or mark them excluded from membership requirements.

Wrong choices here are the most common source of “they can’t log in,” “wrong country,” or “membership wall” tickets.

---

## Step-by-step: filling the form

### 1. Login details

| Field | What to enter | Rules |
|-------|---------------|--------|
| Username | Unique login name | Required; must not already exist |
| Email | Their real email | Required; unique; used for the welcome email with password |
| Lion Roaring ID | Auto prefix + **4 digits** you choose | Full ID must be unique. Prefix is generated as `LR` + daily sequence + date |
| Roar ID | Optional extra ID | Optional |
| User Type | Global / Regional / G_R (options depend on who you are) | Required; see authorization rules above |
| Phone | Phone with country code UI | Required |
| Password / Confirm | Temporary password you will share | At least **8** characters, **no spaces**, and at least one of **`@` `$` `%` `&`** |

The system also builds an internal personal email shaped like `firstname…@lionroaring.us`. That is separate from the email you type for login/contact.

### 2. Personal and address details

Required: first name, last name, address, country, state, city, zip, phone.  
Optional: middle name, address line 2, ecclesia (house) assignment when the list applies.

### 3. Role

Choose one role from the templates (role types used for partners/staff, not the Super Admin template list).

**If the role is an Ecclesia role** (marked as ecclesia in Role Permission):

- You **must** select at least one **House Of ECCLESIA** checkbox.
- Leaving that empty blocks save with a clear error.

**If the role is marked Admin** in Role Permission:

- The system forces the stored User Type to **G_R**, even if you selected something else. Plan for that when creating admin-style roles.

### 4. Membership section

| Situation | What you must do |
|-----------|------------------|
| Role is **MEMBER_SOVEREIGN** and you do **not** check “Exclude from membership requirements” | You **must** choose a **membership tier/plan** |
| Role is **MEMBER_SOVEREIGN** and you **do** check exclude | No plan required; they bypass the membership gate like excluded users |
| Role is **not** MEMBER_SOVEREIGN | You **must** select at least one **permission** (or accept the permissions driven by the role UI). Membership tier is not the main path |

When you assign a tier to MEMBER_SOVEREIGN (and they are not excluded):

- Permissions for that person are taken from the **plan’s permission list**.
- A subscription is created starting **today**, ending after the plan’s duration (months on the plan; often 12 if not set otherwise).
- Price/method on the subscription follow the plan.

### 5. Permissions section

- For non–MEMBER_SOVEREIGN roles, permissions are required.
- Use the category search / checkboxes to match what the person should see in the sidebar.
- MEMBER_SOVEREIGN with a plan usually inherits plan permissions instead of a free-form pick.

If you give too few permissions, menus will be missing. If you give too many, they see admin tools they should not.

### 6. Save

On success:

1. The member is created **active** (`status` on) and marked accepted.
2. A personal Spatie role is created from their username and permissions are attached.
3. Audit log records **member_created** (visible under All Members → Audit Logs if you have access).
4. An email is sent to the address you entered, including their name, role label, and **the password in plain text** (welcome / sign-up style mail).
5. You return to the All Members list with a success message.

Tell the member to change their password after first login when that is your practice. The welcome email contains the temporary password you set.

---

## What the new member can do immediately

- They can log in (active account, accepted flag set).
- They do **not** go through Signup Rules or public registration validation.
- They may still hit the **register agreement** flow on first panel use if your panel requires a signed agreement (Super Admins are exempt; this new member is not Super Admin).
- If they are MEMBER_SOVEREIGN with a valid subscription (or membership_excluded), they pass the membership gate. If you forgot the plan and did not exclude them, they can be blocked by membership access even though the account exists.

---

## Rules by who is creating (cheat sheet)

### Super Admin

- May select User Type: Global, Regional, or G_R.
- May select any normal country (not `GL`).
- Sees all ecclesias on the form.
- Can create any allowed role combination the form supports.

### Global creator (not Super Admin)

- Form User Types shown: Global, G_R.
- Countries: all normal countries.
- **On save:** User Type must equal the creator’s own User Type (typically Global). Creating G_R while you are Global will be rejected even if the dropdown showed G_R.

### Regional creator (not Super Admin)

- Form User Types shown: Regional, G_R.
- Country list locked to **their country**.
- Ecclesias: their houses if they are an ecclesia user; otherwise houses for that country.
- **On save:** User Type must be Regional (matching themselves), and country must be their country.

### G_R creator (not Super Admin)

- Treated like the non-Global branch of the form (Regional/G_R options, broad country list in many cases).
- **On save:** User Type must be G_R to match themselves.

---

## Permissions you need (staff side)

| Permission | Purpose |
|------------|---------|
| **Manage Partners** | See All Members |
| **Create Partners** | Open Add Members / Create form |
| **Edit Partners** | Change an existing member later |
| **View Partners** | View member detail |
| **Delete Partners** | Delete (where allowed) |
| **View Member Audit Logs** | See create/update history (Super Admin has this by default; others need it granted) |

Create Partners is **not** the same as **Create Membership** (that one is for plans).

---

## After create — where to check if something looks wrong

1. **All Members** — find the person; confirm status active, country, user type, role.
2. **Membership Management → Members** — if they should have a subscription, confirm plan and expire date.
3. **Audit Logs** — confirm `member_created` and field snapshot.
4. Ask them to check email (including spam) for the welcome message.
5. If they cannot open the panel: check membership expire / exclude, agreement signature, and whether they are on the correct Global vs Regional website for their User Type.

---

## Common mistakes

1. **Using Create Plan instead of Create Member** — creates a product plan, not a person.
2. **Picking G_R on the form when you are Global/Regional** — form shows it; save rejects it unless you are Super Admin or already G_R.
3. **MEMBER_SOVEREIGN without plan and without “exclude”** — validation fails or they hit membership walls.
4. **Ecclesia role without House Of ECCLESIA** — save blocked.
5. **Expecting Signup Rules to apply** — they do not on this form.
6. **Weak or invalid password** — must include `@`, `$`, `%`, or `&` and be 8+ characters with no spaces.
7. **Duplicate email, username, or Lion Roaring ID** — save fails; change the conflicting field.
8. **Assuming Manage Partners includes create** — it does not; need Create Partners.
9. **Admin role unexpectedly becoming G_R** — by design when the role template is marked admin.
10. **Thinking Membership → Members “adds” people** — it only lists subscriptions.

---

## Mobile / API note

Staff apps can create a partner through the API create-partner flow. Behavior mirrors this form (same User Type/country restrictions, LR ID, password rules, membership, welcome email). Audit source is recorded as API rather than PMA. Prefer the PMA form when training new admins so they see every field.

---

## Related documentation

- **All Members** — list, edit, status, export, agreements, audit logs  
- **Membership Management** — plans, subscription list, payments, promos  
- **Membership (Self-Service)** — what the member sees for their own plan  
- **Role Permission** — role templates, admin/ecclesia flags, permission maps  
- **Signup Rules** — public registration only  
- **Global & Regional Domains** — what Global / Regional / G_R mean for login hosts  
- **Super Admin** — creating Super Admin users (different screen)
