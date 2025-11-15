import { Head, useForm } from '@inertiajs/react';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/login');
    };

    return (
        <>
            <Head title="Login" />

            <div className="flex min-h-[60vh] items-center justify-center">
                <div className="relative w-full max-w-3xl overflow-hidden rounded-3xl bg-[#f4562c] px-10 py-10 shadow-2xl">
                    {/* Soft background tint */}
                    <div className="pointer-events-none absolute inset-0 bg-gradient-to-br from-[#ffe0d1] via-transparent to-[#f4562c]/40" />

                    <div className="relative w-full text-white">
                        <h1 className="text-3xl font-bold md:text-4xl">Welcome back</h1>
                        <p className="mt-2 text-sm text-orange-100">
                            Please enter your details to access your ledger.
                        </p>

                        <form onSubmit={handleSubmit} className="mt-8 space-y-5">
                            {/* Global credentials error from backend */}
                            {errors.credentials && (
                                <div className="rounded-md border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                                    {errors.credentials}
                                </div>
                            )}

                            {/* Email */}
                            <div className="space-y-1.5">
                                <label
                                    htmlFor="email"
                                    className="text-sm font-semibold text-orange-50"
                                >
                                    Email address
                                </label>
                                <div className="flex items-center rounded-md bg-[#fbe1d9] px-3 py-2">
                                    <input
                                        id="email"
                                        type="email"
                                        className="w-full bg-transparent text-sm text-slate-900 placeholder:text-slate-500 focus:outline-none"
                                        placeholder="you@example.com"
                                        autoComplete="email"
                                        value={data.email}
                                        onChange={(e) => setData('email', e.target.value)}
                                        required
                                    />
                                </div>
                                {errors.email && (
                                    <p className="text-xs text-red-100">{errors.email}</p>
                                )}
                            </div>

                            {/* Password */}
                            <div className="space-y-1.5">
                                <label
                                    htmlFor="password"
                                    className="text-sm font-semibold text-orange-50"
                                >
                                    Password
                                </label>
                                <div className="flex items-center rounded-md bg-[#fbe1d9] px-3 py-2">
                                    <input
                                        id="password"
                                        type="password"
                                        className="w-full bg-transparent text-sm text-slate-900 placeholder:text-slate-500 focus:outline-none"
                                        placeholder="••••••••"
                                        autoComplete="current-password"
                                        value={data.password}
                                        onChange={(e) => setData('password', e.target.value)}
                                        required
                                    />
                                </div>
                                {errors.password && (
                                    <p className="text-xs text-red-100">{errors.password}</p>
                                )}
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                className="mt-4 inline-flex w-full justify-center rounded-md bg-[#004a98] px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-[#003b77] disabled:opacity-70"
                            >
                                {processing ? 'Signing in…' : 'Login'}
                            </button>
                        </form>

                        <p className="mt-4 text-xs text-orange-100">
                            Don&apos;t have an account? Contact your administrator to be added to
                            Falcon Ledger.
                        </p>
                    </div>
                </div>
            </div>
        </>
    );
}
