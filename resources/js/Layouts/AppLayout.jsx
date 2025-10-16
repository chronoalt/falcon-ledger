import { Link, router, usePage } from '@inertiajs/react';

export default function AppLayout({ children }) {
    const { auth, flash } = usePage().props;

    const handleLogout = () => {
        router.post('/logout');
    };

    return (
        <div className="min-h-screen bg-slate-100">
            <header className="bg-white shadow">
                <div className="max-w-6xl mx-auto px-4 py-4 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 className="text-2xl font-semibold text-slate-900">Falcon Ledger</h1>
                        <p className="text-sm text-slate-500">Secure Ops Dashboard</p>
                    </div>
                    <div className="flex flex-col md:flex-row md:items-center md:gap-4">
                        <nav className="flex gap-3 text-sm">
                            <Link className="text-slate-700 hover:text-slate-900" href="/">Dashboard</Link>
                            <Link className="text-slate-700 hover:text-slate-900" href="/projects">Projects</Link>
                        </nav>
                        <div className="flex items-center gap-3 text-sm text-slate-600">
                            <span>{auth?.user?.email ?? 'Guest'}</span>
                            <button
                                onClick={handleLogout}
                                className="inline-flex items-center rounded bg-red-500 px-3 py-1.5 text-white text-xs font-medium hover:bg-red-600"
                                type="button"
                            >
                                Logout
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            {(flash.success || flash.error) && (
                <div className="max-w-6xl mx-auto px-4 mt-4">
                    <div
                        className={`rounded border px-4 py-3 text-sm ${
                            flash.success
                                ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
                                : 'border-rose-200 bg-rose-50 text-rose-800'
                        }`}
                    >
                        {flash.success ?? flash.error}
                    </div>
                </div>
            )}

            <main className="max-w-6xl mx-auto px-4 py-8">
                {children}
            </main>
        </div>
    );
}
