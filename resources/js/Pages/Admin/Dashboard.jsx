import { Head, Link } from '@inertiajs/react';

export default function Dashboard() {
    return (
        <div className="space-y-6">
            <Head title="Admin Dashboard" />
            <div>
                <h2 className="text-3xl font-semibold text-slate-900">Administrator Dashboard</h2>
                <p className="mt-2 text-slate-600">
                    Manage operational assets, targets, and findings from a single workspace.
                </p>
            </div>

            <div className="rounded border border-slate-200 bg-white p-6 shadow-sm">
                <h3 className="text-xl font-medium text-slate-800">Projects</h3>
                <p className="mt-2 text-sm text-slate-600">
                    Review all engagements, update scope, and track findings by target.
                </p>
                <Link
                    href="/projects"
                    className="mt-4 inline-flex items-center rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    View Projects
                </Link>
            </div>
        </div>
    );
}
