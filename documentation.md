### Models

#### Users
| name | types | descriptions |
| :--- | :--- | :--- |
| id | serial | PK |
| name | varchar(255) | - |
| email | varchar(255) | Unique |
| password | char(32) | Hash : SHA(256) + salt |

#### /login
##### parameters
- email
- password

#### /logout
##### parameters

---

### Frontend (Inertia + React)

- **Stack**: Inertia.js bridges Laravel controllers to React pages (see `resources/js/Pages/**`). Vite handles asset builds; use `npm run dev` for HMR or `npm run build` for production bundles.
- **Entry point**: `resources/js/app.jsx` mounts via the shared `AppLayout` (`resources/js/Layouts/AppLayout.jsx`). Controllers return Inertia responses (`Inertia::render(...)`) instead of Blade views.
- **State**: Page-level forms use Inertia’s `useForm` helper for validation and submission. Flash data and authenticated user info are shared via `HandleInertiaRequests` middleware.

### Database Models

#### Projects (`projects`)
- `id` (UUID, PK)
- `title` (string, 255)
- `description` (string, nullable)
- `status` (string, default `Active`)
- `created_by` (UUID FK → `users.id`, cascade delete)
- `due_at` (timestamp, nullable)
- `created_at` / `updated_at`
- Relationships: has many `assets`; via assets → targets → findings.

#### Assets (`assets`)
- `id` (UUID, PK)
- `project_id` (UUID FK → `projects.id`, cascade delete)
- `name` (string, 255)
- `detail` (text, nullable)
- `address` (string, nullable)
- `created_at` / `updated_at`
- Relationships: belongs to `project`; has many `targets`.

#### Targets (`targets`)
- `id` (UUID, PK)
- `asset_id` (UUID FK → `assets.id`, cascade delete)
- `label` (string, 255)
- `endpoint` (string, 255)
- `description` (text, nullable)
- `created_at` / `updated_at`
- Relationships: belongs to `asset`; has many `findings`.

#### Findings (`findings`)
- `id` (UUID, PK)
- `target_id` (UUID FK → `targets.id`, cascade delete)
- `cvss_vector_id` (UUID FK → `cvss_vectors.id`, cascade delete)
- `title` (string, 255)
- `status` (string, default `open`, enum enforced in UI)
- `description` (text)
- `recommendation` (text, nullable)
- `created_at` / `updated_at`
- Relationships: belongs to `target`; belongs to `cvss_vector`; has many `finding_attachments`.

#### CVSS Vectors (`cvss_vectors`)
- `id` (UUID, PK)
- `vector_string` (string, unique)
- Metric columns: `attack_vector`, `attack_complexity`, `privileges_required`, `user_interaction`, `scope`, `confidentiality_impact`, `integrity_impact`, `availability_impact`
- `base_score` (decimal 4,1)
- `base_severity` (string)
- `created_at` / `updated_at`
- Pre-seeded with every CVSS v3.1 base combination for constant-time lookups.

#### Finding Attachments (`finding_attachments`)
- `id` (UUID, PK)
- `finding_id` (UUID FK → `findings.id`, cascade delete)
- `disk` (string, default `local`)
- `path` (string)
- `original_name` (string)
- `mime_type` (string, nullable)
- `size` (unsigned integer bytes, nullable)
- `created_at` / `updated_at`
- Files stored under `storage/app/findings/{finding_id}/`; upload restricted to ≤3 files, each ≤10 MB, with extension/MIME allowlists.

### Domain Relationships

- Projects → Assets (1:N)
- Assets → Targets (1:N)
- Targets → Findings (1:N)
- Findings reference precomputed CVSS vectors via `cvss_vector_id`. Attachments (max 3, 10 MB each) are stored under `storage/app/findings/{finding}` with MIME/extension allowlists.

### Key Commands

- `composer run dev` – starts Laravel (`php artisan serve`), queue listener, log tail, and Vite concurrently.
- `npm run build` – produces production assets in `public/build`.
- `php artisan migrate --seed` – provisions tables and seeds CVSS vectors, roles, and demo users.
