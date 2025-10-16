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

### Domain Relationships

- Projects → Assets (1:N)
- Assets → Targets (1:N)
- Targets → Findings (1:N)
- Findings reference precomputed CVSS vectors via `cvss_vector_id`. Attachments (max 3, 10 MB each) are stored under `storage/app/findings/{finding}` with MIME/extension allowlists.

### Key Commands

- `composer run dev` – starts Laravel (`php artisan serve`), queue listener, log tail, and Vite concurrently.
- `npm run build` – produces production assets in `public/build`.
- `php artisan migrate --seed` – provisions tables and seeds CVSS vectors, roles, and demo users.
