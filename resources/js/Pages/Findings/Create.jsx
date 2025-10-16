import { Head, Link, useForm, usePage } from '@inertiajs/react';

const defaultCvssSelections = (options, fallback) => {
    const keys = Object.keys(options);
    if (keys.length === 0) {
        return fallback;
    }
    return keys[0];
};

export default function Create() {
    const {
        project,
        assets,
        statuses,
        attackVectors,
        attackComplexities,
        privilegesRequired,
        userInteractions,
        scopeOptions,
        impactMetrics,
        preselectedTargetId,
        attachmentLimit,
        attachmentMaxSizeMb,
        allowedExtensions,
    } = usePage().props;

    const backLink = project.links.back;

    const flatTargets = assets.flatMap((asset) => asset.targets);
    const defaultTargetId = preselectedTargetId ?? (flatTargets[0]?.id ?? '');

    const form = useForm({
        title: '',
        status: statuses[0] ?? 'open',
        target_id: defaultTargetId,
        attack_vector: defaultCvssSelections(attackVectors, 'network'),
        attack_complexity: defaultCvssSelections(attackComplexities, 'low'),
        privileges_required: defaultCvssSelections(privilegesRequired, 'none'),
        user_interaction: defaultCvssSelections(userInteractions, 'none'),
        scope: defaultCvssSelections(scopeOptions, 'unchanged'),
        confidentiality_impact: defaultCvssSelections(impactMetrics, 'high'),
        integrity_impact: defaultCvssSelections(impactMetrics, 'high'),
        availability_impact: defaultCvssSelections(impactMetrics, 'high'),
        description: '',
        recommendation: '',
        attachments: [],
    });

    const targetsAvailable = flatTargets;

    const submit = (event) => {
        event.preventDefault();
        form.post(project.links.storeFinding, {
            forceFormData: true,
        });
    };

    return (
        <div className="space-y-6">
            <Head title="Add Finding" />

            <div className="flex items-center justify-between">
                <div>
                    <h2 className="text-3xl font-semibold text-slate-900">Add Finding</h2>
                    <p className="text-sm text-slate-600">Project: {project.title}</p>
                </div>
                <Link href={backLink} className="text-sm text-slate-600 hover:text-slate-800">
                    ← Back
                </Link>
            </div>

            {targetsAvailable.length === 0 ? (
                <div className="rounded border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    No targets are available for this project yet. Create an asset and target before adding findings.
                </div>
            ) : (
                <form onSubmit={submit} className="space-y-5 rounded border border-slate-200 bg-white p-6 shadow-sm">
                    <div className="grid gap-4 md:grid-cols-2">
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
                            <label className="block text-sm font-medium text-slate-700" htmlFor="status">
                                Status
                            </label>
                            <select
                                id="status"
                                value={form.data.status}
                                onChange={(e) => form.setData('status', e.target.value)}
                                className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                            >
                                {statuses.map((status) => (
                                    <option key={status} value={status}>
                                        {status.replace('_', ' ')}
                                    </option>
                                ))}
                            </select>
                            {form.errors.status && <p className="mt-1 text-xs text-rose-600">{form.errors.status}</p>}
                        </div>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-700" htmlFor="target_id">
                            Target
                        </label>
                        <select
                            id="target_id"
                            value={form.data.target_id}
                            onChange={(e) => form.setData('target_id', e.target.value)}
                            className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                            required
                        >
                            <option value="">Select target…</option>
                            {assets.map((asset) => (
                                <optgroup key={asset.id} label={`${asset.name}${asset.address ? ` (${asset.address})` : ''}`}>
                                    {asset.targets.map((target) => (
                                        <option key={target.id} value={target.id}>
                                            {target.label} — {target.endpoint}
                                        </option>
                                    ))}
                                </optgroup>
                            ))}
                        </select>
                        {form.errors.target_id && <p className="mt-1 text-xs text-rose-600">{form.errors.target_id}</p>}
                    </div>

                    <fieldset className="grid gap-4 rounded border border-slate-200 p-4 md:grid-cols-2">
                        <legend className="px-2 text-sm font-semibold text-slate-700">CVSS v3.1 Metrics</legend>

                        {[
                            ['attack_vector', 'Attack Vector', attackVectors],
                            ['attack_complexity', 'Attack Complexity', attackComplexities],
                            ['privileges_required', 'Privileges Required', privilegesRequired],
                            ['user_interaction', 'User Interaction', userInteractions],
                            ['scope', 'Scope', scopeOptions],
                            ['confidentiality_impact', 'Confidentiality Impact', impactMetrics],
                            ['integrity_impact', 'Integrity Impact', impactMetrics],
                            ['availability_impact', 'Availability Impact', impactMetrics],
                        ].map(([field, label, options]) => (
                            <div key={field} className="space-y-1">
                                <label className="block text-sm font-medium text-slate-700" htmlFor={field}>
                                    {label}
                                </label>
                                <select
                                    id={field}
                                    value={form.data[field]}
                                    onChange={(e) => form.setData(field, e.target.value)}
                                    className="w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                                >
                                    {Object.entries(options).map(([key, meta]) => (
                                        <option key={key} value={key}>
                                            {meta.label}
                                        </option>
                                    ))}
                                </select>
                                {form.errors[field] && <p className="text-xs text-rose-600">{form.errors[field]}</p>}
                            </div>
                        ))}
                    </fieldset>

                    <div>
                        <label className="block text-sm font-medium text-slate-700" htmlFor="description">
                            Description
                        </label>
                        <textarea
                            id="description"
                            rows="5"
                            value={form.data.description}
                            onChange={(e) => form.setData('description', e.target.value)}
                            className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                        />
                        {form.errors.description && <p className="mt-1 text-xs text-rose-600">{form.errors.description}</p>}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-700" htmlFor="recommendation">
                            Recommendation (optional)
                        </label>
                        <textarea
                            id="recommendation"
                            rows="4"
                            value={form.data.recommendation}
                            onChange={(e) => form.setData('recommendation', e.target.value)}
                            className="mt-1 w-full rounded border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring"
                        />
                        {form.errors.recommendation && <p className="mt-1 text-xs text-rose-600">{form.errors.recommendation}</p>}
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-700" htmlFor="attachments">
                            Attachments (optional)
                        </label>
                        <input
                            id="attachments"
                            type="file"
                            multiple
                            onChange={(e) => form.setData('attachments', e.target.files)}
                            className="mt-1 block w-full text-sm text-slate-600"
                        />
                        <p className="mt-1 text-xs text-slate-500">
                            Up to {attachmentLimit} files, max {attachmentMaxSizeMb} MB each. Allowed: {allowedExtensions.join(', ')}.
                        </p>
                        {form.errors.attachments && <p className="mt-1 text-xs text-rose-600">{form.errors.attachments}</p>}
                    </div>

                    <div className="flex items-center justify-end gap-3">
                        <Link href={backLink} className="text-sm text-slate-600 hover:text-slate-800">
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={form.processing}
                            className="rounded bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:bg-blue-400"
                        >
                            {form.processing ? 'Saving…' : 'Create Finding'}
                        </button>
                    </div>
                </form>
            )}
        </div>
    );
}
