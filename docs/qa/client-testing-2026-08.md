# Client Development Testing — 8/10/26 to 8/16/26

Source: `DemoTesting Sent.pdf` (client-supplied). Item numbers below match the PDF exactly.
Screenshots are referenced by PDF page — the `assets/imageN.png` files from the earlier
conversion do not exist and those links were dropped.

Legend: ✅ Passed · ❌ Failed · ❓ Question / needs clarification · ⚠️ OK (with note)

> Note: the PDF title says 8/10–8/15, but ORG items 10–13 are dated 8/16. Range corrected above.

---

## Action items (Failed / Question)

### Member-scope filter — one root cause, four symptoms
The `.us` / `.org` member-scoping rule is implemented inconsistently across features.
**All Members passes in both apps** (US #13, ORG #7), so the correct query already exists —
these four call sites disagree with it:

- [x] **US #13a — Export Report (`.us`)**
  - `ssubowo27@gmail.com` (.us): `user_type = G_R` rows missing; total member count ≠ report count.
  - Super Admin (.us): report lists only 5 members, and includes Global members that must not appear on `.us`.
  - Super Admin (.org): **report is correct** — use this as the reference implementation.
- [x] **ORG #8 — Export Report (`.org`, G_R login)** — includes Regional users; must be Global & G_R only.
- [ ] **ORG #9 / US #23 — Chat member list** — "Admin Admin" appears in Chat but not in Team & Mail. (PDF p.9)
- [ ] **ORG #13 — Private Collaboration invite list** — far fewer members than All Members. (PDF p.10)

Client also requests: **add Member Tier level as a column** to the member export report.

### Tier / user_type option rules
- [x] **ORG #6 — Add Member (Global)**: User Type dropdown offers "Regional & G_R"; it must offer **Global**. `G_R` should be derived from the Regional country context, not picked here. (PDF p.8)
- [ ] **ORG #10 — Global member Renew Membership**: Tier 1 must not be offered. Default Tier 2, only alternative Tier 3. (PDF p.9)
- [x] **US #19 — Ecclesia Tier 2 member adding a Tier 1 member** with `user_type = Regional` throws *"You are not authorized to create partners of this type."* (see open question 4)

### Sign-up form
- [ ] **US #21 — Field label reads "Church"; must read "Ecclesia"** (matches production). This is a label rename, not a data problem. (PDF p.6)
- [ ] **ORG #11 — Ecclesia selection dropdown is empty**; must be populated and selectable. Optional is acceptable, but an empty value currently renders as **"NO NAME"** in All Members. (PDF p.10)

### Permissions model
- [x] **US #17 — Support Reports & Change Logs** privileges are attached to the role, not to the Member Tier privilege, unlike every other menu. Client preference: move them to a **Member Tier privilege**. Alternative accepted: drop them from the role if they should always be on. (PDF p.4)

### Ecclesia membership
- [x] **US #18 — Super Admin promotes Tier 2 member to an Ecclesia role.** For `ssubowo27@gmail.com` (ECC access = "Grace International"), a member of Grace International should have house of ecclesia = Grace International, with House of Ecclesia **access** to Grace International *and* other ecclesia (e.g. Lion Roaring PMA). Observed: `irenesubowo@gmail` belongs to "Lion Roaring PMA", not Grace International. (PDF p.4–5)

### Questions (need client answer before coding)
- [ ] **US #20 — New member free membership.** Is the "1 Month Special 2025" promo code applied in the logic? Round the Remaining days up to whole days (currently shows `30.967926331736 days`). What is the fallback rule when no promo code exists — 1 day? (PDF p.5)
- [ ] **US #23 — Super Admin account (`ssubowo@proton.me`).** What is the case where a super admin has `user_type = G_R`? Why is "Admin Admin" visible in Chat but not in Team & Mail? And why does `ssubowo@proton.me` itself not appear in messages at all? (PDF p.6–7)

### Marked OK but likely still defects
- **US #22 / ORG #1 — "No role called CHURCH".** Both marked OK, yet both screenshots still show a live `CHURCH` role (`ssubowo27@gmail.com`). Confirm whether CHURCH should be migrated/renamed to Ecclesia. (PDF p.6, p.7)

---

## Business rules (as stated by the tester)

- **All Members on `.us`** — `user_type IN (G_R, Regional)` AND `country = US`
- **All Members on `.org`** — `user_type IN (Global, G_R)` AND `country = *` (any country)
- **Private Collaboration invite list** — `user_type IN (Global, G_R)`
- **E-Store setup & Warehouse Store setup access** — Member Tier 2 **+** Manage WH privilege **+** role `ESTORE_MGR`
- **Adding a `G_R` member is a two-step process** — (1) assign role `MEMBER_SOVEREIGN` and select Tier 2, (2) assign Ecclesia role and select the ecclesia house
- **A member added by an Ecclesia member must log in to complete the Registration Agreement**
- Request: add **Member Tier level** as a column in the member export report

---

## Section 1 — Lionroaring.us

| # | Item | Result | Date |
|---|------|--------|------|
| 1 | User asked Admin to change their email | ✅ Passed — **no data lost** | 8/10/26 |
| 1 | Send / receive email — check notification | ✅ Passed | 8/10/26 |
| 2 | Membership expired & user had assigned role; renews on the same tier — prior assigned role did not drop | ✅ Passed | 8/10/26 |
| 3 | Lionroaring.org — Regional user denied login | ✅ Passed | 8/10/26 |
| 4 | **Education — Topic & Files.** Tier 1: no access to menu. Tier 2: View, Delete, Add | ✅ Passed | 8/10/26 |
| 5 | **Education — Becoming Sovereign / Becoming Christ Like / Becoming Leader and Innovator.** Tier 1: View & Download, no Upload/Edit/Delete. Tier 2: Upload, Edit, Delete + View & Download | ✅ Passed | 8/11/26 |
| 6 | **Files.** Tier 1: View & Download. Tier 2: Re-upload existing file (edit), view, download, upload | ✅ Passed | 8/11/26 |
| 6 | **Bulletin — Create Bulletins.** Tier 1 & 2: create and delete own bulletin, view others' | ✅ Passed | 8/11/26 |
| 7 | **Bulletin — Job Posting.** Tier 1 & 2: create and delete own posting, view others' | ✅ Passed | 8/11/26 |
| 8 | **Bulletin — Meeting Schedule.** Tier 1: view & join meeting (e.g. Zoom link). Tier 2: view, create, edit, delete own and others' meetings | ✅ Passed | 8/11/26 |
| 9 | **Live Events.** Tier 1: view & register for created event. Tier 2: same, plus add/edit/delete own created event | ✅ Passed | 8/11/26 |
| 10 | **Private Collaboration.** Tier 1 & 2: view, join, accept/reject invite. Tier 2 also: create collaboration & send meeting info to other PMA members. Collaboration deletable only by its creator | ✅ Passed | 8/11/26 |
| 11 | **E-Store (Store & WH setup).** Tier 1 & 2: no privilege by default. Access limited to Tier 2 + Manage WH privilege + `ESTORE_MGR`. EStore: Dashboard/CMS, Categories, Sizes, Colors, Tier 2-only promo codes, Settings (shipping rules, CC fee), Order Status flow, Email Templates, Product create/delete. Warehouse Store: Warehouses, Warehouse Products, Warehouse Orders | ✅ Passed | 8/11/26 |
| 12 | **E-Learning (setting).** Tier 1: no privilege. Tier 2: Dashboard/CMS, Categories, Sub-Categories, Topics, Product (create/view/delete) | ✅ Passed | 8/11/26 |
| 13 | **All Members.** Tier 1 & 2: view members residing in the US | ✅ Passed | 8/11/26 |
| 13a | **All Members — Export Report** — see action items | ❌ **Failed** | 8/11/26 |
| 14 | **Strategy.** Tier 1: View & Download. Tier 2: View, Delete, Download & Upload. Member can delete only their own document | ✅ Passed | 8/11/26 |
| 15 | **Policy and Guidance.** Tier 1: View & Download. Tier 2: View, Download & Upload. Note in source says member can delete only their own document (see open question 2) | ✅ Passed | 8/11/26 |
| 16 | **Member Tiers.** Tier 1 & 2: renew plan; cancel, upgrade, or downgrade plan | ✅ Passed | 8/11/26 |
| 17 | **Support Reports & Change Logs** — privilege not tied to Member Tier privilege (PDF p.4) | ❌ **Failed** | 8/11/26 |
| 18 | **Super Admin promotes a Tier 2 Member to an Ecclesia role** (PDF p.4–5) | ❌ **Failed** | 8/11/26 |
| 19 | **Member Tier 2 with Ecclesia Role.** Add Member Tier 1 (`user_type = Regional`) → error. Add Member Tier 2 (`user_type = G_R`) → passed. Edit & Drop Member → passed | ❌ **Failed** | 8/11/26 |
| 20 | **New member created in #19 completes the registration agreement** — receives temp password email; auto-gets 1 month free membership (PDF p.5) | ❓ **Question** | 8/11/26 |
| 21 | **New member sign-up** — "Church" label incorrect, should be Ecclesia (PDF p.6) | ❌ **Failed** | 8/14/26 |
| 22 | **No role called "CHURCH"** — marked OK, but screenshot still shows a CHURCH role (PDF p.6) | ⚠️ OK | 8/14/26 |
| 23 | **Super Admin Account** (`ssubowo@proton.me`) (PDF p.6–7) | ❓ **Question** | 8/14/26 |

---

## Section 2 — Lionroaring.org

| # | Item | Result | Date |
|---|------|--------|------|
| 1 | Role named CHURCH no longer exists; see production — Ecclesia. Screenshot still shows CHURCH (PDF p.7) | ⚠️ OK | 8/14/26 |
| 1 | Warehouse manager member (`lrvawarehouse`) — `user_type` must be `G_R` to handle lionroaring.org | ✅ Passed | 8/12/26 |
| 2 | Create group team with a Global member NOT in the US — group must not appear when logged into `.us` | ⚠️ OK | 8/12/26 |
| 3 | Create group team with a `G_R` member who exists in the US — group exists in both Global and US | ✅ Passed | 8/12/26 |
| 4 | Chat & Mail within `G_R` member — exists in both Global and US | ✅ Passed | 8/12/26 |
| 5 | Bulletins — all functionality checked, no issues | ✅ Passed | 8/12/26 |
| 6 | **Add Member in Global — incorrect user_type selection** (PDF p.8) | ❌ **Failed** | 8/15/26 |
| 7 | Login as Member with `G_R` — View Members lists correct members (Global & G_R) | ✅ Passed | 8/15/26 |
| 8 | **Login as Member with `G_R` — Export Report listing incorrect** (includes Regional users) | ❌ **Failed** | 8/15/26 |
| 9 | **Login as Member with `G_R` — chat member list incorrect** (PDF p.9) | ❌ **Failed** | 8/15/26 |
| 10 | **Global Member — Renew Membership** — Tier 1 offered (PDF p.9) | ❌ **Failed** | 8/16/26 |
| 11 | **New member sign-up — Ecclesia selection empty** (PDF p.10) | ❌ **Failed** | 8/16/26 |
| 12 | New member sign-up — default Tier Member is 2 and 3 | ✅ Passed | 8/16/26 |
| 13 | **Bulletin — Private Collaboration — invite user list too short** (PDF p.10) | ❌ **Failed** | 8/16/26 |

---

## Open questions for the client

1. **CHURCH role** (US #22, ORG #1) — marked OK but still present in both screenshots. Migrate/rename to Ecclesia, or leave as-is?
2. **Policy and Guidance (US #15)** — Tier 2 privilege list says "View, Download & Upload" with no Delete, but the accompanying note grants delete-own. Which is correct? (The note also says "Strategy document", copied from #14.)
3. **Free membership (US #20)** — is "1 Month Special 2025" the intended default promo? Round remaining days up to whole days? What is the fallback when no promo code exists?
4. **Ecclesia member creating Regional/Tier 1 (US #19)** — is that supposed to be permitted? The current error may be correct authorization behaviour. Need the intended rule, not just the error.
5. **G_R derivation (ORG #6)** — "G_R should come from the Regional country" needs a concrete rule: who sets `G_R`, at which step, based on what input?
6. **Super admin with `user_type = G_R` (US #23)** — what is this configuration meant to represent?
