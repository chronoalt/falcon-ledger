import { Link, router, usePage } from '@inertiajs/react';

export default function Header() {
    const { auth } = usePage().props;
    const user = auth?.user;
    const isAdmin = user?.is_admin;

    const handleLogout = (event) => {
        event.preventDefault();
        router.post('/logout', {
            onFinish: () => {
                // After the logout request is finished, force a full page reload
                window.location.href = '/login';
            }
        });
    };

    return (
        <header className="bg-[#f4562c] text-white">
            <div className="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
                {/* Logo + brand */}
                <div className="flex items-center gap-3">
                    <img
                        src="/images/falcon-logo.png"
                        alt="Falcon Ledger logo"
                        className="h-9 w-auto"
                    />
                    <span className="text-xl font-semibold tracking-tight">
                        Falcon Ledger
                    </span>
                </div>

                {/* Navigation */}
                <nav className="flex items-center gap-6 text-sm font-medium">
                    {user ? (
                        <>
                            <Link
                                href="/projects"
                                className="transition hover:opacity-80"
                            >
                                Projects
                            </Link>
                            {isAdmin && (
                                <Link
                                    href="/admin/users"
                                    className="transition hover:opacity-80"
                                >
                                    Users
                                </Link>
                            )}

                            <form onSubmit={handleLogout}>
                                <button
                                    type="submit"
                                    className="rounded-full bg-[#004a98] px-4 py-1.5 text-sm font-semibold shadow hover:bg-[#003b77]"
                                >
                                    Logout
                                </button>
                            </form>
                        </>
                    ) : (
                        <>
                            <Link
                                href="/login"
                                className="rounded-full bg-[#004a98] px-4 py-1.5 text-sm font-semibold shadow hover:bg-[#003b77]"
                            >
                                Log In
                            </Link>
                        </>
                    )}
                </nav>
            </div>
        </header>
    );
}
