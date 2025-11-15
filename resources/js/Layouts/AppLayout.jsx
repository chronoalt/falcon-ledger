import { usePage } from '@inertiajs/react';
import Header from '@/Components/Header';

export default function AppLayout({ children }) {
    const { flash } = usePage().props;

    return (
        <div className="min-h-screen bg-[#f8f3ea]">
            <Header />

            {(flash?.success || flash?.error) && (
                <div className="mx-auto mt-4 max-w-6xl px-6">
                    <div
                        className={`rounded-lg border px-4 py-3 text-sm ${
                            flash.success
                                ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
                                : 'border-rose-200 bg-rose-50 text-rose-800'
                        }`}
                    >
                        {flash.success ?? flash.error}
                    </div>
                </div>
            )}

            <main className="mx-auto max-w-6xl px-6 py-10">
                {children}
            </main>
        </div>
    );
}
