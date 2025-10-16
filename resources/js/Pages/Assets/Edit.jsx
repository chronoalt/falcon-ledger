import { Head, Link, useForm, usePage } from '@inertiajs/react';

export default function Edit() {
    const { project, asset } = usePage().props;

    const form = useForm({
        name: asset.name,
        address: asset.address ?? '',
        detail: asset.detail ?? '',
    });

    const submit = (event) => {
        event.preventDefault();
        form.put(asset.links.update);
    };

    return (
        <div className="space-y-6">
            <Head title={`Edit ${asset.name}`} />

            <div className="flex items-center justify-between">
                <div>
                    <h2 className="text-3xl font-semibold text-slate-900">Edit Asset</h2>
                    <p className="text-sm text-slate-600">Project: {project.title}</p>
                </div>
                <Link href={project.links.show} className="text-sm text-slate-600 hover:text-slate-800">
                    ← Back to project
                </Link>
            </div>

            <form onSubmit={submit} className="space-y-5 rounded border border-slate-200 bg-white p-6 shadow-sm">
                <div>
                    <label className="block text-sm font-medium text-slate-700" htmlFor="name">
                        Name
                    </label>
                    <input
                        id="name"
                        type="text"
                        value={form.data.name}
                        onChange={(e) => form.setData('name', e.target.value)}
                        className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                    />
                    {form.errors.name && <p className="mt-1 text-xs text-rose-600">{form.errors.name}</p>}
                </div>

                <div>
                    <label className="block text-sm font-medium text-slate-700" htmlFor="address">
                        Scope / Address
                    </label>
                    <input
                        id="address"
                        type="text"
                        value={form.data.address}
                        onChange={(e) => form.setData('address', e.target.value)}
                        className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                    />
                    {form.errors.address && <p className="mt-1 text-xs text-rose-600">{form.errors.address}</p>}
                </div>

                <div>
                    <label className="block text-sm font-medium text-slate-700" htmlFor="detail">
                        Description
                    </label>
                    <textarea
                        id="detail"
                        value={form.data.detail}
                        onChange={(e) => form.setData('detail', e.target.value)}
                        className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                        rows="4"
                    />
                    {form.errors.detail && <p className="mt-1 text-xs text-rose-600">{form.errors.detail}</p>}
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
                        {form.processing ? 'Saving…' : 'Update Asset'}
                    </button>
                </div>
            </form>
        </div>
    );
}
