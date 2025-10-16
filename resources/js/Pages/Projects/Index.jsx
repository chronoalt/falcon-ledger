import { Head, Link, router, usePage } from '@inertiajs/react';

export default function Index() {
    const { projects } = usePage().props;

    const handleDelete = (project) => {
        if (confirm(`Delete project "${project.title}"? This cannot be undone.`)) {
            router.delete(project.links.destroy);
        }
    };

    return (
        <div className="space-y-6">
            <Head title="Projects" />

            <div className="flex items-center justify-between">
                <h2 className="text-3xl font-semibold text-slate-900">Projects</h2>
                <Link
                    href="/projects/create"
                    className="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
                >
                    Create Project
                </Link>
            </div>

            <div className="overflow-x-auto rounded border border-slate-200 bg-white shadow-sm">
                <table className="min-w-full divide-y divide-slate-200 text-sm">
                    <thead className="bg-slate-50">
                        <tr>
                            <th className="px-4 py-3 text-left font-medium text-slate-600">Title</th>
                            <th className="px-4 py-3 text-left font-medium text-slate-600">Status</th>
                            <th className="px-4 py-3 text-left font-medium text-slate-600">Due Date</th>
                            <th className="px-4 py-3 text-left font-medium text-slate-600">Assets</th>
                            <th className="px-4 py-3 text-right font-medium text-slate-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-200">
                        {projects.length === 0 && (
                            <tr>
                                <td colSpan="5" className="px-4 py-6 text-center text-slate-500">
                                    No projects found.
                                </td>
                            </tr>
                        )}
                        {projects.map((project) => (
                            <tr key={project.id} className="hover:bg-slate-50/80">
                                <td className="px-4 py-3 font-medium text-slate-800">{project.title}</td>
                                <td className="px-4 py-3 text-slate-600">{project.status}</td>
                                <td className="px-4 py-3 text-slate-600">{project.due_at ?? '—'}</td>
                                <td className="px-4 py-3 text-slate-600">{project.assets_count}</td>
                                <td className="px-4 py-3">
                                    <div className="flex items-center justify-end gap-2">
                                        <Link
                                            href={project.links.show}
                                            className="rounded border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100"
                                        >
                                            View
                                        </Link>
                                        <Link
                                            href={project.links.edit}
                                            className="rounded border border-amber-400 px-3 py-1.5 text-xs font-semibold text-amber-600 hover:bg-amber-50"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            type="button"
                                            onClick={() => handleDelete(project)}
                                            className="rounded border border-rose-400 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50"
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
