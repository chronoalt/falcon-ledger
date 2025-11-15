import { Head, Link, usePage } from '@inertiajs/react';

export default function Show() {
    const { project, target, auth } = usePage().props;
    const userRoles = auth.user ? auth.user.roles : [];

    const canAddFinding =
        userRoles.includes('admin') || userRoles.includes('supervisor') || userRoles.includes('pentester');

    return (
        <div className="space-y-6">
            <Head title={target.label} />

            <div className="flex items-center justify-between">
                <div>
                    <h2 className="text-3xl font-semibold text-slate-900">{target.label}</h2>
                    <p className="text-sm text-slate-600">
                        Endpoint: {target.endpoint}
                    </p>
                    <p className="text-xs text-slate-500">
                        Asset: {target.asset.name} • Project: {project.title}
                    </p>
                </div>
                <div className="flex gap-2">
                    <Link
                        href={project.links.show}
                        className="rounded border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-100"
                    >
                        Back to project
                    </Link>
                    {canAddFinding && (
                        <Link
                            href={target.links.createFinding}
                            className="rounded bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700"
                        >
                            Add Finding
                        </Link>
                    )}
                </div>
            </div>

            {target.description && (
                <div className="rounded border border-slate-200 bg-white p-4 text-sm text-slate-700 shadow-sm">
                    {target.description}
                </div>
            )}

            <section className="space-y-3">
                <h3 className="text-xl font-semibold text-slate-800">Findings</h3>
                {target.findings.length === 0 ? (
                    <p className="text-sm text-slate-500">No findings recorded for this target yet.</p>
                ) : (
                    <div className="space-y-3">
                        {target.findings.map((finding) => (
                            <Link
                                href={finding.links.show}
                                key={finding.id}
                                className="block rounded border border-slate-200 bg-white p-4 shadow-sm transition hover:border-blue-300 hover:bg-blue-50"
                            >
                                <div className="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                                    <div>
                                        <h4 className="text-lg font-semibold text-slate-800">{finding.title}</h4>
                                        <p className="text-sm text-slate-600">
                                            Status: {finding.status.replace('_', ' ')}
                                        </p>
                                    </div>
                                    <div className="text-sm text-slate-600">
                                        <span className="font-semibold">CVSS:</span>{' '}
                                        {finding.cvss.score.toFixed(1)} ({finding.cvss.severity})
                                    </div>
                                </div>
                                <p className="mt-2 text-xs text-slate-500">Vector: {finding.cvss.vector}</p>
                                <p className="mt-1 text-xs text-slate-400">Updated {finding.updated_human}</p>
                            </Link>
                        ))}
                    </div>
                )}
            </section>
        </div>
    );
}
