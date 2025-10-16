import { Head, Link, useForm, usePage } from '@inertiajs/react';

export default function Edit() {
    const { project, asset, target } = usePage().props;

    const form = useForm({
        label: target.label,
        endpoint: target.endpoint,
        description: target.description ?? '',
    });

    const submit = (event) => {
        event.preventDefault();
        form.put(target.links.update);
    };

    return (
        <div className="space-y-6">
            <Head title={`Edit ${target.label}`} />

            <div className="flex items-center justify-between">
                <div>
                    <h2 className="text-3xl font-semibold text-slate-900">Edit Target</h2>
                    <p className="text-sm text-slate-600">
                        Asset: {asset.name} • Project: {project.title}
                    </p>
                </div>
                <Link href={project.links.show} className="text-sm text-slate-600 hover:text-slate-800">
                    ← Back to project
                </Link>
            </div>

            <form onSubmit={submit} className="space-y-5 rounded border border-slate-200 bg-white p-6 shadow-sm">
                <div>
                    <label className="block text-sm font-medium text-slate-700" htmlFor="label">
                        Label
                    </label>
                    <input
                        id="label"
                        type="text"
                        value={form.data.label}
                        onChange={(e) => form.setData('label', e.target.value)}
                        className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                    />
                    {form.errors.label && <p className="mt-1 text-xs text-rose-600">{form.errors.label}</p>}
                </div>

                <div>
                    <label className="block text-sm font-medium text-slate-700" htmlFor="endpoint">
                        Endpoint
                    </label>
                    <input
                        id="endpoint"
                        type="text"
                        value={form.data.endpoint}
                        onChange={(e) => form.setData('endpoint', e.target.value)}
                        className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                    />
                    {form.errors.endpoint && <p className="mt-1 text-xs text-rose-600">{form.errors.endpoint}</p>}
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

                <div className="flex items-center justify-end gap-3">
                    <Link href={project.links.show} className="text-sm text-slate-600 hover:text-slate-800">
                        Cancel
                    </Link>
                    <button
                        type="submit"
                        disabled={form.processing}
                        className="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-blue-400"
                    >
                        {form.processing ? 'Saving…' : 'Update Target'}
                    </button>
                </div>
            </form>
        </div>
    );
}
