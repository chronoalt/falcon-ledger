import { router, usePage } from '@inertiajs/react';
import Navigation from './Navigation.jsx';

export default function Header() {
    const { auth } = usePage().props;

    const handleLogout = () => {
        router.post('/logout');
    };

    return (
        <header className="bg-white shadow">
            <div className="max-w-6xl mx-auto px-4 py-4 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 className="text-2xl font-semibold text-slate-900">Falcon Ledger</h1>
                    <p className="text-sm text-slate-500">Secure Ops Dashboard</p>
                </div>

                {auth?.user ? (
                    <div className="flex flex-col md:flex-row md:items-center md:gap-4">
                        <Navigation />
                        <div className="flex items-center gap-3 text-sm text-slate-600">
                            <span>{auth.user.email}</span>
                            <button
                                onClick={handleLogout}
                                className="inline-flex items-center rounded bg-red-500 px-3 py-1.5 text-white text-xs font-medium hover:bg-red-600"
                                type="button"
                            >
                                Logout
                            </button>
                        </div>
                    </div>
                ) : (
                    <div className="text-sm text-slate-500">Guest</div>
                )}
            </div>
        </header>
    );
}