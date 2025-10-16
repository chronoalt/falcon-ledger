import { Head, Link, useForm, usePage } from '@inertiajs/react';

export default function Edit() {
    const { project, statusOptions } = usePage().props;

    const form = useForm({
        title: project.title,
        description: project.description ?? '',
        due_at: project.due_at ?? '',
        status: project.status,
    });

    const submit = (event) => {
        event.preventDefault();
        form.put(project.links.update);
    };

    return (
        <div className="space-y-6">
            <Head title={`Edit ${project.title}`} />

            <div className="flex items-center justify-between">
                <h2 className="text-3xl font-semibold text-slate-900">Edit Project</h2>
                <Link href="/projects" className="text-sm text-slate-600 hover:text-slate-800">
                    ← Back to projects
                </Link>
            </div>

            <form onSubmit={submit} className="space-y-5 rounded border border-slate-200 bg-white p-6 shadow-sm">
                <div>
                    <label className="block text-sm font-medium text-slate-700" htmlFor="title">
                        Title
                    </label>
                    <input
                        id="title"
                        type="text"
                        value={form.data.title}
                        onChange={(e) => form.setData('title', e.target.value)}
                        className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                    />
                    {form.errors.title && <p className="mt-1 text-xs text-rose-600">{form.errors.title}</p>}
                </div>

                <div>
                    <label className="block text-sm font-medium text-slate-700" htmlFor="description">
                        Description
                    </label>
                    <textarea
                        id="description"
                        value={form.data.description}
                        onChange={(e) => form.setData('description', e.target.value)}
                        className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                        rows="4"
                    />
                    {form.errors.description && <p className="mt-1 text-xs text-rose-600">{form.errors.description}</p>}
                </div>

                <div className="grid gap-4 md:grid-cols-2">
                    <div>
                        <label className="block text-sm font-medium text-slate-700" htmlFor="due_at">
                            Due Date
                        </label>
                        <input
                            id="due_at"
                            type="date"
                            value={form.data.due_at ?? ''}
                            onChange={(e) => form.setData('due_at', e.target.value)}
                            className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                        />
                        {form.errors.due_at && <p className="mt-1 text-xs text-rose-600">{form.errors.due_at}</p>}
                    </div>
                    <div>
                        <label className="block text-sm font-medium text-slate-700" htmlFor="status">
                            Status
                        </label>
                        <select
                            id="status"
                            value={form.data.status}
                            onChange={(e) => form.setData('status', e.target.value)}
                            className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                        >
                            {statusOptions.map((option) => (
                                <option key={option} value={option}>
                                    {option}
                                </option>
                            ))}
                        </select>
                        {form.errors.status && <p className="mt-1 text-xs text-rose-600">{form.errors.status}</p>}
                    </div>
                </div>

                <div className="flex items-center justify-end gap-3">
                    <Link href="/projects" className="text-sm text-slate-600 hover:text-slate-800">
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        disabled={form.processing}
                        className="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-blue-400"
                    >
                        {form.processing ? 'Saving...' : 'Update Project'}
                    </button>
                </div>
            </form>
        </div>
    );
}
