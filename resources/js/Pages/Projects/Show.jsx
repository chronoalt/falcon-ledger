import { Head, Link, router, useForm, usePage } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';

function TargetCreator({ asset, onCancel }) {
    const form = useForm({
        label: '',
        endpoint: '',
        description: '',
    });

    const submit = (event) => {
        event.preventDefault();
        form.post(asset.links.storeTarget, {
            onSuccess: () => {
                form.reset();
                onCancel?.();
            },
        });
    };

    return (
        <form onSubmit={submit} className="space-y-3 rounded border border-slate-200 bg-slate-50 p-4">
            <h4 className="text-sm font-semibold text-slate-700">Add Target</h4>
            <div>
                <label className="block text-xs font-medium text-slate-600" htmlFor={`label-${asset.id}`}>
                    Label
                </label>
                <input
                    id={`label-${asset.id}`}
                    type="text"
                    value={form.data.label}
                    onChange={(e) => form.setData('label', e.target.value)}
                    className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                />
                {form.errors.label && <p className="mt-1 text-xs text-rose-600">{form.errors.label}</p>}
            </div>

            <div>
                <label className="block text-xs font-medium text-slate-600" htmlFor={`endpoint-${asset.id}`}>
                    Endpoint
                </label>
                <input
                    id={`endpoint-${asset.id}`}
                    type="text"
                    value={form.data.endpoint}
                    onChange={(e) => form.setData('endpoint', e.target.value)}
                    className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                />
                {form.errors.endpoint && <p className="mt-1 text-xs text-rose-600">{form.errors.endpoint}</p>}
            </div>

            <div>
                <label className="block text-xs font-medium text-slate-600" htmlFor={`description-${asset.id}`}>
                    Description
                </label>
                <textarea
                    id={`description-${asset.id}`}
                    rows="3"
                    value={form.data.description}
                    onChange={(e) => form.setData('description', e.target.value)}
                    className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                />
                {form.errors.description && <p className="mt-1 text-xs text-rose-600">{form.errors.description}</p>}
            </div>

            <div className="flex justify-end gap-2">
                <button
                    type="button"
                    onClick={() => {
                        form.reset();
                        onCancel?.();
                    }}
                    className="rounded border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100"
                >
                    Cancel
                </button>
                <button
                    type="submit"
                    disabled={form.processing}
                    className="rounded bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-blue-400"
                >
                    {form.processing ? 'Saving…' : 'Create Target'}
                </button>
            </div>
        </form>
    );
}

export default function Show() {
    const { project, assets } = usePage().props;

    const assetForm = useForm({
        name: '',
        address: '',
        detail: '',
    });

    const submitAsset = (event) => {
        event.preventDefault();
        assetForm.post(project.links.storeAsset, {
            onSuccess: () => assetForm.reset(),
        });
    };

    const deleteAsset = (asset) => {
        if (confirm(`Delete asset "${asset.name}"? This will remove all associated targets and findings.`)) {
            router.delete(asset.links.destroy);
        }
    };

    const deleteTarget = (asset, target) => {
        if (confirm(`Delete target "${target.label}" and all of its findings?`)) {
            router.delete(target.links.destroy);
        }
    };

    const [openPanels, setOpenPanels] = useState({});
    const [showTargetForms, setShowTargetForms] = useState({});

    const assetIds = useMemo(() => assets.map((asset) => asset.id), [assets]);

    useEffect(() => {
        setOpenPanels((prev) => {
            const next = {};
            assetIds.forEach((id) => {
                next[id] = prev[id] ?? false;
            });
            return next;
        });

        setShowTargetForms((prev) => {
            const next = {};
            assetIds.forEach((id) => {
                next[id] = prev[id] ?? false;
            });
            return next;
        });
    }, [assetIds]);

    const togglePanel = (assetId) => {
        setOpenPanels((prev) => ({ ...prev, [assetId]: !(prev[assetId] ?? false) }));
    };

    const handleAddTargetClick = (assetId) => {
        setOpenPanels((prev) => ({ ...prev, [assetId]: true }));
        setShowTargetForms((prev) => ({ ...prev, [assetId]: true }));
    };

    const handleCancelTarget = (assetId) => {
        setShowTargetForms((prev) => ({ ...prev, [assetId]: false }));
    };

    return (
        <div className="space-y-8">
            <Head title={project.title} />

            <div className="flex flex-col gap-2">
                <h2 className="text-3xl font-semibold text-slate-900">{project.title}</h2>
                <p className="text-slate-600">{project.description ?? 'No description provided.'}</p>
                <div className="text-sm text-slate-500">
                    <span className="mr-4">
                        <strong>Status:</strong> {project.status}
                    </span>
                    <span>
                        <strong>Due:</strong> {project.due_at ?? '—'}
                    </span>
                </div>
            </div>

            <section className="space-y-4">
                <h3 className="text-xl font-semibold text-slate-800">Add Asset</h3>
                <form
                    onSubmit={submitAsset}
                    className="grid gap-4 rounded border border-slate-200 bg-white p-6 shadow-sm md:grid-cols-3"
                >
                    <div className="md:col-span-1">
                        <label className="block text-sm font-medium text-slate-700" htmlFor="asset-name">
                            Name
                        </label>
                        <input
                            id="asset-name"
                            type="text"
                            value={assetForm.data.name}
                            onChange={(e) => assetForm.setData('name', e.target.value)}
                            className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                        />
                        {assetForm.errors.name && <p className="mt-1 text-xs text-rose-600">{assetForm.errors.name}</p>}
                    </div>
                    <div className="md:col-span-1">
                        <label className="block text-sm font-medium text-slate-700" htmlFor="asset-address">
                            Scope / Address
                        </label>
                        <input
                            id="asset-address"
                            type="text"
                            value={assetForm.data.address}
                            onChange={(e) => assetForm.setData('address', e.target.value)}
                            className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                        />
                        {assetForm.errors.address && <p className="mt-1 text-xs text-rose-600">{assetForm.errors.address}</p>}
                    </div>
                    <div className="md:col-span-3">
                        <label className="block text-sm font-medium text-slate-700" htmlFor="asset-detail">
                            Description
                        </label>
                        <textarea
                            id="asset-detail"
                            rows="3"
                            value={assetForm.data.detail}
                            onChange={(e) => assetForm.setData('detail', e.target.value)}
                            className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                        />
                        {assetForm.errors.detail && <p className="mt-1 text-xs text-rose-600">{assetForm.errors.detail}</p>}
                    </div>
                    <div className="md:col-span-3 flex justify-end">
                        <button
                            type="submit"
                            disabled={assetForm.processing}
                            className="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-blue-400"
                        >
                            {assetForm.processing ? 'Saving…' : 'Create Asset'}
                        </button>
                    </div>
                </form>
            </section>

                <section className="space-y-6">
                <h3 className="text-xl font-semibold text-slate-800">Assets &amp; Targets</h3>

                {assets.length === 0 && <p className="text-slate-500">No assets registered yet.</p>}

                {assets.map((asset) => {
                    const isOpen = openPanels[asset.id] ?? false;
                    const showForm = showTargetForms[asset.id] ?? false;

                    return (
                        <div key={asset.id} className="rounded border border-slate-200 bg-white shadow-sm">
                            <div className="flex items-start justify-between px-6 py-4">
                                <div className="flex items-center gap-3">
                                    <button
                                        type="button"
                                        aria-expanded={isOpen}
                                        onClick={() => togglePanel(asset.id)}
                                        className={`flex h-8 w-8 items-center justify-center rounded-full border text-sm font-semibold transition ${
                                            isOpen
                                                ? 'border-blue-500 bg-blue-100 text-blue-600'
                                                : 'border-slate-300 bg-white text-slate-500'
                                        }`}
                                    >
                                        {isOpen ? '–' : '+'}
                                    </button>
                                    <div>
                                        <h4 className="text-lg font-semibold text-slate-800">{asset.name}</h4>
                                        {asset.address && (
                                            <p className="text-sm text-slate-600">
                                                <strong>Scope:</strong> {asset.address}
                                            </p>
                                        )}
                                        {asset.detail && <p className="mt-1 text-sm text-slate-600">{asset.detail}</p>}
                                    </div>
                                </div>
                                <div className="flex gap-2">
                                    <Link
                                        href={asset.links.edit}
                                        className="rounded border border-amber-400 px-3 py-1.5 text-xs font-semibold text-amber-600 hover:bg-amber-50"
                                    >
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        onClick={() => deleteAsset(asset)}
                                        className="rounded border border-rose-400 px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50"
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>

                            {isOpen && (
                                <div className="space-y-4 border-t border-slate-200 px-6 py-4">
                                    <div className="grid gap-4 md:grid-cols-2">
                                        {asset.targets.length === 0 ? (
                                            <p className="text-sm text-slate-500 md:col-span-2">
                                                No targets defined for this asset.
                                            </p>
                                        ) : (
                                            asset.targets.map((target) => (
                                                <div
                                                    key={target.id}
                                                    className="space-y-3 rounded border border-slate-200 bg-slate-50 p-4"
                                                >
                                                    <div className="flex items-start justify-between gap-3">
                                                        <div>
                                                            <h6 className="text-base font-semibold text-slate-800">{target.label}</h6>
                                                            <p className="text-sm text-slate-600">{target.endpoint}</p>
                                                            {target.description && (
                                                                <p className="mt-2 text-sm text-slate-600">{target.description}</p>
                                                            )}
                                                        </div>
                                                        <div className="flex flex-col gap-1">
                                                            <Link
                                                                href={target.links.edit}
                                                                className="rounded border border-amber-400 px-3 py-1 text-xs font-semibold text-amber-600 hover:bg-amber-50"
                                                            >
                                                                Edit
                                                            </Link>
                                                            <button
                                                                type="button"
                                                                onClick={() => deleteTarget(asset, target)}
                                                                className="rounded border border-rose-400 px-3 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50"
                                                            >
                                                                Delete
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div className="flex flex-wrap items-center gap-2 text-xs text-slate-600">
                                                        <span className="rounded bg-slate-200 px-2 py-1">
                                                            {target.findings_count} findings
                                                        </span>
                                                    </div>

                                                    <div className="flex flex-wrap gap-2 text-sm">
                                                        <Link
                                                            href={target.links.view}
                                                            className="rounded bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700"
                                                        >
                                                            View Findings
                                                        </Link>
                                                        <Link
                                                            href={target.links.createFinding}
                                                            className="rounded border border-blue-500 px-3 py-1.5 text-xs font-semibold text-blue-600 hover:bg-blue-50"
                                                        >
                                                            Add Finding
                                                        </Link>
                                                    </div>
                                                </div>
                                            ))
                                        )}
                                    </div>

                                    <div className="space-y-3">
                                        {!showForm && (
                                            <button
                                                type="button"
                                                onClick={() => handleAddTargetClick(asset.id)}
                                                className="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700"
                                            >
                                                <span className="inline-flex h-7 w-7 items-center justify-center rounded-full border border-blue-400 bg-blue-50 text-blue-600">
                                                    +
                                                </span>
                                                Add Target
                                            </button>
                                        )}

                                        {showForm && (
                                            <TargetCreator
                                                asset={asset}
                                                onCancel={() => handleCancelTarget(asset.id)}
                                            />
                                        )}
                                    </div>
                                </div>
                            )}
                        </div>
                    );
                })}
            </section>
        </div>
    );
}
