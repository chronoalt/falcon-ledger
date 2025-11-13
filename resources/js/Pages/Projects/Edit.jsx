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
        <div className="space-y-8">
            <Head title={`Edit ${project.title}`} />

            {/* Header row */}
            <div className="flex items-center justify-between">
                <h1 className="text-3xl font-extrabold text-[#0050a4]">
                    Edit Project
                </h1>

                <Link
                    href="/projects"
                    className="text-sm font-semibold text-[#004a98] hover:underline"
                >
                    ← Back to projects
                </Link>
            </div>

            {/* Light blue wrapper */}
            <section className="rounded-2xl bg-[#c7d9f6] p-6 shadow-sm">
                <form
                    onSubmit={submit}
                    className="space-y-5 rounded-2xl bg-[#d8e4fb] p-6"
                >
                    {/* Title */}
                    <div>
                        <label
                            className="block text-sm font-medium text-[#03407c]"
                            htmlFor="title"
                        >
                            Title
                        </label>
                        <input
                            id="title"
                            type="text"
                            value={form.data.title}
                            onChange={(e) =>
                                form.setData('title', e.target.value)
                            }
                            className="mt-1 w-full rounded-md border border-white/60 bg-white/80 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#004a98] focus:outline-none focus:ring-2 focus:ring-[#004a98]/40"
                            required
                        />
                        {form.errors.title && (
                            <p className="mt-1 text-xs text-rose-600">
                                {form.errors.title}
                            </p>
                        )}
                    </div>

                    {/* Description */}
                    <div>
                        <label
                            className="block text-sm font-medium text-[#03407c]"
                            htmlFor="description"
                        >
                            Description
                        </label>
                        <textarea
                            id="description"
                            rows="4"
                            value={form.data.description ?? ''}
                            onChange={(e) =>
                                form.setData('description', e.target.value)
                            }
                            className="mt-1 w-full rounded-md border border-white/60 bg-white/80 px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-[#004a98] focus:outline-none focus:ring-2 focus:ring-[#004a98]/40"
                        />
                        {form.errors.description && (
                            <p className="mt-1 text-xs text-rose-600">
                                {form.errors.description}
                            </p>
                        )}
                    </div>

                    {/* Due date */}
                    <div className="grid gap-4 md:grid-cols-2">
                        <div>
                            <label
                                className="block text-sm font-medium text-[#03407c]"
                                htmlFor="due_at"
                            >
                                Due date
                            </label>
                            <input
                                id="due_at"
                                type="date"
                                value={form.data.due_at ?? ''}
                                onChange={(e) =>
                                    form.setData('due_at', e.target.value)
                                }
                                className="mt-1 w-full rounded-md border border-white/60 bg-white/80 px-3 py-2 text-sm text-slate-900 focus:border-[#004a98] focus:outline-none focus:ring-2 focus:ring-[#004a98]/40"
                            />
                            {form.errors.due_at && (
                                <p className="mt-1 text-xs text-rose-600">
                                    {form.errors.due_at}
                                </p>
                            )}
                        </div>

                        {/* Status */}
                        <div>
                            <label
                                className="block text-sm font-medium text-[#03407c]"
                                htmlFor="status"
                            >
                                Status
                            </label>
                            <select
                                id="status"
                                value={form.data.status}
                                onChange={(e) =>
                                    form.setData('status', e.target.value)
                                }
                                className="mt-1 w-full rounded-md border border-white/60 bg-white/80 px-3 py-2 text-sm text-slate-900 focus:border-[#004a98] focus:outline-none focus:ring-2 focus:ring-[#004a98]/40"
                            >
                                {statusOptions.map((status) => (
                                    <option key={status} value={status}>
                                        {status}
                                    </option>
                                ))}
                            </select>
                            {form.errors.status && (
                                <p className="mt-1 text-xs text-rose-600">
                                    {form.errors.status}
                                </p>
                            )}
                        </div>
                    </div>

                    {/* Actions */}
                    <div className="flex items-center justify-end gap-3 pt-2">
                        <Link
                            href="/projects"
                            className="text-sm font-semibold text-[#004a98] hover:underline"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            disabled={form.processing}
                            className="rounded-full bg-[#004a98] px-5 py-2 text-sm font-semibold text-white shadow hover:bg-[#003b77] disabled:opacity-70"
                        >
                            {form.processing ? 'Saving…' : 'Save Changes'}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    );
}
