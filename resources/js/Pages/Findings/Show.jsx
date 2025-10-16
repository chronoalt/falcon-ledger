import { Head, Link, usePage } from '@inertiajs/react';

export default function Show() {
    const { project, target, finding } = usePage().props;

    return (
        <div className="space-y-6">
            <Head title={finding.title} />

            <div className="flex items-center justify-between">
                <div>
                    <h2 className="text-3xl font-semibold text-slate-900">{finding.title}</h2>
                    <p className="text-sm text-slate-600">
                        Target: {target.label} ({target.endpoint}) • Project: {project.title}
                    </p>
                </div>
                <Link
                    href={target.links.view}
                    className="rounded border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100"
                >
                    ← Back to findings
                </Link>
            </div>

            <dl className="grid gap-4 md:grid-cols-3">
                <div className="rounded border border-slate-200 bg-white p-4 shadow-sm">
                    <dt className="text-xs font-semibold uppercase tracking-wide text-slate-500">Status</dt>
                    <dd className="mt-1 text-sm text-slate-700">{finding.status.replace('_', ' ')}</dd>
                </div>
                <div className="rounded border border-slate-200 bg-white p-4 shadow-sm">
                    <dt className="text-xs font-semibold uppercase tracking-wide text-slate-500">CVSS Score</dt>
                    <dd className="mt-1 text-sm text-slate-700">
                        {finding.cvss.score.toFixed(1)} ({finding.cvss.severity})
                    </dd>
                </div>
                <div className="rounded border border-slate-200 bg-white p-4 shadow-sm md:col-span-1">
                    <dt className="text-xs font-semibold uppercase tracking-wide text-slate-500">Vector</dt>
                    <dd className="mt-1 text-xs text-slate-600">{finding.cvss.vector}</dd>
                </div>
            </dl>

            <section className="space-y-2">
                <h3 className="text-xl font-semibold text-slate-800">Description</h3>
                <p className="rounded border border-slate-200 bg-white p-4 text-sm text-slate-700 shadow-sm">
                    {finding.description}
                </p>
            </section>

            {finding.recommendation && (
                <section className="space-y-2">
                    <h3 className="text-xl font-semibold text-slate-800">Recommendation</h3>
                    <p className="rounded border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 shadow-sm">
                        {finding.recommendation}
                    </p>
                </section>
            )}

            {finding.attachments.length > 0 && (
                <section className="space-y-2">
                    <h3 className="text-xl font-semibold text-slate-800">Attachments</h3>
                    <ul className="list-disc space-y-2 pl-5 text-sm text-slate-600">
                        {finding.attachments.map((attachment) => (
                            <li key={attachment.id}>
                                <a
                                    href={attachment.links.download}
                                    className="text-blue-600 underline-offset-2 hover:underline"
                                >
                                    {attachment.original_name ?? 'Download attachment'}
                                </a>
                                {attachment.size && (
                                    <span className="ml-2 text-xs text-slate-500">
                                        ({(attachment.size / 1024).toFixed(2)} KB)
                                    </span>
                                )}
                            </li>
                        ))}
                    </ul>
                </section>
            )}
        </div>
    );
}
