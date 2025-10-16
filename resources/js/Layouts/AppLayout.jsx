import { usePage } from '@inertiajs/react';
import Header from "@/Components/Header";


export default function AppLayout({ children }) {
    const { flash } = usePage().props;

    return (
        <div className="min-h-screen bg-slate-100">
            <Header />

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

            <main className="max-w-6xl mx-auto px-4 py-8">{children}</main>
        </div>
    );
}
