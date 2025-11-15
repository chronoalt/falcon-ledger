import { Head, Link, usePage } from '@inertiajs/react';

export default function Show() {
    const { project, target, finding } = usePage().props;

    const hasAttachments = finding.attachments && finding.attachments.length > 0;

    return (
        <div className="space-y-6">
            <Head title={finding.title} />

            <div className="rounded-2xl bg-[#c7d9f6] p-6 shadow-sm md:flex md:gap-6">
                {/* Left column – acts like the “Ledger” sidebar */}
                <aside className="mb-6 w-full max-w-xs md:mb-0">
                    <h2 className="text-2xl font-bold text-[#03407c]">Ledger</h2>
                    <p className="mt-1 text-sm text-[#03549a]">{project.title}</p>

                    <div className="mt-4 space-y-1 text-sm text-[#03407c]">
                        <p className="font-semibold">Target</p>
                        <p>{target.label}</p>
                        <p className="text-xs text-[#03549a]">{target.endpoint}</p>
                    </div>

                    <div className="mt-6 space-y-2 text-xs">
                        <Link
                            href={`/projects/${project.id}`}
                            className="text-[#004a98] hover:underline"
                        >
                            ← Back to project
                        </Link>
                        <br />
                        <Link
                            href={target.links.view}
                            className="text-[#004a98] hover:underline"
                        >
                            View all findings for this target
                        </Link>
                    </div>
                </aside>

                {/* Main notes panel */}
                <article className="flex-1 rounded-2xl bg-[#d8e4fb] p-6">
                    <header className="mb-6 text-center">
                        <p className="text-xs uppercase tracking-wide text-[#03549a]">
                            {project.title} • {target.label}
                        </p>
                        <h1 className="mt-2 text-2xl font-bold text-[#f4562c]">
                            {finding.title}
                        </h1>
                    </header>

                    <section className="space-y-6 text-sm leading-relaxed text-[#03407c]">
                        <div>
                            <h2 className="mb-1 text-base font-semibold">Description</h2>
                            <p className="whitespace-pre-wrap">
                                {finding.description}
                            </p>
                        </div>

                        <div>
                            <h2 className="mb-1 text-base font-semibold">Recommendation</h2>
                            <p className="whitespace-pre-wrap">
                                {finding.recommendation}
                            </p>
                        </div>

                        <div className="grid gap-3 text-xs md:grid-cols-3">
                            <div className="rounded-lg bg-white/70 p-3">
                                <p className="text-[11px] font-semibold uppercase text-[#03549a]">
                                    Status
                                </p>
                                <p className="mt-1 text-sm capitalize">
                                    {finding.status}
                                </p>
                            </div>

                            <div className="rounded-lg bg-white/70 p-3">
                                <p className="text-[11px] font-semibold uppercase text-[#03549a]">
                                    CVSS Score
                                </p>
                                <p className="mt-1 text-sm">
                                    {finding.cvss.score.toFixed(1)} ({finding.cvss.severity})
                                </p>
                                <p className="mt-1 text-[11px] text-[#03549a]">
                                    Vector: {finding.cvss.vector}
                                </p>
                            </div>

                            {hasAttachments && (
                                <div className="rounded-lg bg-white/70 p-3">
                                    <p className="text-[11px] font-semibold uppercase text-[#03549a]">
                                        Attachments
                                    </p>
                                    <ul className="mt-1 space-y-1">
                                        {finding.attachments.map((attachment) => (
                                            <li key={attachment.id}>
                                                <a
                                                    href={attachment.links.download}
                                                    className="text-xs font-semibold text-[#004a98] hover:underline"
                                                >
                                                    {attachment.original_name ?? 'Download attachment'}
                                                </a>
                                                {attachment.size && (
                                                    <span className="ml-1 text-[11px] text-slate-500">
                                                        ({(attachment.size / 1024).toFixed(1)} KB)
                                                    </span>
                                                )}
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            )}
                        </div>
                    </section>
                </article>
            </div>
        </div>
    );
}
