import { Head, Link, usePage, router } from '@inertiajs/react';

export default function Index() {
    const { projects, auth } = usePage().props;
    const userRoles = auth.user ? auth.user.roles : [];

    const canPerformCrud = userRoles.includes('admin') || userRoles.includes('supervisor');

    const handleDelete = (project) => {
        if (!confirm(`Delete project "${project.title}"? This cannot be undone.`)) {
            return;
        }

        router.delete(project.links.destroy);
    };

    return (
        <div className="space-y-10">
            <Head title="Projects" />

            {/* Header row */}
            <div className="flex items-center justify-between">
                <h1 className="text-3xl font-extrabold text-[#0050a4]">
                    Projects
                </h1>

                {canPerformCrud && (
                    <Link
                        href="/projects/create"
                        className="rounded-full bg-[#004a98] px-5 py-2 text-sm font-semibold text-white shadow hover:bg-[#003b77] transition"
                    >
                        Create Project
                    </Link>
                )}
            </div>

            {/* Wrapper */}
            <section className="rounded-2xl bg-[#c7d9f6] p-6 shadow-sm">
                {projects.length === 0 && (
                    <p className="text-sm text-[#03407c]">
                        No projects found.
                        {canPerformCrud && " Create your first project!"}
                    </p>
                )}

                <div className="mt-2 space-y-3">
                    {projects.map((project) => (
                        <div
                            key={project.id}
                            className="flex flex-col gap-3 rounded-2xl bg-[#d8e4fb] p-4 md:flex-row md:items-center md:justify-between"
                        >
                            {/* Left: title + description + meta */}
                            <div className="space-y-1">
                                <h2 className="text-base font-semibold text-[#f4562c]">
                                    {project.title}
                                </h2>

                                <p className="text-sm text-[#03407c] line-clamp-2">
                                    {project.description || 'No description provided.'}
                                </p>

                                <div className="mt-1 flex flex-wrap items-center gap-3 text-xs text-[#03549a]">

                                    {project.status && (
                                        <span
                                            className={`
                                                rounded-full px-3 py-1 text-xs font-medium
                                                ${
                                                    project.status === 'Active'
                                                        ? 'bg-[#ffe0c2] text-[#b84a14]'        // light orange
                                                        : project.status === 'Completed'
                                                        ? 'bg-[#d6f5d6] text-[#247a24]'        // green
                                                        : 'bg-[#e5e7eb] text-[#374151]'        // gray for Inactive/others
                                                }
                                            `}
                                        >
                                            {project.status}
                                        </span>
                                    )}

                                    {project.due_at && (
                                        <span>Due: {project.due_at}</span>
                                    )}
                                </div>
                            </div>

                            {/* Right: actions */}
                            <div className="flex flex-wrap items-center gap-2 md:justify-end">
                                <Link
                                    href={project.links.show}
                                    className="rounded-full bg-[#004a98] px-4 py-1.5 text-xs font-semibold text-white shadow hover:bg-[#003b77]"
                                >
                                    View
                                </Link>

                                {canPerformCrud && (
                                    <>
                                        <Link
                                            href={project.links.edit}
                                            className="rounded-full border border-[#004a98] px-4 py-1.5 text-xs font-semibold text-[#004a98] hover:bg-[#004a98] hover:text-white"
                                        >
                                            Edit
                                        </Link>

                                        <button
                                            type="button"
                                            onClick={() => handleDelete(project)}
                                            className="rounded-full border border-rose-400 px-4 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-600 hover:text-white"
                                        >
                                            Delete
                                        </button>
                                    </>
                                )}
                            </div>
                        </div>
                    ))}
                </div>
            </section>
        </div>
    );
}
